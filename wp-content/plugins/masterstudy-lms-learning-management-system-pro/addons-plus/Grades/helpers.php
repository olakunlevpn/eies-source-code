<?php

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

// Check if Course is Gradable
function masterstudy_lms_is_course_gradable( $course_id, $exclude_material_id = null ): bool {
	global $wpdb;

	$exclude_condition      = $exclude_material_id ?
		$wpdb->prepare( 'AND cm.id != %d', $exclude_material_id )
		: '';
	$has_quiz_or_assignment = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}stm_lms_curriculum_sections AS cs JOIN {$wpdb->prefix}stm_lms_curriculum_materials AS cm
			ON cs.id = cm.section_id WHERE cs.course_id = %d AND cm.post_type IN (%s, %s) {$exclude_condition}", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$course_id,
			PostType::QUIZ,
			PostType::ASSIGNMENT
		)
	);

	return $has_quiz_or_assignment > 0;
}

// Update User Assignment Grade
function masterstudy_lms_update_user_assignment_grade( $user_assignment_id, $post_data, $status ) {
	$grade_type = sanitize_text_field( $post_data['grade-type'] ?? '' );

	if ( empty( $grade_type ) ) {
		return $status;
	}

	$grade = sanitize_text_field( $post_data[ $grade_type ] ?? null );

	// Update Assignment Grade Type and Grade
	update_post_meta( $user_assignment_id, 'grade_type', $grade_type );

	if ( $grade ) {
		if ( 'percent' === $grade_type ) {
			$grade = intval( $grade );
		} else {
			$grade = GradeCalculator::get_instance()->get_percent_by_type( $grade, $grade_type );
		}

		// Update Assignment Grade
		( new AssignmentStudentRepository() )->update_grade( $user_assignment_id, $grade );

		// Set Assignment Status
		$passing_grade = AssignmentStudentRepository::get_passing_grade( $user_assignment_id );
		$status        = $grade >= $passing_grade ? 'passed' : 'not_passed';
	} else {
		// Set Assignment Status according to the Reviewer Comment
		$status = ! empty( $post_data['editor_comment'] ) ? 'draft' : 'pending';
	}

	return $status;
}
