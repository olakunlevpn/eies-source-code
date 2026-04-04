<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Assignments;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeDisplay;

final class AssignmentStudentRepository {
	const ATTACHMENT_META   = 'student_attachments';
	const STATUS_PASSED     = 'passed';
	const STATUS_NOT_PASSED = 'not_passed';
	const STATUS_PENDING    = 'pending';
	const STATUS_DRAFT      = 'draft';

	private $table_name;

	public function __construct() {
		global $wpdb;

		$this->table_name = stm_lms_user_assignments_name( $wpdb );
	}

	public function get_assignments( $params = array() ) {
		$query_params = array(
			'post_type'      => PostType::USER_ASSIGNMENT,
			'posts_per_page' => intval( $params['per_page'] ?? 10 ),
			'post_status'    => ! empty( $params['post_status'] ) ? $params['post_status'] : array(
				'pending',
				'publish',
			),
			'meta_query'     => array(
				array(
					'key'   => 'assignment_id',
					'value' => $params['assignment_id'],
				),
			),
		);

		if ( ! empty( $params['page'] ) ) {
			$query_params['paged'] = $params['page'];
		}

		if ( ! empty( $params['s'] ) ) {
			$query_params['s'] = sanitize_text_field( $params['s'] );
		}

		if ( ! empty( $params['status'] ) ) {
			if ( 'pending' === $params['status'] ) {
				$query_params['post_status'] = array( 'pending' );
			} else {
				$query_params['meta_query']['relation'] = 'AND';
				$query_params['meta_query'][]           = array(
					'key'   => 'status',
					'value' => sanitize_text_field( $params['status'] ),
				);
			}
		}
		// sorting data.
		if ( ! empty( $params['sortby'] ) && ! empty( $params['sort_order'] ) ) {
			if ( 'date' === $params['sortby'] ) {
				$query_params['orderby'] = $params['sortby'];
				$query_params['order']   = $params['sort_order'];
			} else {
				$query_params['meta_query'][] = array(
					'sorting_clause' => array(
						'key' => $params['sortby'],
					),
				);

				$query_params['orderby'] = array(
					'sorting_clause' => strtoupper( $params['sort_order'] ),
				);
			}
		}

		if ( ! empty( $params['student_id'] ) && empty( $params['status'] ) ) {
			$query_params['meta_query']['relation'] = 'AND';
			$query_params['meta_query'][]           = array(
				'key'   => 'student_id',
				'value' => intval( $params['student_id'] ),
			);
		}

		$query = new \WP_Query( $query_params );

		if ( ! empty( $params['return_query'] ) ) {
			return $query;
		}

		$assignments = array(
			'assignments' => array(),
			'page'        => $params['page'],
			'found_posts' => $query->found_posts,
			'per_page'    => $params['per_page'],
			'max_pages'   => $query->max_num_pages,
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id        = get_the_ID();
				$status    = $this->get_status( $id );
				$userdata  = get_userdata( get_post_meta( $id, 'student_id', true ) );
				$course_id = get_post_meta( $id, 'course_id', true );

				$student_assignment = array(
					'id'          => $id,
					'title'       => str_replace( array( '&#8220;', '&#8221;' ), '"', get_the_title() ),
					'student'     => array(
						'first_name'   => $userdata->first_name,
						'last_name'    => $userdata->last_name,
						'email'        => $userdata->user_email,
						'display_name' => $userdata->display_name,
					),
					'course'      => array(
						'id'    => $course_id,
						'title' => get_the_title( $course_id ),
						'link'  => get_permalink( $course_id ),
					),
					'date'        => get_the_date( 'd.m.Y' ),
					'try_num'     => get_post_meta( $id, 'try_num', true ),
					'status'      => array(
						'slug'  => $status,
						'title' => $this->get_status_html( $status, false ),
					),
					'review_link' => ms_plugin_user_account_url( "user-assignment/$id" ),
				);

				if ( is_ms_lms_addon_enabled( 'grades' ) ) {
					$grade                       = $this->get_grade( $id );
					$student_assignment['grade'] = array(
						'value' => $grade,
						'html'  => GradeDisplay::get_instance()->detailed_render( $grade ),
					);
				}

				$assignments['assignments'][] = $student_assignment;
			}
		}

		return $assignments;
	}

	public static function enclose_attachment( int $assignment_id, int $attachment_id ): void {
		$attachments   = get_post_meta( $assignment_id, self::ATTACHMENT_META, true );
		$attachments   = ! empty( $attachments ) ? $attachments : array();
		$attachments[] = $attachment_id;

		update_post_meta( $assignment_id, self::ATTACHMENT_META, array_unique( $attachments ) );
	}

	public function get_display_name( int $assignment_id ): string {
		$student_id   = get_post_meta( $assignment_id, 'student_id', true );
		$student      = get_userdata( $student_id );
		$display_name = \STM_LMS_User::display_name( $student );

		return $display_name ?? '';
	}

	public function add_assignment( $student_id, $course_id, $assignment_id, $user_assignment_id, $status, $grade = null ) {
		global $wpdb;

		$wpdb->insert(
			$this->table_name,
			array(
				'user_id'            => $student_id,
				'course_id'          => $course_id,
				'assignment_id'      => $assignment_id,
				'user_assignment_id' => $user_assignment_id,
				'status'             => $status,
				'grade'              => $grade,
				'updated_at'         => time(),
			),
			array( '%d', '%d', '%d', '%d', '%s', '%d' )
		);

		return $wpdb->insert_id;
	}

	public function get_old_status( int $user_assignment_id ): string {
		$status = get_post_status( $user_assignment_id );

		if ( ! in_array( $status, array( 'draft', 'pending' ), true ) ) {
			$status = get_post_meta( $user_assignment_id, 'status', true );
		}

		return $status ?? 'pending';
	}

	public static function get_passing_grade( int $user_assignment_id ): int {
		$assignment_id = get_post_meta( $user_assignment_id, 'assignment_id', true );
		$passing_grade = get_post_meta( $assignment_id, 'passing_grade', true );

		return (int) $passing_grade;
	}

	public function get_grade( int $user_assignment_id ): ?int {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT grade FROM $this->table_name WHERE user_assignment_id = %d",
				$user_assignment_id
			)
		);
	}

	public function update_grade( int $user_assignment_id, $grade ): void {
		global $wpdb;

		$wpdb->update(
			$this->table_name,
			array(
				'grade'      => $grade,
				'updated_at' => time(),
			),
			array(
				'user_assignment_id' => $user_assignment_id,
			),
			array( '%s', '%d' ),
			array( '%d' )
		);
	}

	public function update_status( int $user_assignment_id, string $status ): void {
		global $wpdb;

		// Update Assignment Status Post Meta for faster queries
		update_post_meta( $user_assignment_id, 'status', $status );

		$data         = array(
			'status'     => $status,
			'updated_at' => time(),
		);
		$placeholders = array(
			'%s',
			'%d',
		);

		// Update Assignment Grade if Grades Addon is enabled
		if ( ! is_ms_lms_addon_enabled( 'grades' ) ) {
			if ( self::STATUS_PASSED === $status ) {
				$data['grade']  = 100;
				$placeholders[] = '%d';
			} elseif ( self::STATUS_NOT_PASSED === $status ) {
				$data['grade']  = 0;
				$placeholders[] = '%d';
			}
		}

		$wpdb->update(
			$this->table_name,
			$data,
			array(
				'user_assignment_id' => $user_assignment_id,
			),
			$placeholders,
			array( '%d' )
		);
	}

	public function get_status( $user_assignment_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT status FROM $this->table_name WHERE user_assignment_id = %d ORDER BY updated_at DESC LIMIT 1",
				$user_assignment_id
			)
		);
	}

	public function get_status_html( $status, $show_icon = true ): string {
		if ( empty( $status ) ) {
			return '';
		}

		$statuses = Assignments::statuses();

		$status_title = $statuses[ $status ]['title'] ?? '';

		if ( $show_icon ) {
			return "{$statuses[ $status ]['icon']} $status_title";
		}

		return $status_title;
	}

	public function get_user_assignment_status_html( $user_assignment_id ) {
		$status = $this->get_status( $user_assignment_id );

		return $this->get_status_html( $status );
	}

	public function count_by_status( string $status ): ?int {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM $this->table_name WHERE status = %s",
				$status
			)
		);
	}

	public function get_last_attempt( $course_id, $assignment_id, $student_id, $user_assignment_id = null ) {
		global $wpdb;
		$condition = '';

		if ( $user_assignment_id ) {
			$condition .= $wpdb->prepare( ' AND user_assignment_id != %d', $user_assignment_id );
		}

		return $wpdb->get_row(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT user_assignment_id, grade, status FROM $this->table_name WHERE course_id = %d AND assignment_id = %d AND user_id = %d $condition ORDER BY updated_at DESC LIMIT 1",
				$course_id,
				$assignment_id,
				$student_id
			),
			ARRAY_A
		);
	}

	public function get_attempts_count( $course_id, $assignment_id, $student_id, $status = '' ) {
		global $wpdb;

		$status_query = '';
		if ( ! empty( $status ) ) {
			$status_query = $wpdb->prepare(
				'AND status = %s',
				$status
			);
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM $this->table_name WHERE course_id = %d AND assignment_id = %d AND user_id = %d {$status_query}",
				$course_id,
				$assignment_id,
				$student_id
			)
		);
	}

	public function has_passed_assignment( $assignment_id, $student_id, $course_id = null ) {
		global $wpdb;

		$course_query = '';
		if ( ! empty( $course_id ) ) {
			$course_query = $wpdb->prepare(
				'AND course_id = %d',
				$course_id
			);
		}

		$last_attempt = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT status FROM $this->table_name WHERE assignment_id = %d AND user_id = %d $course_query ORDER BY id DESC LIMIT 1",
				$assignment_id,
				$student_id
			),
			ARRAY_A
		);

		return self::STATUS_PASSED === ( $last_attempt['status'] ?? '' );
	}

	public static function get_all_students() {
		global $wpdb;

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} AS pm INNER JOIN {$wpdb->posts} AS p 
                ON pm.post_id = p.ID WHERE pm.meta_key = 'student_id' AND CAST(pm.meta_value AS SIGNED) > 0 AND p.post_type = %s",
				PostType::USER_ASSIGNMENT
			)
		);
	}

	public function get_students_passed_assignments_count( $course_id, $student_ids = array() ) {
		global $wpdb;

		$students_query = '';

		if ( ! empty( $student_ids ) ) {
			$placeholders   = implode( ',', array_fill( 0, count( $student_ids ), '%d' ) );
			$students_query = $wpdb->prepare(
			// phpcs:ignore WordPress.DB
				"AND user_id IN ($placeholders)",
				...$student_ids
			);
		}

		return $wpdb->get_results(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT user_id, COUNT(*) as 'count' FROM $this->table_name WHERE course_id = %d $students_query AND status = %s GROUP BY user_id",
				$course_id,
				self::STATUS_PASSED
			),
			ARRAY_A
		);
	}

	public function get_average_passed_assignments( $course_id, $total_assignments, $total_students ) {
		global $wpdb;

		if ( $total_assignments < 1 || $total_students < 1 ) {
			return 0;
		}

		$passed_assignments = $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM $this->table_name WHERE course_id = %d AND status = %s",
				$course_id,
				self::STATUS_PASSED
			)
		);

		return round( ( $passed_assignments / ( $total_assignments * $total_students ) ) * 100, 2 );
	}

	public function is_assignment_draft( $assignment_id, $student_id ) {
		global $wpdb;

		$result = $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT status FROM $this->table_name WHERE assignment_id = %d AND user_id = %d ORDER BY updated_at DESC LIMIT 1",
				$assignment_id,
				$student_id
			)
		);

		return ! empty( $result ) && self::STATUS_DRAFT === $result;
	}
}
