<?php

use MasterStudy\Lms\Database\CurriculumSection;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeDisplay;
use MasterStudy\Lms\Utility\CourseGrade;

// Add is_gradable field to the user course table
function masterstudy_lms_add_is_gradable( $material ) {
	if ( ! empty( $material->post_type ) && in_array( $material->post_type, array( PostType::QUIZ, PostType::ASSIGNMENT ), true ) ) {
		global $wpdb;

		$section = ( new CurriculumSection() )->find_one( $material->section_id );
		$table   = stm_lms_user_courses_name( $wpdb );

		if ( $section ) {
			$wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"UPDATE $table SET is_gradable = 1 WHERE course_id = %d",
					$section->course_id
				)
			);
		}
	}
}
add_action( 'masterstudy_lms_curriculum_material_created', 'masterstudy_lms_add_is_gradable', 10, 1 );

// Unset is_gradable field from the user course table
function masterstudy_lms_remove_is_gradable( $material ) {
	if ( ! empty( $material->post_type ) && in_array( $material->post_type, array( PostType::QUIZ, PostType::ASSIGNMENT ), true ) ) {
		global $wpdb;

		$section = ( new CurriculumSection() )->find_one( $material->section_id );
		$table   = stm_lms_user_courses_name( $wpdb );

		if ( $section && ! masterstudy_lms_is_course_gradable( $section->course_id, $material->id ) ) {
			$wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"UPDATE $table SET is_gradable = 0 WHERE course_id = %d",
					$section->course_id
				)
			);
		}
	}
}
add_action( 'masterstudy_lms_curriculum_material_before_delete', 'masterstudy_lms_remove_is_gradable', 10, 1 );

// Generate course grade when a user quiz is added
function masterstudy_lms_generate_grade_on_user_quiz_added( $user_quiz ) {
	if ( class_exists( 'MasterStudy\Lms\Utility\CourseGrade' ) && ! empty( $user_quiz ) ) {
		CourseGrade::update_user_course_grade( (int) $user_quiz['user_id'], (int) $user_quiz['course_id'] );
	}
}
add_action( 'masterstudy_lms_user_quiz_added', 'masterstudy_lms_generate_grade_on_user_quiz_added', 10, 1 );

// Generate course grade when a user quiz is deleted
function masterstudy_lms_generate_grade_on_user_quiz_deleted( $user_id, $course_id ) {
	if ( class_exists( 'MasterStudy\Lms\Utility\CourseGrade' ) ) {
		CourseGrade::update_user_course_grade( (int) $user_id, (int) $course_id );
	}
}
add_action( 'masterstudy_lms_user_quiz_deleted', 'masterstudy_lms_generate_grade_on_user_quiz_deleted', 10, 2 );

// Generate course grade when Course Progress is updated
function masterstudy_lms_progress_updated( $course_id, $user_id ) {
	if ( class_exists( 'MasterStudy\Lms\Utility\CourseGrade' ) ) {
		CourseGrade::update_user_course_grade( (int) $user_id, (int) $course_id );
	}
}
add_action( 'stm_lms_progress_updated', 'masterstudy_lms_progress_updated', 10, 2 );

// Output a Grade for User Assignment
function masterstudy_lms_student_assignments_grade_column_field( $column, $assignment_id ) {
	if ( 'lms_grade' === $column ) {
		global $wpdb;

		$user_assignment = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT grade, status FROM {$wpdb->prefix}stm_lms_user_assignments WHERE user_assignment_id = %d",
				$assignment_id
			)
		);

		if ( in_array( $user_assignment->status, array( AssignmentStudentRepository::STATUS_PASSED, AssignmentStudentRepository::STATUS_NOT_PASSED ), true ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo GradeDisplay::get_instance()->detailed_render( $user_assignment->grade );
		}
	}
}
add_action( 'manage_stm-user-assignment_posts_custom_column', 'masterstudy_lms_student_assignments_grade_column_field', 10, 2 );

// Show Assignment Grade in User Assignment Review
function masterstudy_lms_admin_assignment_grading( $user_assignment_id ) {
	wp_enqueue_style( 'masterstudy-alert' );
	wp_enqueue_style( 'masterstudy-lms-grades', STM_LMS_PRO_URL . 'assets/css/assignments/grades.css', array(), STM_LMS_PRO_VERSION );
	wp_enqueue_script( 'masterstudy-lms-grades', STM_LMS_PRO_URL . 'assets/js/assignments/grades.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );

	STM_LMS_Templates::show_lms_template(
		'grades/assignment-grading-fields',
		array( 'user_assignment_id' => $user_assignment_id )
	);

	STM_LMS_Templates::show_lms_template( 'grades/grades-table-hint' );
}
add_action( 'masterstudy_lms_admin_assignment_review', 'masterstudy_lms_admin_assignment_grading', 10, 1 );

function masterstudy_lms_grades_menu() {
	add_menu_page(
		esc_html__( 'Grades', 'masterstudy-lms-learning-management-system-pro' ),
		esc_html__( 'Grades', 'masterstudy-lms-learning-management-system-pro' ),
		'manage_options',
		'grades',
		'masterstudy_lms_grades_instructor',
		'',
		4
	);
}
add_action( 'admin_menu', 'masterstudy_lms_grades_menu' );

function masterstudy_lms_grades_instructor() {
	STM_LMS_Templates::show_lms_template( 'grades/instructor' );
}
