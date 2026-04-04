<?php

namespace MasterStudy\Lms\Pro\addons\email_manager;

use STM_LMS_Helpers;

class EmailManagerSettingsPage {
	/**
	 * @param array $pages
	 */
	public static function setup( $pages ): array {
		$pages[] = array(
			'page'        => array(
				'parent_slug' => 'stm-lms-settings',
				'page_title'  => 'Email Manager',
				'menu_title'  => 'Email Manager',
				'menu_slug'   => 'email_manager_settings',
			),
			'fields'      => self::fields(),
			'option_name' => 'stm_lms_email_manager_settings',
		);

		wp_enqueue_style( 'masterstudy-email-smart-tag-styles' );
		wp_enqueue_script( 'masterstudy-email-smart-tag-scripts' );

		return $pages;
	}

	private static function fields(): array {
		$sections = array(
			'emails_to_admin'  => esc_html__(
				'Emails to Admin',
				'masterstudy-lms-learning-management-system-pro'
			),
			'emails_to_instructors'  => esc_html__(
				'Emails to Instructors',
				'masterstudy-lms-learning-management-system-pro'
			),
			'emails_to_students'  => esc_html__(
				'Emails to Students',
				'masterstudy-lms-learning-management-system-pro'
			),
			'system_notifications'  => esc_html__(
				'System Notifications',
				'masterstudy-lms-learning-management-system-pro'
			),
		);

		$emails = require __DIR__ . '/src/emails.php';
		$emails = apply_filters( 'stm_lms_email_manager_emails', $emails );
		$data   = array();

		foreach ( $sections as $section_key => $section ) {
			$data[ $section_key ] = array(
				'name'   => $section,
				'fields' => array(),
			);
		}
		$email_settings = get_option( 'stm_lms_email_manager_settings', array() );

		$digest_mapping = array(
			'stm_lms_reports_student_checked'    => $email_settings['stm_lms_reports_student_checked_enable'] ?? false,
			'stm_lms_reports_instructor_checked' => $email_settings['stm_lms_reports_instructor_checked_enable'] ?? false,
		);

		foreach ( $emails as $email_key => $email ) {
			$value = $digest_mapping[ $email_key ] ?? true;

			if ( isset( $email['hint'] ) && ! empty( $email['hint'] ) ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_enable" ] = array(
					'group' => 'started',
					'type'  => 'checkbox',
					'label' => $email['notice'],
					'hint'  => $email['hint'] ?? '',
					'value' => $value,
				);
			} else {
				$data[ $email['section'] ]['fields'][ "{$email_key}_enable" ] = array(
					'group' => 'started',
					'type'  => 'checkbox',
					'label' => $email['notice'],
					'value' => $value,
				);
			}

			if ( $email['notice_setup'] ?? null ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_notice_setup" ] = array(
					'type'        => 'notification_message',
					'description' => esc_html__( '* To receive certificates, make sure the certificate page is set up properly.', 'masterstudy-lms-learning-management-system-pro' ),
					'value'       => $email['notice_setup'] ?? null,
					'dependency'  => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}

			if ( ( 'stm_lms_assignment_checked' === $email_key || 'stm_lms_password_change' === $email_key ) && $email['subject'] ?? null ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_subject" ] = array(
					'type'        => 'text',
					'label'       => esc_html__( 'Subject', 'masterstudy-lms-learning-management-system-pro' ),
					'value'       => $email['subject'] ?? null,
					'dependency'  => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			} else if ( $email['subject'] ?? null ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_subject" ] = array(
					'type'        => 'text',
					'label'       => esc_html__( 'Subject', 'masterstudy-lms-learning-management-system-pro' ),
					'value'       => $email['subject'] ?? null,
					'description' => esc_html__( '💡 You can use smart tags in the subject too. Just type `{` to open smart tags.', 'masterstudy-lms-learning-management-system-pro' ),
					'dependency'  => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}

			if ( $email['frequency'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_frequency_weekly" ]  = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Weekly', 'masterstudy-lms-learning-management-system' ),
					'toggle'     => false,
					'columns'    => '33',
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
				$data[ $email['section'] ]['fields'][ "{$email_key}_frequency_monthly" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Monthly', 'masterstudy-lms-learning-management-system' ),
					'toggle'     => false,
					'columns'    => '33',
					'value'      => false,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}

			if ( $email['period'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_period" ] = array(
					'type'       => 'select',
					'label'      => esc_html__( 'Day of week to send', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => 'monday',
					'options'    => array(
						'monday'    => esc_html__( 'Monday', 'masterstudy-lms-learning-management-system-pro' ),
						'tuesday'   => esc_html__( 'Tuesday', 'masterstudy-lms-learning-management-system-pro' ),
						'wednesday' => esc_html__( 'Wednesday', 'masterstudy-lms-learning-management-system-pro' ),
						'thursday'  => esc_html__( 'Thursday', 'masterstudy-lms-learning-management-system-pro' ),
						'friday'    => esc_html__( 'Friday', 'masterstudy-lms-learning-management-system-pro' ),
						'saturday'  => esc_html__( 'Saturday', 'masterstudy-lms-learning-management-system-pro' ),
						'sunday'    => esc_html__( 'Sunday', 'masterstudy-lms-learning-management-system-pro' ),
					),
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['inactive_days'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_inactive_days" ] = array(
					'type'       => 'number',
					'label'      => esc_html__( 'Send After (Days of Inactivity)', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => '3',
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['send_email_before'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_send_email_before" ] = array(
					'type'       => 'text',
					'label'      => esc_html__( 'Send email before', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( '💡 Use the format: [ 2w 4d 6h 45m ]   w = weeks d = days  h = hours  m = minutes', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => '12h',
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['time'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_time" ] = array(
					'type'       => 'time',
					'label'      => esc_html__( 'Time', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => ( ( new EmailManagerSettingsPage )->get_email_time_period() ),// phpcs:ignore
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( 'stm_lms_reports_student_checked' === $email_key || 'stm_lms_reports_instructor_checked' === $email_key || 'stm_lms_reports_admin_checked' === $email_key ) {
				if ( $email['title'] ?? false ) {
					$data[ $email['section'] ]['fields'][ "{$email_key}_title" ] = array(
						'type'        => 'text',
						'description' => esc_html__( '💡 You can use smart tags in the subject too. Just type `{` to open smart tags.', 'masterstudy-lms-learning-management-system-pro' ),
						'label'       => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => $email['title'] ?? null,
						'dependency'  => array(
							'key'   => "{$email_key}_enable",
							'value' => 'not_empty',
						),
					);
				}
			} else {
				if ( $email['title'] ?? false ) {
					$data[ $email['section'] ]['fields'][ "{$email_key}_title" ] = array(
						'type'       => 'text',
						'label'      => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
						'value'      => $email['title'] ?? null,
						'dependency' => array(
							'key'   => "{$email_key}_enable",
							'value' => 'not_empty',
						),
					);
				}
			}

			// order fields to render them
			if ( $email['date_order_render'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_date_order_render" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['order_order_render'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_order_order_render" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Order ID', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['title_order_render'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_title_order_render" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['items_order_render'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_items_order_render" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Items list', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['customer_order_render'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_customer_order_render" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Customer section', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
			if ( $email['button_order_render'] ?? false ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_button_order_render" ] = array(
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Button', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			} // order fields to render them end

			$data[ $email['section'] ]['fields'][ $email_key ] = array(
				'type'       => 'trumbowyg',
				'group'      => ( STM_LMS_Helpers::is_pro_plus() ) ? '' : 'ended',
				'label'      => esc_html__( 'Message', 'masterstudy-lms-learning-management-system-pro' ),
				'value'      => $email['message'],
				'vars'       => $email['vars'] ?? array(),
				'dependency' => array(
					'key'   => "{$email_key}_enable",
					'value' => 'not_empty',
				),
			);
			if ( STM_LMS_Helpers::is_pro_plus() ) {
				$data[ $email['section'] ]['fields'][ "{$email_key}_hidden" ] = array(
					'type'       => 'send_email',
					'group'      => 'ended',
					'label'      => esc_html__( 'Hidden', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => $email_key,
					'dependency' => array(
						'key'   => "{$email_key}_enable",
						'value' => 'not_empty',
					),
				);
			}
		}

		return apply_filters( 'stm_lms_email_manager_settings', $data );
	}

	private function get_email_time_period() {
		static $dynamic_value = 6;
		if ( $dynamic_value < 10 ) {
			$result = '0' . $dynamic_value . ':00';
		} else {
			$result = $dynamic_value . ':00';
		}
		$dynamic_value ++;

		return $result;
	}

}
