<?php

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

new STM_LMS_User_Assignment();

class STM_LMS_User_Assignment {

	public function __construct() {
		add_action( 'wp_ajax_stm_lms_get_enrolled_assignments', array( $this, 'enrolled_assignments' ) );

		add_filter( 'stm_lms_course_passed_items', array( $this, 'essay_passed' ), 10, 3 );

		add_filter(
			'stm_lms_menu_items',
			function ( $menus ) {
				$current_slug = masterstudy_get_current_account_slug();

				$menus[] = array(
					'order'        => 135,
					'id'           => 'my_assignments',
					'slug'         => 'enrolled-assignments',
					'lms_template' => 'stm-lms-enrolled-assignments',
					'menu_title'   => esc_html__( 'My Assignments', 'masterstudy-lms-learning-management-system-pro' ),
					'menu_icon'    => 'stmlms-menu-assignments',
					'menu_url'     => ms_plugin_user_account_url( 'enrolled-assignments' ),
					'badge_count'  => STM_LMS_User_Assignment::my_assignments_statuses( get_current_user_id() ),
					'menu_place'   => 'learning',
					'is_active'    => 'enrolled-assignments' === $current_slug,
					'section'      => 'main',
				);

				return $menus;
			}
		);

		$this->init_hooks();
	}

	public function init_hooks() {
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );

		// Schedule cron job on activation
		$this->schedule_expiration_check();

		// Handle cron job
		add_action( 'ms_lms_check_expired_assignments', array( $this, 'check_expired_assignments' ) );
	}

	public function add_cron_schedules( $schedules ) {
		if ( ! isset( $schedules['ms_lms_every_12_hours'] ) ) {
			$schedules['ms_lms_every_12_hours'] = array(
				'interval' => 12 * HOUR_IN_SECONDS,
				'display'  => __( 'Every 12 Hours', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		return $schedules;
	}

	public function schedule_expiration_check() {
		$event = wp_get_scheduled_event( 'ms_lms_check_expired_assignments' );

		if ( ! $event ) {
			wp_schedule_event( time(), 'ms_lms_every_12_hours', 'ms_lms_check_expired_assignments' );
			return;
		}

		if ( 'ms_lms_every_12_hours' !== $event->schedule ) {
			wp_clear_scheduled_hook( 'ms_lms_check_expired_assignments' );
			wp_schedule_event( time(), 'ms_lms_every_12_hours', 'ms_lms_check_expired_assignments' );
		}
	}

	public function check_expired_assignments() {
		if ( ! function_exists( 'stm_lms_user_assignments_times_name' ) ) {
			return;
		}

		global $wpdb;
		$user_assignments_times_table = stm_lms_user_assignments_times_name( $wpdb );
		$user_assignments_table       = stm_lms_user_assignments_name( $wpdb );
		$batch_size                   = 20;

		$now = current_time( 'mysql' );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT at.user_assignment_time_id, at.user_id, at.assignment_id, at.end_time, ua.user_assignment_id, ua.status, ua.course_id
				FROM $user_assignments_times_table AS at
				INNER JOIN $user_assignments_table AS ua ON ua.user_assignment_id = at.assignment_id
				WHERE at.end_time < %s AND ua.status = 'draft'
				LIMIT %d
			",
				$now,
				$batch_size
			),
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( $results as $row ) {
			STM_LMS_Assignments::decline_assignment( $row['user_assignment_id'], $row['course_id'], $row['user_id'] );
		}
	}

	public static function is_my_assignment( $assignment_id, $author_id ) {
		$editor_id = intval( get_post_field( 'post_author', get_post_meta( $assignment_id, 'assignment_id', true ) ) );
		return $editor_id === $author_id;
	}

	public static function get_assignment( $assignment_id ) {
		$editor_id = STM_LMS_User::get_current_user();

		if ( empty( $editor_id ) ) {
			$answer = array(
				'message' => 'Failed',
			);
			return $answer;
		}
		$editor_id = $editor_id['id'];

		if ( ! self::is_my_assignment( $assignment_id, $editor_id ) ) {
			STM_LMS_User::js_redirect( ms_plugin_user_account_url( 'assignments' ) );
			$answer = array(
				'message' => 'Failed',
			);
			return $answer;
		}

		$args = array(
			'post_type'   => 'stm-user-assignment',
			'post_status' => array( 'pending', 'publish' ),
			'post__in'    => array( $assignment_id ),
		);

		$q = new WP_Query( $args );

		$answer = array();

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();

				$answer['title']            = get_the_title();
				$answer['status']           = ( new AssignmentStudentRepository() )->get_status( $assignment_id );
				$answer['content']          = get_the_content();
				$answer['assignment_title'] = get_the_title( get_post_meta( $assignment_id, 'assignment_id', true ) );

				$answer['files'] = STM_LMS_Assignments::get_draft_attachments( $assignment_id, 'student_attachments' );
			}
		}

		wp_reset_postdata();

		return $answer;
	}

	public function essay_passed( $passed_items, $course_materials, $user_id ) {
		foreach ( $course_materials as $material_id ) {
			if ( get_post_type( $material_id ) !== PostType::ASSIGNMENT ) {
				continue;
			}

			if ( ( new AssignmentStudentRepository() )->has_passed_assignment( $material_id, $user_id ) ) {
				++$passed_items;
			}
		}

		return $passed_items;
	}

	public static function my_assignments( $user_id, $page = null, $per_page = 10 ) {
		$student_repository = new AssignmentStudentRepository();
		$grade_calculator   = GradeCalculator::get_instance();
		$args               = array(
			'post_type'      => PostType::USER_ASSIGNMENT,
			'posts_per_page' => $per_page,
			'offset'         => ( $page * $per_page ) - $per_page,
			'post_status'    => array( 'pending', 'publish' ),
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'student_id',
					'value'   => $user_id,
					'compare' => '=',
				),
			),
		);

		if ( ! empty( $_GET['status'] ) && 'undefined' !== $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$args['meta_query'][] = array(
				'key'     => 'status',
				'value'   => sanitize_text_field( $_GET['status'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'compare' => '=',
			);
		}

		if ( ! empty( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$args['s'] = sanitize_text_field( $_GET['s'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$q = new WP_Query( $args );

		$posts = array();
		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				$id            = get_the_ID();
				$course_id     = get_post_meta( $id, 'course_id', true );
				$assignment_id = get_post_meta( $id, 'assignment_id', true );
				$who_view      = get_post_meta( $id, 'who_view', true );
				$status        = $student_repository->get_status( $id );

				if ( is_ms_lms_addon_enabled( 'grades' ) ) {
					$grade = $student_repository->get_grade( $id );

					$grade_data = null;

					if ( 'pending' !== $status && ! empty( $grade ) ) {
						$grade_data            = $grade_calculator->calculate( $grade );
						$grade_data['max']     = $grade_calculator->calculate( $grade_calculator->get_max_range() );
						$grade_data['percent'] = $grade;
					}
				}

				$posts[] = array(
					'assignment_title' => get_the_title( $assignment_id ),
					'course_title'     => get_the_title( $course_id ),
					'updated_at'       => stm_lms_time_elapsed_string( gmdate( 'Y-m-d H:i:s', get_post_timestamp() ) ),
					'status'           => self::statuses( $status ),
					'grade'            => $grade_data ?? null,
					'instructor'       => STM_LMS_User::get_current_user( get_post_field( 'post_author', $course_id ) ),
					'url'              => STM_LMS_Lesson::get_lesson_url( $course_id, $assignment_id ),
					'who_view'         => $who_view,
					'pages'            => ceil( $q->found_posts / $per_page ),
				);

			}
		}
		return $posts;
	}

	public static function my_assignments_statuses( $user_id ) {
		$args = array(
			'post_type'      => 'stm-user-assignment',
			'posts_per_page' => 1,
			'post_status'    => array( 'publish' ),
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'student_id',
					'value'   => $user_id,
					'compare' => '=',
				),
				array(
					'key'     => 'who_view',
					'value'   => 0,
					'compare' => '=',
				),
			),
		);

		$q = new WP_Query( $args );

		return $q->found_posts;
	}

	public static function statuses( $status ) {
		$status_labels = array(
			'pending'    => esc_html__( 'Pending...', 'masterstudy-lms-learning-management-system-pro' ),
			'draft'      => esc_html__( 'Draft', 'masterstudy-lms-learning-management-system-pro' ),
			'passed'     => esc_html__( 'Approved', 'masterstudy-lms-learning-management-system-pro' ),
			'not_passed' => esc_html__( 'Declined', 'masterstudy-lms-learning-management-system-pro' ),
		);

		if ( array_key_exists( $status, $status_labels ) ) {
			return array(
				'status' => $status,
				'label'  => $status_labels[ $status ],
			);
		}
	}

	public function enrolled_assignments() {
		check_ajax_referer( 'stm_lms_get_enrolled_assingments', 'nonce' );
		$page     = intval( $_GET['page'] );
		$per_page = intval( $_GET['per_page'] ?? '10' );
		$user     = STM_LMS_User::get_current_user();
		$items    = self::my_assignments( $user['id'], $page, $per_page );
		$data     = array(
			'assignments' => $items,
			'pagination'  => STM_LMS_Templates::load_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 3,
					'total_pages'       => 10,
					'current_page'      => $page,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_hidden'         => false,
				)
			),
		);
		wp_send_json( $data );
	}

	/**
	 * Clean up cron jobs on deactivation
	 */
	public static function cleanup_cron_jobs(): void {
		wp_clear_scheduled_hook( 'ms_lms_check_expired_assignments' );
	}
}

// Clean up on deactivation
register_deactivation_hook( __FILE__, array( STM_LMS_User_Assignment::class, 'cleanup_cron_jobs' ) );
