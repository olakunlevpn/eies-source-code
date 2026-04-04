<?php

new STM_LMS_User_Manager_Import_Users();

class STM_LMS_User_Manager_Import_Users {

	public function __construct() {
		add_action(
			'wp_ajax_stm_lms_dashboard_import_users_to_course',
			array( $this, 'import_users' )
		);
	}

	public function import_users() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Forbidden' ), 403 );
		}

		check_ajax_referer( 'stm_lms_dashboard_import_users_to_course', 'nonce' );

		$request_body = file_get_contents( 'php://input' );
		$data         = json_decode( $request_body, true );

		$imported_users = $data['users'] ?? array();
		$course_id      = (int) ( $data['course_id'] ?? 0 );

		if ( ! $course_id || empty( $imported_users ) ) {
			wp_send_json_error( array( 'message' => 'Invalid payload' ), 400 );
		}

		$output = array(
			'not_enrolled_users'    => array(),
			'new_enrolled_users'    => array(),
			'incorrect_email_users' => array(),
			'before_enrolled_users' => array(),
		);

		$course_users = stm_lms_get_course_users( $course_id );
		$enrolled_ids = array();

		foreach ( $course_users as $course_user ) {
			$user_id = (int) ( $course_user['user_id'] ?? 0 );
			if ( $user_id ) {
				$enrolled_ids[ $user_id ] = true;
			}
		}

		foreach ( $imported_users as $imported_user ) {
			$email = $imported_user['email'] ?? '';

			if ( ! is_email( $email ) ) {
				$output['incorrect_email_users'][] = $imported_user;
				continue;
			}

			$user = get_user_by( 'email', $email );

			if ( $user && isset( $enrolled_ids[ (int) $user->ID ] ) ) {
				$output['before_enrolled_users'][] = $imported_user;
				continue;
			}

			$adding_student = STM_LMS_Instructor::add_student_to_course(
				array( $course_id ),
				array( $email )
			);

			if ( is_array( $adding_student ) && empty( $adding_student['error'] ) ) {
				$output['new_enrolled_users'][] = $imported_user;
				$this->update_user_names( $email, $imported_user );
			} else {
				$output['not_enrolled_users'][] = $imported_user;
			}
		}

		wp_send_json_success( $output );
	}


	public function update_user_names( $email, $user_data = array() ) {
		$first_name = sanitize_text_field(
			trim( $user_data['first_name'] ?? '' )
		);
		$last_name  = sanitize_text_field(
			trim( $user_data['last_name'] ?? '' )
		);

		if ( ! $first_name && ! $last_name ) {
			return;
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			return;
		}

		wp_update_user(
			array(
				'ID'         => $user->ID,
				'first_name' => $first_name,
				'last_name'  => $last_name,
			)
		);
	}
}
