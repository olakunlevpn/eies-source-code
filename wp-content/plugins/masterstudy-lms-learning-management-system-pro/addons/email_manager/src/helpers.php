<?php
/**
 * Helpers.
 *
 * @package MasterStudy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return digest user types map.
 *
 * @return array<string, array<string, string>>
 */
function ms_lms_get_digest_user_types(): array {
	return array(
		'student'    => array(
			'enable'         => 'stm_lms_reports_student_checked_enable',
			'weekly_enable'  => 'stm_lms_reports_student_checked_frequency_weekly',
			'monthly_enable' => 'stm_lms_reports_student_checked_frequency_monthly',
			'weekly_day'     => 'stm_lms_reports_student_checked_period',
			'weekly_time'    => 'stm_lms_reports_student_checked_time',
			'monthly_time'   => 'stm_lms_reports_student_checked_time',
			'wp_role'        => 'subscriber',
		),
		'instructor' => array(
			'enable'         => 'stm_lms_reports_instructor_checked_enable',
			'weekly_enable'  => 'stm_lms_reports_instructor_checked_frequency_weekly',
			'monthly_enable' => 'stm_lms_reports_instructor_checked_frequency_monthly',
			'weekly_day'     => 'stm_lms_reports_instructor_checked_period',
			'weekly_time'    => 'stm_lms_reports_instructor_checked_time',
			'monthly_time'   => 'stm_lms_reports_instructor_checked_time',
			'wp_role'        => 'stm_lms_instructor',
		),
		'admin'      => array(
			'enable'         => 'stm_lms_reports_admin_checked_enable',
			'weekly_enable'  => 'stm_lms_reports_admin_checked_frequency_weekly',
			'monthly_enable' => 'stm_lms_reports_admin_checked_frequency_monthly',
			'weekly_day'     => 'stm_lms_reports_admin_checked_period',
			'weekly_time'    => 'stm_lms_reports_admin_checked_time',
			'monthly_time'   => 'stm_lms_reports_admin_checked_time',
			'wp_role'        => 'administrator',
		),
	);
}

/**
 * Migrate old single-frequency setting into new weekly/monthly toggles.
 *
 * Backward compatible: if new toggles already exist, does nothing.
 *
 * @param array $settings Settings option array.
 *
 * @return array
 */
function ms_lms_migrate_digest_frequency_settings( array $settings ): array {
	$map = array(
		'student'    => array(
			'old'   => 'stm_lms_reports_student_checked_frequency',
			'week'  => 'stm_lms_reports_student_checked_frequency_weekly',
			'month' => 'stm_lms_reports_student_checked_frequency_monthly',
		),
		'instructor' => array(
			'old'   => 'stm_lms_reports_instructor_checked_frequency',
			'week'  => 'stm_lms_reports_instructor_checked_frequency_weekly',
			'month' => 'stm_lms_reports_instructor_checked_frequency_monthly',
		),
		'admin'      => array(
			'old'   => 'stm_lms_reports_admin_checked_frequency',
			'week'  => 'stm_lms_reports_admin_checked_frequency_weekly',
			'month' => 'stm_lms_reports_admin_checked_frequency_monthly',
		),
	);

	foreach ( $map as $keys ) {
		$has_new = isset( $settings[ $keys['week'] ] ) || isset( $settings[ $keys['month'] ] );
		if ( $has_new ) {
			continue;
		}

		$old = isset( $settings[ $keys['old'] ] ) ? (string) $settings[ $keys['old'] ] : '';

		if ( 'monthly' === $old ) {
			$settings[ $keys['month'] ] = 1;
			$settings[ $keys['week'] ]  = 0;
		} else {
			$settings[ $keys['week'] ]  = 1;
			$settings[ $keys['month'] ] = 0;
		}
	}

	return $settings;
}

/**
 * Unschedule all instances of a hook.
 *
 * @param string $hook Hook name.
 *
 * @return void
 */
function ms_lms_unschedule_hook_all( string $hook ): void {
	$timestamp = wp_next_scheduled( $hook );
	while ( $timestamp ) {
		wp_unschedule_event( $timestamp, $hook );
		$timestamp = wp_next_scheduled( $hook );
	}
}

function ms_lms_unschedule_legacy_digest_events(): void {
	$legacy_hooks = array(
		'send_student_email_digest_event',
		'send_instructor_email_digest_event',
		'send_admin_email_digest_event',
	);

	foreach ( $legacy_hooks as $hook ) {
		$timestamp = wp_next_scheduled( $hook );
		while ( $timestamp ) {
			wp_unschedule_event( $timestamp, $hook );
			$timestamp = wp_next_scheduled( $hook );
		}
	}
}

/**
 * Get next weekly timestamp.
 *
 * @param string $day Day name (e.g. Monday).
 * @param string $time Time (e.g. 11:00 AM).
 *
 * @return int|false
 */
function ms_lms_get_next_weekly_timestamp( string $day, string $time ) {
	$day  = trim( $day );
	$time = trim( $time );

	if ( '' === $day ) {
		$day = 'Monday';
	}

	if ( '' === $time ) {
		$time = '11:00 AM';
	}

	return strtotime( "next {$day} {$time}" );
}

/**
 * Get next monthly timestamp.
 *
 * @param string $time Time (e.g. 11:00 AM).
 *
 * @return int|false
 */
function ms_lms_get_next_monthly_timestamp( string $time ) {
	$time = trim( $time );
	if ( '' === $time ) {
		$time = '11:00 AM';
	}

	return strtotime( "first day of next month {$time}" );
}

if ( ! function_exists( 'is_digest_enabled' ) ) {
	/**
	 * Helper function to check if a specific digest is enabled.
	 *
	 * @param string $digest_key Option key.
	 * @return bool
	 */
	function is_digest_enabled( string $digest_key ): bool {
		$email_settings = get_option( 'stm_lms_email_manager_settings', array() );

		return ! empty( $email_settings[ $digest_key ] );
	}
}

add_action(
	'init',
	function () {
		if ( get_transient( 'ms_lms_digest_cron_synced' ) ) {
			return;
		}

		ms_lms_schedule_digest_cron();
		set_transient( 'ms_lms_digest_cron_synced', 1, YEAR_IN_SECONDS );
	},
	20
);


function ms_lms_schedule_digest_cron(): void {
	$user_types = ms_lms_get_digest_user_types();
	$settings   = get_option( 'stm_lms_email_manager_settings', array() );
	$settings   = ms_lms_migrate_digest_frequency_settings( is_array( $settings ) ? $settings : array() );

	ms_lms_unschedule_legacy_digest_events();

	foreach ( $user_types as $type ) {
		$is_enabled = ! empty( $settings[ $type['enable'] ] );

		$weekly_hook  = 'ms_lms_send_' . $type['wp_role'] . '_digest_weekly';
		$monthly_hook = 'ms_lms_send_' . $type['wp_role'] . '_digest_monthly';

		ms_lms_unschedule_hook_all( $weekly_hook );
		ms_lms_unschedule_hook_all( $monthly_hook );

		if ( ! $is_enabled ) {
			continue;
		}

		$weekly_on  = ! empty( $settings[ $type['weekly_enable'] ] );
		$monthly_on = ! empty( $settings[ $type['monthly_enable'] ] );

		if ( $weekly_on ) {
			$day  = isset( $settings[ $type['weekly_day'] ] ) ? (string) $settings[ $type['weekly_day'] ] : 'Monday';
			$time = isset( $settings[ $type['weekly_time'] ] ) ? (string) $settings[ $type['weekly_time'] ] : '11:00 AM';

			$next = ms_lms_get_next_weekly_timestamp( $day, $time );
			if ( $next ) {
				wp_schedule_event( $next, 'weekly', $weekly_hook );
			}
		}

		if ( $monthly_on ) {
			$time = isset( $settings[ $type['monthly_time'] ] ) ? (string) $settings[ $type['monthly_time'] ] : '11:00 AM';

			$next = ms_lms_get_next_monthly_timestamp( $time );
			if ( $next ) {
				wp_schedule_event( $next, 'monthly', $monthly_hook );
			}
		}
	}

	update_option( 'stm_lms_email_manager_settings', $settings );
}

/**
 * Digest date range helper by frequency.
 *
 * @param string $frequency weekly|monthly.
 *
 * @return array{date_from:string,date_to:string,frequency:string}
 */
function ms_lms_get_date_range_by_frequency( string $frequency ): array {
	$frequency = ( 'monthly' === $frequency ) ? 'monthly' : 'weekly';
	$now_ts    = current_time( 'timestamp' );

	if ( 'monthly' === $frequency ) {
		$date_from = gmdate( 'Y-m-01', $now_ts );
		$date_to   = gmdate( 'Y-m-t', $now_ts );
	} else {
		$current_date = current_time( 'Y-m-d' );
		$date_from    = gmdate( 'Y-m-d', strtotime( '-6 days', strtotime( $current_date ) ) );
		$date_to      = $current_date;
	}

	return array(
		'date_from' => $date_from,
		'date_to'   => $date_to,
		'frequency' => $frequency,
	);
}

/**
 * Get subject by role.
 *
 * @param string $role Role slug.
 * @param array $settings Settings array.
 *
 * @return string
 */
function get_subject_by_role( $role, $settings ) {
	$default_subject = esc_html__( 'Your Weekly Report', 'masterstudy-lms-learning-management-system-pro' );

	switch ( $role ) {
		case 'subscriber':
			$email_subject = $settings['stm_lms_reports_student_checked_title'] ?? $default_subject;
			break;
		case 'stm_lms_instructor':
			$email_subject = $settings['stm_lms_reports_instructor_checked_title'] ?? $default_subject;
			break;
		case 'admin':
		case 'administrator':
			$email_subject = $settings['stm_lms_reports_admin_checked_title'] ?? $default_subject;
			break;
		default:
			$email_subject = $default_subject;
			break;
	}

	return $email_subject;
}

/**
 * Get message by role.
 *
 * @param string $role Role slug.
 * @param array $settings Settings array.
 *
 * @return string
 */
function get_message_by_role( $role, $settings ) {
	$default_subject = esc_html__( 'Your Weekly Report', 'masterstudy-lms-learning-management-system-pro' );

	switch ( $role ) {
		case 'subscriber':
			$email_subject = $settings['stm_lms_reports_student_checked'] ?? $default_subject;
			break;
		case 'stm_lms_instructor':
			$email_subject = $settings['stm_lms_reports_instructor_checked'] ?? $default_subject;
			break;
		case 'admin':
		case 'administrator':
			$email_subject = $settings['stm_lms_reports_admin_checked'] ?? $default_subject;
			break;
		default:
			$email_subject = $default_subject;
			break;
	}

	return $email_subject;
}
