<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories\DataTable;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Repositories\CurriculumRepository;
use STM_LMS_Lesson;
use STM_LMS_Quiz;

class StudentCoursesRepository extends DataTableAbstractRepository {

	public function get_student_course_progress_data( $student_id, $columns = array(), $order = array() ): array {
		$this->apply_sort( $order, $columns, 'name' );

		$instructor_join = '';
		if ( $this->is_current_user_instructor() ) {
			$instructor_join = $this->db->prepare(
				"INNER JOIN {$this->db->posts} c ON c.post_type = 'stm-courses' AND c.post_status IN ('publish') AND c.post_author = %d AND uc.course_id = c.ID",
				$this->current_instructor_id
			);
		}

		$query = "SELECT 
                DISTINCT p.ID AS course_id,
                p.post_title AS name,
                uc.start_time AS started,
                uc.end_time AS ended,
                uc.progress_percent AS progress
            FROM {$this->db->prefix}stm_lms_user_courses uc
            INNER JOIN {$this->db->posts} p ON uc.course_id = p.ID
            {$instructor_join}
            WHERE uc.user_id = %d
            GROUP BY p.ID, p.post_title, uc.start_time, uc.progress_percent
            ORDER BY {$this->sort_by} {$this->sort_dir}
            LIMIT {$this->limit} OFFSET {$this->start}";

		$results = $this->db->get_results( $this->db->prepare( $query, $student_id ), ARRAY_A );

		if ( ! empty( $results ) ) {
			$curriculum_repo    = new CurriculumRepository();
			$assignment_repo    = new AssignmentStudentRepository();
			$assignment_enabled = is_ms_lms_addon_enabled( 'assignments' );
			$referer            = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
			$is_admin           = strpos( $referer, '/wp-admin/' ) !== false;

			foreach ( $results as &$result ) {
				$course_id         = $result['course_id'];
				$completed_lessons = STM_LMS_Lesson::get_completed_lessons( $student_id, $course_id );
				$passed_quizzes    = STM_LMS_Quiz::get_passed_quizzes( $student_id, $course_id );
				$curriculum        = $curriculum_repo->get_curriculum( $course_id );

				$lesson_completed_count     = 0;
				$quiz_completed_count       = 0;
				$assignment_completed_count = 0;
				$lesson_count               = 0;
				$quiz_count                 = 0;
				$assignment_count           = 0;

				if ( ! empty( $curriculum['materials'] ) ) {
					foreach ( $curriculum['materials'] as $section_material ) {
						$post_type = $section_material['post_type'];
						$post_id   = $section_material['post_id'];

						if ( PostType::QUIZ === $post_type ) {
							if ( isset( $passed_quizzes[ $post_id ] ) ) {
								$quiz_completed_count++;
							}
						} elseif ( PostType::ASSIGNMENT === $post_type && $assignment_enabled ) {
							if ( $assignment_repo->has_passed_assignment( $post_id, $student_id ) ) {
								$assignment_completed_count++;
							}
						} else {
							if ( isset( $completed_lessons[ $post_id ] ) ) {
								$lesson_completed_count++;
							}
						}

						if ( ! isset( $section_material['lesson_completed'] ) ) {
							if ( PostType::QUIZ === $post_type ) {
								$quiz_count++;
							} elseif ( PostType::ASSIGNMENT === $post_type && $assignment_enabled ) {
								$assignment_count++;
							} else {
								$lesson_count++;
							}
						}
					}
				}

				$result['quizzes'] = $quiz_count ? $quiz_completed_count . '/' . $quiz_count : '-';
				$result['lessons'] = $lesson_count ? $lesson_completed_count . '/' . $lesson_count : '-';
				if ( $assignment_enabled ) {
					$result['assignments'] = $assignment_count ? $assignment_completed_count . '/' . $assignment_count : '-';
				}
				$result['url'] = $is_admin ? admin_url( "?page=stm-lms-dashboard#/course/$course_id/$student_id" ) : ms_plugin_user_account_url( "enrolled-students-progress/$student_id/$course_id" );
			}
		}

		return $results;
	}

	public function get_student_membership_data( $student_id, $columns = array(), $order = array() ): array {
		$this->apply_sort( $order, $columns, 'startdate', 'desc' );

		$membership_users_table  = $this->db->prefix . 'pmpro_memberships_users';
		$membership_levels_table = $this->db->prefix . 'pmpro_membership_levels';

		$sort_map = array(
			'price'           => 'initial_payment',
			'name'            => 'membership_name',
			'date_subscribed' => 'startdate',
			'date_canceled'   => 'enddate',
		);

		if ( isset( $sort_map[ $this->sort_by ] ) ) {
			$this->sort_by = $sort_map[ $this->sort_by ];
		}

		return $this->db->get_results(
			$this->db->prepare(
				"SELECT mu.membership_id, ml.name AS membership_name, mu.initial_payment, mu.status, mu.startdate, mu.enddate
				FROM {$membership_users_table} mu
				INNER JOIN {$membership_levels_table} ml ON mu.membership_id = ml.id
				WHERE mu.user_id = {$student_id}
				AND mu.startdate BETWEEN %s AND %s
				ORDER BY {$this->sort_by} {$this->sort_dir}
            	LIMIT {$this->limit} OFFSET {$this->start}",
				$this->date_from,
				$this->date_to,
			)
		);
	}

	public function get_total_membership_count( $user_id ) {
		$table_prefix = $this->db->prefix . 'pmpro_memberships_users';

		// Query to count rows for the given user_id and date range
		return $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM {$table_prefix} WHERE user_id = %d AND startdate BETWEEN %s AND %s",
				$user_id,
				$this->date_from,
				$this->date_to
			)
		);
	}
	public function get_total_student_courses( $student_id ) {
		return $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(DISTINCT course_id) FROM {$this->db->prefix}stm_lms_user_courses uc WHERE uc.user_id = %d",
				$student_id
			)
		);
	}
}
