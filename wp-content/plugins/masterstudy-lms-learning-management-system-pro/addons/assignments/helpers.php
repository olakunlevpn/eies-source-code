<?php

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;

/**
 * Get assignment duration in seconds.
 *
 * Reads assignment time limit meta and converts it to seconds
 * based on the configured time unit.
 *
 * @param int $assignment_id Assignment post ID.
 *
 * @return int Duration in seconds. Returns 0 if no duration is set or meta is invalid.
 */
function masterstudy_lms_get_assignment_duration( int $assignment_id ): int {
	$duration = get_post_meta( $assignment_id, 'assignment_time_limit', true );

	if ( '' === $duration || null === $duration ) {
		return 0;
	}

	$duration = (int) $duration;

	if ( $duration <= 0 ) {
		return 0;
	}

	$duration_measure = get_post_meta( $assignment_id, 'assignment_time_limit_unit', true );

	switch ( $duration_measure ) {
		case 'hours':
			$multiple = 60 * 60;
			break;
		case 'days':
			$multiple = 24 * 60 * 60;
			break;
		case 'weeks':
			$multiple = 7 * 24 * 60 * 60;
			break;
		default:
			// Treat any unknown or empty unit as minutes.
			$multiple = 60;
	}

	return $duration * $multiple;
}

/**
 * Check if assignment has a time limit.
 *
 * @param int $item_id Assignment post ID.
 *
 * @return bool True if a non-empty time limit meta is set, false otherwise.
 */
function masterstudy_lms_assignment_has_time_limit( int $item_id ): bool {
	$time_limit = get_post_meta( $item_id, 'assignment_time_limit', true );

	return ! empty( $time_limit );
}

/**
 * Check if current user's assignment deadline is expired.
 *
 * Uses the latest attempt and its stored end time to determine
 * whether the deadline has passed.
 *
 * @param int $course_id     Course ID.
 * @param int $assignment_id Assignment post ID.
 *
 * @return bool True if deadline is expired, false otherwise.
 */
function masterstudy_lms_assignment_is_deadline_expired( int $course_id, int $assignment_id ): bool {
	if ( ! masterstudy_lms_assignment_has_time_limit( $assignment_id ) ) {
		return false;
	}

	$user    = STM_LMS_User::get_current_user();
	$user_id = isset( $user['id'] ) ? (int) $user['id'] : 0;

	if ( $user_id <= 0 ) {
		return false;
	}

	$repository   = new AssignmentStudentRepository();
	$last_attempt = $repository->get_last_attempt( $course_id, $assignment_id, $user_id );

	if ( empty( $last_attempt['user_assignment_id'] ) ) {
		return false;
	}

	$timer = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_assignments_time( $user_id, (int) $last_attempt['user_assignment_id'] ) );

	if ( empty( $timer ) || empty( $timer['end_time'] ) ) {
		return false;
	}

	$end_ts = masterstudy_lms_assignment_parse_time_to_timestamp( $timer['end_time'] );

	return ( 0 !== $end_ts ) && ( $end_ts < time() );
}

/**
 * Check if deadline is fully expired and cannot be reset on retake.
 *
 * If the assignment is configured to reset time limit on retake,
 * this function always returns false.
 *
 * @param int $course_id     Course ID.
 * @param int $assignment_id Assignment post ID.
 *
 * @return bool True if deadline is expired and not resettable, false otherwise.
 */
function masterstudy_lms_assignment_is_deadline_totally_expired( int $course_id, int $assignment_id ): bool {
	if ( ! masterstudy_lms_assignment_has_time_limit( $assignment_id ) ) {
		return false;
	}

	$reset_limit_on_retake = get_post_meta( $assignment_id, 'assignment_retake_limit_reset', true );

	if ( ! empty( $reset_limit_on_retake ) ) {
		return false;
	}

	return masterstudy_lms_assignment_is_deadline_expired( $course_id, $assignment_id );
}

/**
 * Create assignment timer record in the database.
 *
 * Stores a new timer row for the given user and assignment using
 * start and end timestamps.
 *
 * @param int $user_id       User ID.
 * @param int $assignment_id Assignment post ID.
 * @param int $start_ts      Start timestamp (Unix).
 * @param int $end_ts        End timestamp (Unix).
 *
 * @return bool True on success, false on failure.
 */
function masterstudy_lms_create_assignment_timer( int $user_id, int $assignment_id, int $start_ts, int $end_ts ): bool {
	if ( $user_id <= 0 || $assignment_id <= 0 || $end_ts <= $start_ts ) {
		return false;
	}

	return (bool) stm_lms_add_user_assignment_time(
		array(
			'user_id'       => $user_id,
			'assignment_id' => $assignment_id,
			'start_time'    => wp_date( 'Y-m-d H:i:s', $start_ts ),
			'end_time'      => wp_date( 'Y-m-d H:i:s', $end_ts ),
		)
	);
}

/**
 * Parse assignment datetime value to Unix timestamp in WP timezone.
 *
 * Assignment timers are stored using `wp_date()`, so values in DB are
 * represented in the site's configured timezone.
 *
 * @param string $datetime Date time in 'Y-m-d H:i:s' format.
 *
 * @return int Unix timestamp or 0 when value cannot be parsed.
 */
function masterstudy_lms_assignment_parse_time_to_timestamp( string $datetime ): int {
	if ( empty( $datetime ) ) {
		return 0;
	}

	$wp_timezone = wp_timezone();
	$parsed      = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $datetime, $wp_timezone );

	if ( false === $parsed ) {
		return 0;
	}

	return (int) $parsed->getTimestamp();
}

/**
 * Calculate remaining time based on a previous timer.
 *
 * Takes a timer row and returns the duration between start and end times.
 *
 * @param array $prev_timer {
 *     Previous timer row.
 *
 *     @type string $start_time Start time in 'Y-m-d H:i:s' format.
 *     @type string $end_time   End time in 'Y-m-d H:i:s' format.
 * }
 *
 * @return int Remaining time in seconds. Returns 0 if it cannot be calculated.
 */
function masterstudy_lms_assignment_calculate_remaining_time( array $prev_timer ): int {
	if ( empty( $prev_timer['start_time'] ) || empty( $prev_timer['end_time'] ) ) {
		return 0;
	}

	$prev_start_ts = masterstudy_lms_assignment_parse_time_to_timestamp( $prev_timer['start_time'] );
	$prev_end_ts   = masterstudy_lms_assignment_parse_time_to_timestamp( $prev_timer['end_time'] );

	if ( 0 === $prev_start_ts || 0 === $prev_end_ts ) {
		return 0;
	}

	$diff = $prev_end_ts - $prev_start_ts;

	return $diff > 0 ? (int) $diff : 0;
}

/**
 * Get translated assignment status messages.
 *
 * Returns a map of internal assignment status keys to
 * human-readable, translatable messages.
 *
 * @return array<string,string> Array of status => translation.
 */
function masterstudy_lms_get_assignment_status_translations(): array {
	return array(
		'passed'     => esc_html__( 'You passed assignment.', 'masterstudy-lms-learning-management-system-pro' ),
		'pending'    => esc_html__( 'Your assignment pending review.', 'masterstudy-lms-learning-management-system-pro' ),
		'not_passed' => esc_html__( 'You failed assignment.', 'masterstudy-lms-learning-management-system-pro' ),
	);
}

function masterstudy_lms_get_assignment_duration_translations(): array {
	return array(
		'hours' => esc_html__( 'hour(s)', 'masterstudy-lms-learning-management-system-pro' ),
		'days'  => esc_html__( 'day(s)', 'masterstudy-lms-learning-management-system-pro' ),
		'weeks' => esc_html__( 'week(s)', 'masterstudy-lms-learning-management-system-pro' ),
	);
}
