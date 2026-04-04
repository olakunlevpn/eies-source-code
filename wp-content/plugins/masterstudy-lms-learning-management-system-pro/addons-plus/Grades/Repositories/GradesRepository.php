<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\RestApi\Repositories\DataTable\DataTableAbstractRepository;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Utility\CourseGrade;

class GradesRepository extends DataTableAbstractRepository {
	private string $user_courses_table;
	private string $user_quizzes_table;
	private string $user_assignments_table;
	private array $search_values = array();

	public function __construct( string $date_from = '', string $date_to = '', int $start = 0, int $limit = 10, $search_values = array() ) {
		if ( ! empty( $search_values ) ) {
			$this->search_values = $search_values;
		}

		if ( empty( $date_from ) && empty( $date_to ) ) {
			$date_from = gmdate( 'Y-m-d H:i:s', strtotime( '-1 month' ) );
			$date_to   = gmdate( 'Y-m-d H:i:s' );
		}

		parent::__construct( $date_from, $date_to, $start, $limit );

		$this->user_courses_table     = stm_lms_user_courses_name( $this->db );
		$this->user_quizzes_table     = stm_lms_user_quizzes_name( $this->db );
		$this->user_assignments_table = stm_lms_user_assignments_name( $this->db );
	}

	public function get_grades( array $columns, array $order ) {
		$this->apply_sort( $order, $columns, 'start_time', 'desc' );

		$this->select = array(
			'user_courses.user_course_id',
			'user_courses.user_id',
			'user_courses.course_id',
			'user_courses.start_time',
			'user_courses.final_grade',
			'courses.post_title AS course_title',
			'users.user_email',
			'users.display_name',
			'pm.meta_value AS featured_image_id',
			'quizzes.passed_quizzes',
		);
		$search_query = $this->get_search_query();
		$extra_query  = $this->get_extra_query();

		// Select aggregate tables
		$this->get_query();

		if ( is_ms_lms_addon_enabled( 'assignments' ) ) {
			$this->select[] = 'assignments.passed_assignments';
		}

		$sql = 'SELECT ' . implode( ',', $this->select ) . " FROM {$this->user_courses_table} AS user_courses
		JOIN (
			SELECT user_id, course_id, MAX(start_time) AS latest_start_time
			FROM $this->user_courses_table WHERE is_gradable = 1
			GROUP BY user_id, course_id
		) AS latest ON user_courses.user_id = latest.user_id
		AND user_courses.course_id = latest.course_id AND user_courses.start_time = latest.latest_start_time $search_query $extra_query
		AND user_courses.start_time BETWEEN %s AND %s
		LEFT JOIN {$this->db->posts} AS courses ON user_courses.course_id = courses.ID
		LEFT JOIN {$this->db->postmeta} AS pm ON user_courses.course_id = pm.post_id AND pm.meta_key = '_thumbnail_id'
		LEFT JOIN {$this->db->users} AS users ON user_courses.user_id = users.ID
		LEFT JOIN (
			SELECT user_id, course_id, COUNT(*) AS passed_quizzes
			FROM {$this->user_quizzes_table}
			WHERE status = 'passed'
			GROUP BY user_id, course_id
		) AS quizzes ON user_courses.user_id = quizzes.user_id AND user_courses.course_id = quizzes.course_id ";

		if ( is_ms_lms_addon_enabled( 'assignments' ) ) {
			$sql .= "LEFT JOIN (
				SELECT user_id, course_id, COUNT(*) AS passed_assignments
				FROM {$this->user_assignments_table}
				WHERE status = 'passed'
				GROUP BY user_id, course_id
			) AS assignments ON user_courses.user_id = assignments.user_id AND user_courses.course_id = assignments.course_id ";
		}

		$sql .= $this->pagination_query();

		return $this->db->get_results(
			$this->db->prepare(
				$sql,
				$this->get_timestamp( $this->date_from ),
				$this->get_timestamp( $this->date_to )
			),
			ARRAY_A
		);
	}

	public function get_total_grades() {
		$search_query = $this->get_search_query();
		$extra_query  = $this->get_extra_query();

		$sql = "SELECT COUNT(*) FROM {$this->user_courses_table} AS user_courses
		JOIN (
			SELECT user_id, course_id, MAX(start_time) AS latest_start_time
			FROM $this->user_courses_table WHERE is_gradable = 1
			GROUP BY user_id, course_id
		) AS latest ON user_courses.user_id = latest.user_id
		AND user_courses.course_id = latest.course_id AND user_courses.start_time = latest.latest_start_time $search_query $extra_query
		AND user_courses.start_time BETWEEN %s AND %s";

		return $this->db->get_var(
			$this->db->prepare(
				$sql,
				$this->get_timestamp( $this->date_from ),
				$this->get_timestamp( $this->date_to )
			)
		);
	}

	public function get_user_course_grade( int $user_course_id ) {
		$user_course = $this->db->get_row(
			$this->db->prepare(
				"SELECT user_courses.*, courses.post_title AS course_title, users.display_name
				FROM {$this->user_courses_table} as user_courses
				LEFT JOIN {$this->db->posts} AS courses ON user_courses.course_id = courses.ID
				LEFT JOIN {$this->db->users} AS users ON user_courses.user_id = users.ID
				WHERE user_courses.user_course_id = %d",
				$user_course_id
			),
			ARRAY_A
		);

		$course_id            = $user_course['course_id'];
		$user_id              = $user_course['user_id'];
		$assignments_repo     = new AssignmentStudentRepository();
		$course_materials     = ( new CurriculumMaterialRepository() )->get_course_materials( $course_id, false );
		$user_course['exams'] = array();

		if ( ! empty( $course_materials ) ) {
			foreach ( $course_materials as $course_material ) {
				if ( ! in_array( $course_material['post_type'], array( PostType::ASSIGNMENT, PostType::QUIZ ), true ) ) {
					continue;
				}

				$post_id = $course_material['post_id'];
				$exam    = array(
					'type'     => $course_material['post_type'],
					'title'    => get_the_title( $post_id ),
					'attempts' => 0,
					'grade'    => 0,
				);

				if ( PostType::QUIZ === $course_material['post_type'] ) {
					$quizzes = $this->db->get_results(
						$this->db->prepare(
							"SELECT progress FROM {$this->user_quizzes_table} WHERE user_id = %d AND quiz_id = %d",
							$user_id,
							$post_id
						),
						ARRAY_A
					);

					if ( ! empty( $quizzes ) ) {
						$exam['attempts'] = count( $quizzes );
						$last_attempt     = end( $quizzes );
						$exam['grade']    = $last_attempt['progress'];
					}
				} elseif ( PostType::ASSIGNMENT === $course_material['post_type'] ) {
					$last_attempt = $assignments_repo->get_last_attempt( $course_id, $post_id, $user_id );

					if ( ! empty( $last_attempt ) && in_array( $last_attempt['status'], array( $assignments_repo::STATUS_PASSED, $assignments_repo::STATUS_NOT_PASSED ), true ) ) {
						$exam['attempts'] = $assignments_repo->get_attempts_count( $course_id, $post_id, $user_id );
						$exam['grade']    = $last_attempt['grade'] ?? 0;
					}
				}

				$user_course['exams'][] = $exam;
			}
		}

		return $user_course;
	}

	public function regenerate_user_course_grade( int $user_course_id ) {
		$user_course = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM {$this->user_courses_table} WHERE user_course_id = %d",
				$user_course_id
			),
			ARRAY_A
		);

		if ( ! $user_course ) {
			return;
		}

		CourseGrade::update_user_course_grade(
			(int) $user_course['user_id'],
			(int) $user_course['course_id'],
			$user_course_id
		);
	}

	private function get_search_query() {
		$search_query = '';

		if ( ! empty( $this->search_values ) ) {
			foreach ( $this->search_values as $search_value ) {
				$search_query .= $this->db->prepare(
					" AND user_courses.{$search_value['column']} = %d ",
					intval( $search_value['value'] )
				);
			}
		}

		return $search_query;
	}

	protected function get_extra_query() {
		$extra_query = '';

		if ( ! current_user_can( 'administrator' ) ) {
			$instructor_course_ids = $this->db->get_col(
				$this->db->prepare(
					"SELECT ID FROM {$this->db->posts} WHERE post_type = %s AND post_author = %d",
					PostType::COURSE,
					get_current_user_id()
				)
			);

			if ( ! empty( $instructor_course_ids ) ) {
				$ids_placeholders = implode( ',', array_fill( 0, count( $instructor_course_ids ), '%d' ) );
				$extra_queryv     = $this->db->prepare(
					" AND user_courses.course_id IN ($ids_placeholders) ",
					...$instructor_course_ids
				);
			} else {
				// If instructor has no courses, return empty result
				$extra_query = ' AND 1 = 0 ';
			}
		}

		return $extra_query;
	}
}
