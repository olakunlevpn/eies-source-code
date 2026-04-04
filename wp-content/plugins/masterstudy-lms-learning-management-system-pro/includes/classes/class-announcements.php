<?php

new STM_LMS_Pro_Announcements();

class STM_LMS_Pro_Announcements {
	public function __construct() {
		add_filter( 'stm_lms_menu_items', array( $this, 'add_menu_item' ) );
		add_action( 'wp_ajax_stm_lms_create_announcement', array( $this, 'create_announcement' ) );
	}

	public function add_menu_item( $menus ) {
		if ( STM_LMS_Instructor::is_instructor( get_current_user_id() ) ) {
			$current_slug = masterstudy_get_current_account_slug();

			$menus[] = array(
				'order'        => 20,
				'id'           => 'announcement',
				'slug'         => 'announcement',
				'lms_template' => 'stm-lms-instructor-announcement',
				'menu_title'   => esc_html__( 'Announcement', 'masterstudy-lms-learning-management-system-pro' ),
				'menu_icon'    => 'stmlms-menu-announcement',
				'menu_url'     => ms_plugin_user_account_url( 'announcement' ),
				'menu_place'   => 'main',
				'is_active'    => 'announcement' === $current_slug,
				'section'      => 'communication',
			);
		}

		return $menus;
	}

	public function create_announcement() {
		check_ajax_referer( 'stm_lms_create_announcement', 'nonce' );

		$current_user = STM_LMS_User::get_current_user();
		$user_id      = $current_user['id'];

		$response = array(
			'status'  => 'success',
			'message' => esc_html__( 'Announcement has been sent to course students.', 'masterstudy-lms-learning-management-system-pro' ),
		);

		if ( empty( $_GET['post_id'] ) || empty( $_GET['mail'] ) ) {
			$response['status']  = 'error';
			$response['message'] = esc_html__( 'Please fill all fields', 'masterstudy-lms-learning-management-system-pro' );

			wp_send_json( $response );
		}

		$post_id = intval( $_GET['post_id'] );
		$mail    = nl2br( wp_strip_all_tags( $_GET['mail'] ) );

		/*get post author*/
		$post_author_id = get_post_field( 'post_author', $post_id );

		if ( intval( $post_author_id ) === intval( $user_id ) ) {
			do_action( 'stm_lms_announcement_ready_to_send', $post_id, $user_id, $mail );

			$users = stm_lms_get_course_users( $post_id, array( 'user_id' ) );

			foreach ( $users as $user ) {
				$user_id   = $user['user_id'];
				$user_info = get_userdata( $user_id );

				$template = wp_kses_post(
					'Hi {{user_login}}, <br>
					Exciting news! {{instructor_name}} has just published a new announcement for your course, {{course_title}}.<br>
					Announcement: {{mail}} <br>
					Stay tuned for more updates!'
				);

				$email_data_announcement = array(
					'user_login'      => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
					'instructor_name' => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $post_author_id ),
					'course_title'    => get_the_title( $post_id ),
					'mail'            => $mail,
					'blog_name'       => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
					'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
					'date'            => gmdate( 'Y-m-d H:i:s' ),
					'course_url'      => \MS_LMS_Email_Template_Helpers::link( get_permalink( $post_id ) ),
				);

				$message = \MS_LMS_Email_Template_Helpers::render( $template, $email_data_announcement );

				$subject = esc_html__( 'New Announcement in {{course_title}}', 'masterstudy-lms-learning-management-system-pro' );
				$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data_announcement );

				STM_LMS_Helpers::send_email(
					$user_info->user_email,
					$subject,
					$message,
					'stm_lms_announcement_from_instructor',
					$email_data_announcement
				);
			}

			wp_send_json( $response );
		}
	}
}
