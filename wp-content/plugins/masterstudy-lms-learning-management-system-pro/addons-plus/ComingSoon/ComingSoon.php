<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\ComingSoon;

use DateTime;
use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;

final class ComingSoon implements Addon {
	public function get_name(): string {
		//@TODO Remove condition
		return defined( '\MasterStudy\Lms\Plugin\Addons::COOMING_SOON' )
			? \MasterStudy\Lms\Plugin\Addons::COOMING_SOON
			: 'coming_soon';
	}

	public function register( Plugin $plugin ): void {
		$plugin->load_file( __DIR__ . '/helpers.php' );
		$plugin->load_file( __DIR__ . '/actions.php' );
		$plugin->load_file( __DIR__ . '/filters.php' );

		// ajax actions for notifying
		add_action( 'wp_ajax_nopriv_coming_soon_notify_me', array( $this, 'notify_me' ) );
		add_action( 'wp_ajax_coming_soon_notify_me', array( $this, 'notify_me' ) );
	}

	public function notify_me() {
		check_ajax_referer( 'masterstudy-lms-coming-soon-nonce', 'nonce', false );

		$current_user = wp_get_current_user();
		$email        = ! empty( $current_user->user_email ) ? $current_user->user_email : sanitize_email( $_POST['email'] ?? '' );
		$course_id    = $_POST['id'] ?? '';

		if ( empty( $email ) ) {
			wp_send_json(
				array(
					'error' => esc_html__( 'Please enter the correct email', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);

			wp_die();
		}

		$coming_soon_emails = get_post_meta( $course_id, 'coming_soon_student_emails', true ) ?? array();

		if ( ! is_array( $coming_soon_emails ) ) {
			$coming_soon_emails = array();
		}

		$email_index = array_search( $email, array_column( $coming_soon_emails, 'email' ), true );

		if ( false === $email_index ) {
			$coming_soon_emails[] = array(
				'email' => $email,
				'time'  => new DateTime( 'now' ),
			);
			update_post_meta( $course_id, 'coming_soon_student_emails', $coming_soon_emails );

			wp_send_json(
				array(
					'success'     => esc_html__( 'Notification is added', 'masterstudy-lms-learning-management-system-pro' ),
					'title'       => esc_html__( 'Watch your inbox', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'We will notify you as soon as the course becomes available', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);
		} else {
			unset( $coming_soon_emails[ $email_index ] );
			update_post_meta( $course_id, 'coming_soon_student_emails', array_values( $coming_soon_emails ) );

			wp_send_json(
				array(
					'success'     => esc_html__( 'Notification is removed', 'masterstudy-lms-learning-management-system-pro' ),
					'title'       => esc_html__( 'Notification is disabled', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'We will not notify you when the course becomes available.', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);
		}
	}
}
