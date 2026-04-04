<?php
// TODO: Move all actions to REST API

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleCalendarEvent;
use \MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleOpenAuth;
use MasterStudy\Lms\Repositories\CurriculumRepository;

/**
 * Upload Google Meet credentials
 */
function masterstudy_lms_google_meet_upload_credentials() {
	check_ajax_referer( 'stm-lms-gm-nonce', 'nonce' );

	$file      = $_FILES['file'] ?? '';
	$path_info = pathinfo( $file['name'] );

	if ( 'json' !== $path_info['extension'] ) {
		wp_send_json(
			array( 'error' => esc_html__( 'Only a JSON file allowed', 'masterstudy-lms-learning-management-system-pro' ) )
		);
	}

	$file_content = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$file_content = json_decode( $file_content, true );

	if ( empty( $file_content['web'] ) ||
		empty( $file_content['web']['client_id'] ) ||
		empty( $file_content['web']['project_id'] ) ||
		empty( $file_content['web']['auth_uri'] ) ||
		empty( $file_content['web']['token_uri'] ) ||
		empty( $file_content['web']['client_secret'] ) ||
		empty( $file_content['web']['redirect_uris'] ) ||
		empty( $file_content['web']['auth_provider_x509_cert_url'] )
	) {
		wp_send_json_error(
			array( 'error' => esc_html__( 'Wrong JSON file', 'masterstudy-lms-learning-management-system-pro' ) )
		);
	}

	update_option( GoogleOpenAuth::CONFIG_NAME, $file_content );
	update_user_meta( get_current_user_id(), GoogleOpenAuth::CONFIG_NAME, $file_content );

	$consent_screen_url = ( new GoogleOpenAuth() )->get_consent_screen_url();

	update_user_meta( get_current_user_id(), 'gm_consent_screen_url', $consent_screen_url );

	wp_send_json(
		array(
			'success' => esc_html__( 'JSON file uploaded, reloading...', 'masterstudy-lms-learning-management-system-pro' ),
			'url'     => $consent_screen_url,
		)
	);
}
add_action( 'wp_ajax_gm_upload_credentials_ajax', 'masterstudy_lms_google_meet_upload_credentials' );

/**
 * Change Google Meet account
 */
function masterstudy_lms_google_meet_account_changed() {
	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['nonce'], 'stm-lms-gm-nonce' ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Forbidden', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);

		wp_die();
	}

	wp_send_json(
		array(
			'success' => esc_html__( 'Change Account Url', 'masterstudy-lms-learning-management-system-pro' ),
			'url'     => ( new GoogleOpenAuth() )->get_consent_screen_url(),
		)
	);
}
add_action( 'wp_ajax_stm_gm_account_changed_action', 'masterstudy_lms_google_meet_account_changed' );

/**
 * Reset Google Meet credentials
 */
function masterstudy_lms_google_meet_reset_credentials() {
	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['nonce'], 'stm-lms-gm-nonce' ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Forbidden', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);

		wp_die();
	}

	delete_option( GoogleOpenAuth::CONFIG_NAME );
	delete_option( GoogleOpenAuth::TOKEN_NAME );

	$current_user_id = get_current_user_id();

	delete_user_meta( $current_user_id, GoogleOpenAuth::TOKEN_NAME );
	delete_user_meta( $current_user_id, GoogleOpenAuth::CONFIG_NAME );

	wp_send_json(
		array(
			'success' => esc_html__( 'Access Token and JSON Config revoked', 'masterstudy-lms-learning-management-system-pro' ),
			'url'     => admin_url( 'admin.php?page=google_meet_settings' ),
		)
	);
}
add_action( 'wp_ajax_stm_gm_reset_credentials_action', 'masterstudy_lms_google_meet_reset_credentials' );

/**
 * Get Google Meet by ID
 */
function masterstudy_lms_get_google_meet_by_id() {
	check_ajax_referer( 'gm_front_meet_ajax', 'nonce', false );

	$post_id      = intval( $_POST['post_id'] ?? '' );
	$author_id    = get_post_field( 'post_author', $post_id );
	$author_email = get_the_author_meta( 'user_email', $author_id );

	wp_send_json(
		array(
			'success'    => esc_html__( 'Loading Google Meet Lesson...', 'masterstudy-lms-learning-management-system-pro' ),
			'meetData'   => get_post_meta( $post_id ),
			'meet_title' => get_the_title( $post_id ),
			'meet_host'  => $author_email,
		)
	);
}
add_action( 'wp_ajax_gm_get_meet_by_id_ajax', 'masterstudy_lms_get_google_meet_by_id' );

/**
 * Delete Google Meet
 */
function masterstudy_lms_delete_google_meet() {
	check_ajax_referer( 'gm_front_meet_ajax', 'nonce', false );

	$post_id = intval( $_POST['post_id'] ?? 0 );
	if ( empty( $post_id ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Meeting not found', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);
	}

	if ( ! current_user_can( 'delete_post', $post_id ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Forbidden', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);
	}

	if ( PostType::GOOGLE_MEET !== get_post_type( $post_id ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Invalid meeting', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);
	}

	$deleted_google_event = GoogleCalendarEvent::delete_google_event( $post_id );
	if ( ! $deleted_google_event ) {
		$error_message = get_post_meta( $post_id, 'google_meet_last_error', true );
		delete_post_meta( $post_id, 'google_meet_last_error' );

		wp_send_json_error(
			array(
				'error' => ! empty( $error_message ) ? sanitize_text_field( $error_message ) : esc_html__( 'Unable to delete Google Meet event. Please try again.', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);
	}

	wp_delete_post( $post_id, true );

	wp_send_json(
		array(
			'success' => esc_html__( 'Meeting deleted', 'masterstudy-lms-learning-management-system-pro' ),
		)
	);
}
add_action( 'wp_ajax_gm_delete_meet_ajax', 'masterstudy_lms_delete_google_meet' );

/**
 * Save Google Meet settings
 */
function masterstudy_lms_save_google_meet_settings() {
	check_ajax_referer( 'gm_front_meet_ajax', 'nonce', false );
	$meeting_meta = array(
		'timezone'     => sanitize_text_field( wp_unslash( $_POST['timezone'] ?? '' ) ),
		'reminder'     => sanitize_text_field( wp_unslash( $_POST['reminder'] ?? '' ) ),
		'send_updates' => sanitize_text_field( wp_unslash( $_POST['send_updates'] ?? '' ) ),
	);
	update_user_meta( get_current_user_id(), 'frontend_instructor_google_meet_settings', $meeting_meta );

	wp_send_json(
		array(
			'success' => esc_html__( 'Google Meet settings saved', 'masterstudy-lms-learning-management-system-pro' ),
		)
	);
}
add_action( 'wp_ajax_gm_save_settings_ajax', 'masterstudy_lms_save_google_meet_settings' );

/**
 * Reset Google Meet settings
 */
function masterstudy_lms_reset_google_meet_settings() {
	check_ajax_referer( 'gm_front_meet_ajax', 'nonce', false );

	$is_change_account = sanitize_text_field( wp_unslash( $_POST['changeAccount'] ?? '' ) );
	$current_user_id   = get_current_user_id();

	if ( $is_change_account ) {
		wp_send_json(
			array(
				'success' => esc_html__( 'Loading Google Consent Screen... ', 'masterstudy-lms-learning-management-system-pro' ),
				'url'     => get_user_meta( $current_user_id, 'gm_consent_screen_url', true ),
			)
		);
	}

	delete_user_meta( $current_user_id, 'frontend_instructor_google_meet_settings' );
	delete_user_meta( $current_user_id, GoogleOpenAuth::TOKEN_NAME );
	delete_user_meta( $current_user_id, GoogleOpenAuth::CONFIG_NAME );

	wp_send_json(
		array(
			'success' => esc_html__( 'Reset credentials in progress', 'masterstudy-lms-learning-management-system-pro' ),
		)
	);
}
add_action( 'wp_ajax_gm_front_reset_settings_ajax', 'masterstudy_lms_reset_google_meet_settings' );

/**
 * Create Google Meet
 */
function masterstudy_lms_create_google_meet() {
	check_ajax_referer( 'gm_front_meet_ajax', 'nonce', false );

	$meeting_name = $_POST['name'] ?? '';
	$is_edit      = $_POST['is_edit'] ?? '';
	$post_id      = $_POST['google_meet_id'] ?? '';

	$start_date_time = ! empty( $_POST['front_start_date_time'] ) ? strtotime( $_POST['front_start_date_time'] . ':00' ) : time();
	$end_date_time   = ! empty( $_POST['front_end_date_time'] ) ? strtotime( $_POST['front_end_date_time'] . ':00' ) : time();

	$post_data = array(
		'post_title'  => $meeting_name,
		'post_type'   => 'stm-google-meets',
		'post_status' => 'publish',
	);

	$is_reload = false;

	if ( $is_edit && ! empty( $post_id ) ) {
		$is_reload       = true;
		$post_data['ID'] = $post_id;
	}

	$post_id = wp_insert_post( $post_data );
	if ( is_wp_error( $post_id ) || empty( $post_id ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Unable to save meeting. Please try again.', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);
	}

	if ( 'true' !== $is_edit ) {
		update_post_meta( $post_id, 'stm_gma_summary', sanitize_text_field( $_POST['stm_gma_summary'] ?? '' ) );
		update_post_meta( $post_id, 'stm_gma_timezone', sanitize_text_field( $_POST['stm_gma_timezone'] ?? '' ) );
	}

	update_post_meta( $post_id, 'stm_gma_start_date', $start_date_time * 1000 );
	update_post_meta( $post_id, 'stm_gma_start_time', gmdate( 'H:i', $start_date_time ) );
	update_post_meta( $post_id, 'stm_gma_end_date', $end_date_time * 1000 );
	update_post_meta( $post_id, 'stm_gma_end_time', gmdate( 'H:i', $end_date_time ) );

	$google_meet_error = get_post_meta( $post_id, 'google_meet_last_error', true );
	if ( ! empty( $google_meet_error ) ) {
		delete_post_meta( $post_id, 'google_meet_last_error' );

		if ( 'true' !== $is_edit ) {
			wp_delete_post( $post_id, true );
		}

		wp_send_json_error(
			array(
				'error' => sanitize_text_field( $google_meet_error ),
			)
		);
	}

	$meeting_table_data = \STM_LMS_Templates::load_lms_template(
		'google-meet/meeting-table-data',
		array( 'meeting_id' => $post_id )
	);

	wp_send_json(
		array(
			'success'    => esc_html__( 'Meeting Saved', 'masterstudy-lms-learning-management-system-pro' ),
			'table_data' => $meeting_table_data,
			'is_reload'  => $is_reload,
		)
	);
}
add_action( 'wp_ajax_gm_create_new_event_front', 'masterstudy_lms_create_google_meet' );

/**
 * Sync Google Meets with Calendar
 */
function masterstudy_lms_admin_sync_meetings() {

	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['nonce'], 'stm-lms-gm-nonce' ) ) {
		wp_send_json_error(
			array(
				'error' => esc_html__( 'Forbidden', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);

		wp_die();
	}

	$meetings_ids = get_posts(
		array(
			'post_type'      => PostType::GOOGLE_MEET,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'google_meet_id',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => 'google_meet_id',
					'value'   => '',
					'compare' => '!=',
				),
			),
		),
	);

	$curriculum_repo = new CurriculumRepository();
	foreach ( $meetings_ids as $meeting_post_id ) {
		if ( ! empty( get_post_meta( $meeting_post_id, 'stm_gma_attendees', true ) ) ) {
			$meeting_course_ids = $curriculum_repo->get_lesson_course_ids( $meeting_post_id );
			$new_attendees      = array();

			foreach ( $meeting_course_ids as $meeting_course_id ) {
				$course_attendees = stm_lms_get_course_users( $meeting_course_id );

				foreach ( $course_attendees as $course_attendee ) {
					$user_data = get_userdata( $course_attendee['user_id'] );

					if ( ! empty( $user_data->user_email ) ) {
						$new_attendees[] = new Google_Service_Calendar_EventAttendee( array( 'email' => $user_data->user_email ) );
					}
				}
			}
			if ( ! empty( $new_attendees ) ) {
				GoogleCalendarEvent::add_users_to_event( $new_attendees, $meeting_post_id );
			}
		}
	}

	wp_send_json(
		array(
			'success' => esc_html__( 'Meeting Synchronized', 'masterstudy-lms-learning-management-system-pro' ),
		)
	);
}
add_action( 'wp_ajax_masterstudy_lms_admin_sync_meetings', 'masterstudy_lms_admin_sync_meetings' );
