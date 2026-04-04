<?php

use MasterStudy\Lms\Pro\addons\certificate_builder\CertificateRepository;
use MasterStudy\Lms\Pro\addons\email_manager\EmailDataCompiler;
use MasterStudy\Lms\Pro\addons\email_manager\EmailManagerSettingsPage;
use MasterStudy\Lms\Pro\addons\email_manager\EmailManagerSettings;

add_filter( 'stm_lms_filter_email_data', array( EmailDataCompiler::class, 'compile' ), 10, 1 );
add_filter( 'wpcfto_options_page_setup', array( EmailManagerSettingsPage::class, 'setup' ), 100 );

/**
 * Ensure weekly/monthly schedules exist.
 *
 * @param array $schedules Cron schedules.
 * @return array
 */
function ms_lms_custom_cron_schedules( $schedules ) {
	if ( ! isset( $schedules['weekly'] ) ) {
		$schedules['weekly'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Once Weekly', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}

	if ( ! isset( $schedules['monthly'] ) ) {
		$schedules['monthly'] = array(
			'interval' => 30 * DAY_IN_SECONDS,
			'display'  => __( 'Once Monthly', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}

	return $schedules;
}
add_filter( 'cron_schedules', 'ms_lms_custom_cron_schedules' );

/**
 * Reschedule digests after settings saved.
 */
add_action( 'wpcfto_after_settings_saved', 'ms_lms_schedule_digest_cron' );

/**
 * Unschedule digest events (call from your plugin deactivation hook).
 *
 * NOTE: register_deactivation_hook() must be in the main plugin file.
 *
 * @return void
 */
function ms_lms_unschedule_digest_cron(): void {
	EmailManagerSettings::sync_with_defaults();

	$user_types = ms_lms_get_digest_user_types();

	foreach ( $user_types as $type ) {
		$weekly_hook  = 'ms_lms_send_' . $type['wp_role'] . '_digest_weekly';
		$monthly_hook = 'ms_lms_send_' . $type['wp_role'] . '_digest_monthly';

		ms_lms_unschedule_hook_all( $weekly_hook );
		ms_lms_unschedule_hook_all( $monthly_hook );
	}
}

function masterstudy_lms_load_report_template( $email_subject, $user_email, $role_to_check, $user_id = 0, $frequency = 'weekly' ) {
	$settings = array();

	if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
		$settings = STM_LMS_Email_Manager::stm_lms_get_settings();
	}

	$message = STM_LMS_Templates::load_lms_template(
		'emails/report-template',
		array(
			'email_manager' => $settings,
			'role'          => $role_to_check,
			'user_id'       => (int) $user_id,
			'frequency'     => ( 'monthly' === $frequency ) ? 'monthly' : 'weekly',
		)
	);

	add_filter(
		'wp_mail_from',
		function ( $from_email ) use ( $settings ) {
			return $settings['stm_lms_email_template_header_email'] ?? $from_email;
		}
	);

	$analytics_url = STM_LMS_Helpers::masterstudy_lms_get_site_url();

	if ( 'administrator' === $role_to_check ) {
		$analytics_url .= '/wp-admin/admin.php?page=revenue';
	} elseif ( 'stm_lms_instructor' === $role_to_check && STM_LMS_Options::get_option( 'instructors_reports', true ) ) {
		$analytics_url = ms_plugin_user_account_url() . 'analytics/';
	} else {
		$analytics_url = ms_plugin_user_account_url();
	}

	$email_data = array(
		'instructor_name' => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
		'user_login'      => STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
		'blog_name'       => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
		'site_url'        => MS_LMS_Email_Template_Helpers::link( STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
		'analytics_url'   => MS_LMS_Email_Template_Helpers::link( $analytics_url ),
		'date'            => gmdate( 'Y-m-d H:i:s' ),
	);

	$message       = MS_LMS_Email_Template_Helpers::render( $message, $email_data );
	$email_subject = MS_LMS_Email_Template_Helpers::render( $email_subject, $email_data );

	wp_mail( $user_email, $email_subject, $message );
}
/**
 * Send digest emails for a WP role and frequency.
 *
 * @param string $role_to_check WP role slug.
 * @param string $frequency     weekly|monthly.
 * @param bool   $send_test_email Send test email.
 * @return void
 */
function ms_lms_process_user_emails( string $role_to_check, string $frequency, bool $send_test_email = false ): void {
	$number        = 20;
	$page          = 1;
	$settings      = get_option( 'stm_lms_email_manager_settings', array() );
	$email_subject = get_subject_by_role( $role_to_check, is_array( $settings ) ? $settings : array() );

	if ( $send_test_email ) {
		add_filter( 'wp_mail_content_type', array( 'STM_LMS_Helpers', 'set_html_content_type' ) );
		masterstudy_lms_load_report_template( $email_subject, get_option( 'admin_email' ), $role_to_check, 0, $frequency );
		remove_filter( 'wp_mail_content_type', array( 'STM_LMS_Helpers', 'set_html_content_type' ) );
		wp_die();
	}

	do {
		$user_query = new WP_User_Query(
			array(
				'number'     => $number,
				'paged'      => $page,
				'fields'     => array( 'ID', 'user_email' ),
				'meta_query' => array(
					array(
						'key'     => 'disable_report_email_notifications',
						'compare' => 'NOT EXISTS',
					),
				),
				'role'       => $role_to_check,
			)
		);

		$users = $user_query->get_results();

		add_filter( 'wp_mail_content_type', array( 'STM_LMS_Helpers', 'set_html_content_type' ) );

		if ( ! empty( $users ) ) {
			foreach ( $users as $user ) {
				$user_id    = (int) $user->ID;
				$user_email = (string) $user->user_email;

				masterstudy_lms_load_report_template( $email_subject, $user_email, $role_to_check, $user_id, $frequency );
			}
		}

		remove_filter( 'wp_mail_content_type', array( 'STM_LMS_Helpers', 'set_html_content_type' ) );

		$page++;
	} while ( ! empty( $users ) );
}

if ( ! function_exists( 'process_user_emails' ) ) {
	/**
	 * Backward compatible wrapper for old code.
	 *
	 * Old signature:
	 * process_user_emails( $role_to_check, $send_test_email = false )
	 *
	 * New engine:
	 * ms_lms_process_user_emails( $role, $frequency, $send_test_email )
	 *
	 * @param string $role_to_check WP role slug.
	 * @param bool   $send_test_email Whether it is a test send.
	 * @return void
	 */
	function process_user_emails( $role_to_check, $send_test_email = false ): void {
		EmailManagerSettings::sync_with_defaults();

		$settings = get_option( 'stm_lms_email_manager_settings', array() );
		$settings = is_array( $settings ) ? $settings : array();
		$settings = ms_lms_migrate_digest_frequency_settings( $settings );

		// Decide which frequency to use for legacy calls.
		$frequency = 'weekly';

		if ( 'subscriber' === $role_to_check ) {
			$weekly_on  = ! empty( $settings['stm_lms_reports_student_checked_frequency_weekly'] );
			$monthly_on = ! empty( $settings['stm_lms_reports_student_checked_frequency_monthly'] );
		} elseif ( 'stm_lms_instructor' === $role_to_check ) {
			$weekly_on  = ! empty( $settings['stm_lms_reports_instructor_checked_frequency_weekly'] );
			$monthly_on = ! empty( $settings['stm_lms_reports_instructor_checked_frequency_monthly'] );
		} else {
			// administrator/admin.
			$weekly_on  = ! empty( $settings['stm_lms_reports_admin_checked_frequency_weekly'] );
			$monthly_on = ! empty( $settings['stm_lms_reports_admin_checked_frequency_monthly'] );
		}

		// If only monthly is enabled, send monthly. If both enabled, send weekly (legacy behavior).
		if ( $monthly_on && ! $weekly_on ) {
			$frequency = 'monthly';
		}

		ms_lms_process_user_emails( (string) $role_to_check, $frequency, (bool) $send_test_email );
	}
}

/**
 * Weekly/monthly cron callbacks (role-based).
 */
add_action(
	'ms_lms_send_subscriber_digest_weekly',
	function () {
		ms_lms_process_user_emails( 'subscriber', 'weekly' );
	}
);

add_action(
	'ms_lms_send_subscriber_digest_monthly',
	function () {
		ms_lms_process_user_emails( 'subscriber', 'monthly' );
	}
);

add_action(
	'ms_lms_send_stm_lms_instructor_digest_weekly',
	function () {
		ms_lms_process_user_emails( 'stm_lms_instructor', 'weekly' );
	}
);

add_action(
	'ms_lms_send_stm_lms_instructor_digest_monthly',
	function () {
		ms_lms_process_user_emails( 'stm_lms_instructor', 'monthly' );
	}
);

add_action(
	'ms_lms_send_administrator_digest_weekly',
	function () {
		ms_lms_process_user_emails( 'administrator', 'weekly' );
	}
);

add_action(
	'ms_lms_send_administrator_digest_monthly',
	function () {
		ms_lms_process_user_emails( 'administrator', 'monthly' );
	}
);

/**
 * Certificate email (your original code).
 */
function mastertudy_plugin_send_certificate_email( $user_id, $course_id, $test_mode ) {
	if ( ! $course_id || ! $user_id ) {
		return;
	}

	if ( function_exists( 'masterstudy_lms_course_has_certificate' ) && ! masterstudy_lms_course_has_certificate( $course_id ) ) {
		return;
	}

	$user_data = get_userdata( $user_id );
	if ( ! $user_data ) {
		return;
	}

	$manager_settings = get_option( 'stm_lms_email_manager_settings', array() );

	$email_template = '{{date}} {{certificate_preview}} {{button}}';

	$student_email       = $user_data->user_email;
	$current_time        = date_i18n( 'F j, Y g:i a', current_time( 'timestamp' ) );
	$button_url          = ( new CertificateRepository() )->certificate_page_url() . "?user={$user_id}&course={$course_id}";
	$email_subject       = $manager_settings['stm_lms_certificates_preview_checked_subject'] ?? esc_html__( 'You have received a certificate!', 'masterstudy-lms-learning-management-system-pro' );
	$button_message      = esc_html__( 'View Certificate', 'masterstudy-lms-learning-management-system-pro' );
	$certificate_preview = STM_LMS_PRO_URL . 'assets/img/emails/certificate_preview.jpg';

	$email_subject = str_replace(
		array( '{{date}}' ),
		array( $current_time ),
		$email_subject
	);

	$post_author_id = get_post_field( 'post_author', $course_id );

	$email_data = array(
		'date'                => $current_time,
		'certificate_preview' => "<p><img src='{$certificate_preview}' alt='Certificate Preview' style='max-width: 620px; width: auto; height: auto; display: block; margin: 0 auto;' /></p>",
		'button'              => "<p><a href='{$button_url}' class='masterstudy-button masterstudy-button_style-primary masterstudy-button_size-sm masterstudy-button_icon-left masterstudy-button_icon-upload-alt' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; font-size: 16px; border-radius: 5px; margin-top: 25px;'>{$button_message}</a></p>",
		'instructor_name'     => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $post_author_id ),
		'course_title'        => get_the_title( $course_id ),
		'blog_name'           => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
		'site_url'            => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
		'course_url'          => \MS_LMS_Email_Template_Helpers::link( get_permalink( $course_id ) ),
	);

	$email_template = '<div>' . $email_template . '</div>';

	$email_template .= "<style>
		body{ margin-top: 40px; margin-bottom: 40px; }
		h2{
			color: #333; text-align: center; margin-bottom: 20px; font-size: 24px; font-weight: bold;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
		}
		p, div{
			text-align: center;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
		}
	</style>";

	$email_body    = \MS_LMS_Email_Template_Helpers::render( $email_template, $email_data );
	$email_subject = \MS_LMS_Email_Template_Helpers::render( $email_subject, $email_data );

	STM_LMS_Helpers::send_email(
		$student_email,
		$email_subject,
		$email_body,
		'stm_lms_certificates_preview_checked',
		$email_data
	);
}
add_action( 'masterstudy_plugin_student_course_completion', 'mastertudy_plugin_send_certificate_email', 10, 3 );

/**
 * Pro email filter (your original code).
 */
if ( STM_LMS_Helpers::is_pro() && ! STM_LMS_Helpers::is_pro_plus() ) {
	add_filter( 'stm_lms_filter_email_data', 'masterstudy_plugin_pro_email_filter_email_data', 90, 1 );
}

function masterstudy_plugin_pro_email_filter_email_data( $data ) {
	$data['message'] = STM_LMS_Templates::load_lms_template(
		'emails/pro-template',
		array(
			'message' => $data['message'],
			'subject' => $data['subject'],
		)
	);

	return $data;
}

/**
 * Course rejected email (your original code).
 */
add_action(
	'stm_lms_course_rejected',
	function ( $post_id ) {
		$instructor_id = get_post_field( 'post_author', $post_id );
		$instructor    = get_userdata( $instructor_id );

		if ( empty( $instructor ) || empty( $instructor->user_email ) ) {
			return;
		}

		$admin_id    = get_current_user_id();
		$admin_user  = get_userdata( $admin_id );
		$admin_email = $admin_user ? $admin_user->user_email : get_option( 'admin_email' );

		$email_data = array(
			'course_title' => get_the_title( $post_id ),
			'admin_email'  => $admin_email,
			'blog_name'    => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'     => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'date'         => gmdate( 'Y-m-d H:i:s' ),
		);

		$template = wp_kses_post(
			'We regret to inform you that your course {{course_title}} has been rejected by the admin.<br>
			We encourage you to get feedback from the admin and make the necessary adjustments to meet our guidelines.
			If you want to get feedback and have any questions, please contact at {{admin_email}}.<br>
			Thank you for your understanding and cooperation.'
		);

		$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );

		$subject = esc_html__( 'Your Course {{course_title}} Has Been Rejected', 'masterstudy-lms-learning-management-system' );
		$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

		STM_LMS_Helpers::send_email(
			$instructor->user_email,
			$subject,
			$message,
			'stm_lms_course_rejected',
			$email_data
		);
	},
	10,
	1
);

/**
 * Track last login (your original code).
 */
add_action( 'wp_login', 'track_user_last_login', 10, 2 );
function track_user_last_login( $user_login, $user ) {
	update_user_meta( $user->ID, 'masterstudy_lms_last_login', current_time( 'timestamp' ) );
	delete_user_meta( $user->ID, 'inactivity_last_sent_ts' );
}

/**
 * Return inactivity threshold in days (admin option), min 1.
 *
 * @return int
 */
function ms_lms_get_inactivity_days(): int {
	EmailManagerSettings::sync_with_defaults();

	$settings = get_option( 'stm_lms_email_manager_settings', array() );
	$days     = (int) ( $settings['stm_lms_email_inactivity_students_inactive_days'] ?? 30 );

	return ( $days > 0 ) ? $days : 1;
}

/**
 * Get inactivity email option.
 *
 * @return int
 */
function ms_lms_get_inactivity_email_option(): int {
	EmailManagerSettings::sync_with_defaults();

	$settings = get_option( 'stm_lms_email_manager_settings', array() );

	return $settings['stm_lms_email_inactivity_students_enable'] ?? false;
}

/**
 * Register a dynamic cron schedule named "ms_lms_inactivity_every_n_days"
 * with an interval equal to inactive_days * DAY_IN_SECONDS.
 */
add_filter(
	'cron_schedules',
	function ( $schedules ) {
		$inactive_days = ms_lms_get_inactivity_days();
		$interval_key  = 'ms_lms_inactivity_every_' . $inactive_days . '_days';

		$schedules[ $interval_key ] = array(
			'interval' => $inactive_days * DAY_IN_SECONDS,
			'display'  => sprintf(
				__( 'Every %d days (MS Inactivity)', 'masterstudy-lms-learning-management-system-pro' ),
				$inactive_days
			),
		);

		return $schedules;
	}
);

/**
 * (Re)schedule the inactivity cron using the dynamic interval.
 */
function ms_lms_schedule_inactivity_cron() {
	$inactive_days = ms_lms_get_inactivity_days();
	$interval_key  = 'ms_lms_inactivity_every_' . $inactive_days . '_days';
	$hook          = 'ms_lms_inactivity_reminder_students_check_event';

	$next = wp_next_scheduled( $hook );
	if ( $next ) {
		wp_unschedule_event( $next, $hook );
	}

	if ( ! wp_next_scheduled( $hook ) ) {
		wp_schedule_event( time(), $interval_key, $hook );
	}
}
add_action( 'wpcfto_after_settings_saved', 'ms_lms_schedule_inactivity_cron' );

/**
 * Inactivity emails sender (your original code).
 */
function ms_lms_send_interval_inactivity_emails() {
	if ( ! ms_lms_get_inactivity_email_option() ) {
		$hook = 'ms_lms_inactivity_reminder_students_check_event';

		$next = wp_next_scheduled( $hook );
		if ( $next ) {
			wp_unschedule_event( $next, $hook );
		}

		$hook = 'masterstudy_lms_send_inactivity_students_emails_daily';

		$next = wp_next_scheduled( $hook );
		if ( $next ) {
			wp_unschedule_event( $next, $hook );
		}

		return false;
	}

	$inactive_days = ms_lms_get_inactivity_days();
	$threshold_sec = $inactive_days * DAY_IN_SECONDS;
	$now           = current_time( 'timestamp' );

	$per_page = 200;
	$page     = 1;

	do {
		$users = get_users(
			array(
				'role'       => 'subscriber',
				'number'     => $per_page,
				'offset'     => ( $page - 1 ) * $per_page,
				'fields'     => array( 'ID', 'user_email', 'user_login' ),
				'meta_query' => array(
					array(
						'key'     => 'disable_report_email_notifications',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		if ( empty( $users ) ) {
			break;
		}

		foreach ( $users as $user ) {
			$user_id    = (int) $user->ID;
			$user_email = (string) $user->user_email;

			$last_login = (int) get_user_meta( $user_id, 'masterstudy_lms_last_login', true );
			if ( empty( $last_login ) ) {
				$reg = strtotime( get_userdata( $user_id )->user_registered );
				if ( $reg ) {
					$last_login = $reg;
				}
			}

			if ( empty( $last_login ) ) {
				continue;
			}

			if ( ( $now - $last_login ) < $threshold_sec ) {
				continue;
			}

			$last_sent_ts = (int) get_user_meta( $user_id, 'inactivity_last_sent_ts', true );
			if ( $last_sent_ts && ( $now - $last_sent_ts ) < $threshold_sec ) {
				continue;
			}

			$days_inactive = (int) floor( ( $now - $last_login ) / DAY_IN_SECONDS );

			$email_data = array(
				'inactivity_period' => $days_inactive,
				'user_login'        => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'login_url'         => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_login_url() ),
				'blog_name'         => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'          => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'              => gmdate( 'Y-m-d H:i:s' ),
			);

			$template = wp_kses_post(
				'Hey {{user_login}}, <br><br>
				We noticed you\'ve been away from {{blog_name}} for {{inactivity_period}} days. <br>
				Your learning journey is important, and we\'re here to support you every step of the way. <br><br>
				<b>Here are the details:</b> <br>
				Student Username: {{user_login}} <br>
				Inactive Days: {{inactivity_period}} <br>
				Site Name: {{blog_name}}<br>
				Current Date: {{date}}<br><br>
				<a href="{{login_url}}" target="_blank">Sign in</a> and continue learning where you left off'
			);

			$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data );
			$subject = wp_kses_post( 'We miss you! It’s been {{inactivity_period}} days since your last visit.' );

			\STM_LMS_Helpers::send_email(
				$user_email,
				$subject,
				$message,
				'stm_lms_email_inactivity_students',
				$email_data
			);

			update_user_meta( $user_id, 'inactivity_last_sent_ts', $now );
		}

		$page++;
	} while ( count( $users ) === $per_page ); // phpcs:ignore
}
add_action( 'ms_lms_inactivity_reminder_students_check_event', 'ms_lms_send_interval_inactivity_emails' );
