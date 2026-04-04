<?php

namespace MasterStudy\Lms\Pro\addons\email_manager;

class EmailManagerSettings {
	/**
	 * Cache sync result per request to avoid repeated writes.
	 *
	 * @var bool
	 */
	private static $synced = false;

	public static function get_all(): array {
		self::sync_with_defaults();

		return (array) get_option( 'stm_lms_email_manager_settings', array() );
	}

	/**
	 * Ensure runtime email templates always exist in DB settings.
	 * This prevents newly-enabled addon templates from being available
	 * in UI only, but missing in option storage until manual save.
	 */
	public static function sync_with_defaults( bool $force = false ): void {
		if ( self::$synced && ! $force ) {
			return;
		}

		$current  = (array) get_option( 'stm_lms_email_manager_settings', array() );
		$defaults = self::build_defaults();

		if ( empty( $defaults ) ) {
			self::$synced = true;

			return;
		}

		$missing = array_diff_key( $defaults, $current );
		if ( ! empty( $missing ) ) {
			update_option( 'stm_lms_email_manager_settings', $current + $missing );
		}

		self::$synced = true;
	}

	/**
	 * Build flattened defaults from current runtime templates.
	 */
	private static function build_defaults(): array {
		$emails_path = __DIR__ . '/src/emails.php';
		if ( ! file_exists( $emails_path ) ) {
			return array();
		}

		$emails = require $emails_path;
		$emails = apply_filters( 'stm_lms_email_manager_emails', $emails );

		if ( ! is_array( $emails ) ) {
			return array();
		}

		$defaults = array();

		foreach ( $emails as $email_key => $email ) {
			if ( ! is_array( $email ) ) {
				continue;
			}

			$defaults[ "{$email_key}_enable" ] = true;
			$defaults[ $email_key ]          = $email['message'] ?? '';

			if ( isset( $email['subject'] ) ) {
				$defaults[ "{$email_key}_subject" ] = $email['subject'];
			}

			if ( isset( $email['title'] ) ) {
				$defaults[ "{$email_key}_title" ] = $email['title'];
			}

			if ( ! empty( $email['frequency'] ) ) {
				$defaults[ "{$email_key}_frequency" ] = 'weekly';
			}

			if ( ! empty( $email['period'] ) ) {
				$defaults[ "{$email_key}_period" ] = 'monday';
			}

			if ( ! empty( $email['inactive_days'] ) ) {
				$defaults[ "{$email_key}_inactive_days" ] = '3';
			}

			if ( ! empty( $email['send_email_before'] ) ) {
				$defaults[ "{$email_key}_send_email_before" ] = '12h';
			}

			if ( ! empty( $email['time'] ) ) {
				$defaults[ "{$email_key}_time" ] = '06:00';
			}

			foreach (
				array(
					'date_order_render',
					'order_order_render',
					'title_order_render',
					'items_order_render',
					'customer_order_render',
					'button_order_render',
				) as $flag_key
			) {
				if ( ! empty( $email[ $flag_key ] ) ) {
					$defaults[ "{$email_key}_{$flag_key}" ] = true;
				}
			}
		}

		// Preserve existing behavior for digest defaults in settings page.
		$defaults['stm_lms_reports_student_checked_enable']    = false;
		$defaults['stm_lms_reports_instructor_checked_enable'] = false;

		return $defaults;
	}
}
