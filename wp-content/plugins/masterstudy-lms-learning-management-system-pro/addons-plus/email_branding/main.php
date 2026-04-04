<?php

add_filter( 'stm_lms_email_manager_settings', 'stm_lms_email_manager_settings_plus', 999, 1 );
function stm_lms_email_manager_settings_plus( $settings ) {
	$email_textarea = 'hint_textarea';
	if ( defined( 'STM_WPCFTO_VERSION' ) && STM_LMS_Helpers::is_pro_plus() ) {
		$email_textarea = 'trumbowyg';
	}

	$email_branding = array(
		'email_template' => array(
			'name'   => 'Email Branding',
			'fields' => array(
				'controll_id'                           => array(
					'type'             => 'group_title',
					'label'            => esc_html__( 'Default Email Template', 'masterstudy-lms-learning-management-system-pro' ),
					'description'      => esc_html__( 'By default, emails will be sent as displayed in Preview. Uploading a custom logo, and background images for the header & footer will cover the default template.', 'masterstudy-lms-learning-management-system-pro' ),
					'preview'          => STM_LMS_PRO_URL . 'addons/email_manager/images/template.png',
					'preview_position' => 'preview_bottom custom_email_preview',
					'dependency'       => array(
						'key'   => 'stm_lms_email_template_branding',
						'value' => 'not_empty',
					),
				),
				'stm_lms_email_template_header_name'    => array(
					'type'         => 'text',
					'label'        => esc_html__( 'Sender Name', 'masterstudy-lms-learning-management-system-pro' ),
					'description'  => esc_html__( 'The name that emails are sent from', 'masterstudy-lms-learning-management-system-pro' ),
					'value'        => esc_html__( 'Masterstudy', 'masterstudy-lms-learning-management-system-pro' ),
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_header_email'   => array(
					'type'         => 'text',
					'label'        => esc_html__( 'Sender Email Address', 'masterstudy-lms-learning-management-system-pro' ),
					'description'  => esc_html__( 'The email address that emails are sent from', 'masterstudy-lms-learning-management-system-pro' ),
					'value'        => masterstudy_lms_get_default_email(),
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_hf'             => array(
					'group'      => 'started',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Header & Footer', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => 'stm_lms_email_template_branding',
						'value' => 'not_empty',
					),
				),
				'stm_lms_email_template_hf_logo'        => array(
					'type'         => 'image',
					'description'  => esc_html__( 'Recommended size: 200x35 pixels, Max height: 40px; File Support: jpg, .jpeg or .png.', 'masterstudy-lms-learning-management-system-pro' ),
					'label'        => esc_html__( 'Logo', 'masterstudy-lms-learning-management-system-pro' ),
					'url'          => '933',
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_hf',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_hf_header_bg'   => array(
					'type'         => 'image',
					'description'  => esc_html__( 'Recommended size: 700x95 pixels, Max height: 100px; File Support: jpg, .jpeg or .png.', 'masterstudy-lms-learning-management-system-pro' ),
					'label'        => esc_html__( 'Header Background', 'masterstudy-lms-learning-management-system-pro' ),
					'url'          => 'http://lms.loc/wp-content/uploads/2022/11/email_logo-1.png',
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_hf',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_hf_footer_bg'   => array(
					'type'         => 'image',
					'description'  => esc_html__( 'Recommended size: 700x155 pixels, Max height: 160px; File Support: jpg, .jpeg or .png.', 'masterstudy-lms-learning-management-system-pro' ),
					'label'        => esc_html__( 'Footer Background', 'masterstudy-lms-learning-management-system-pro' ),
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_hf',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_hf_entire_bg'   => array(
					'type'         => 'color',
					'label'        => esc_html__( 'Entire Background Color', 'masterstudy-lms-learning-management-system-pro' ),
					'group'        => 'ended',
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_hf',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_reply'          => array(
					'group'      => 'started',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Copyrights', 'masterstudy-lms-learning-management-system-pro' ),
					'value'      => true,
					'dependency' => array(
						'key'   => 'stm_lms_email_template_branding',
						'value' => 'not_empty',
					),
				),
				'stm_lms_email_template_reply_text'     => array(
					'type'         => 'text',
					'label'        => esc_html__( 'Additional Textbox with Icon', 'masterstudy-lms-learning-management-system-pro' ),
					'value'        => esc_html__( 'Reply to this email to communicate with the instructor.', 'masterstudy-lms-learning-management-system-pro' ),
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_reply',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_reply_icon'     => array(
					'type'         => 'image',
					'description'  => esc_html__( 'Recommended size: 18x7 pixels, Max height: 8px; File Support: jpg, .jpeg or .png.', 'masterstudy-lms-learning-management-system-pro' ),
					'label'        => esc_html__( 'Reply Text Icon', 'masterstudy-lms-learning-management-system-pro' ),
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_reply',
							'value' => 'not_empty',
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_reply_textarea' => array(
					'type'         => $email_textarea,
					'group'        => 'ended',
					'label'        => esc_html__( 'Copyrights', 'masterstudy-lms-learning-management-system-pro' ),
					'value'        => sprintf(
						esc_html__( 'Masterstudy © %s All Rights Reserved.', 'masterstudy-lms-learning-management-system-pro' ),
						gmdate( 'Y' )
					),
					'dependency'   => array(
						array(
							'key'   => 'stm_lms_email_template_branding',
							'value' => 'not_empty',
						),
						array(
							'key'   => 'stm_lms_email_template_reply',
							'value' => 'not_empty',
						),
					),
					'vars'    => array(
						'blog_name'  => esc_html__(
							'Blog name',
							'masterstudy-lms-learning-management-system-pro'
						),
						'site_url'  => esc_html__(
							'Site URL',
							'masterstudy-lms-learning-management-system-pro'
						),
						'year' => esc_html__(
							'Year',
							'masterstudy-lms-learning-management-system-pro'
						),
					),
					'dependencies' => '&&',
				),
				'stm_lms_email_template_branding'       => array(
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Email Branding', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'Disable if you want to use a custom HTML template with your own Header & Footer.', 'masterstudy-lms-learning-management-system-pro' ),
					'value'       => true,
				),
			),
		),
	);

	return array_merge( $email_branding, $settings );
}

add_filter( 'stm_lms_filter_email_data', 'stm_lms_email_filter_email_data', 100, 1 );
function stm_lms_email_filter_email_data( $data ) {

	$settings = array();

	if ( class_exists( 'STM_LMS_Email_Manager' ) ) {
		$settings = STM_LMS_Email_Manager::stm_lms_get_settings();
	}

	if ( ! empty( $settings['stm_lms_email_template_branding'] ) ) {
		$data['message'] = STM_LMS_Templates::load_lms_template(
			'emails/main',
			array(
				'message'       => $data['message'],
				'subject'       => $data['subject'],
				'email_manager' => $settings,
			)
		);
	}

	return $data;
}

add_filter( 'wp_mail_from_name', 'masterstudy_lms_get_from_name' );
function masterstudy_lms_get_from_name() {
	$settings = ( class_exists( 'STM_LMS_Email_Manager' ) ) ? STM_LMS_Email_Manager::stm_lms_get_settings() : array();

	return $settings['stm_lms_email_template_header_name'] ?? get_option( 'blogname' );
}

/**
 * Get default WP Email
 *
 * @return string
 */
function masterstudy_lms_get_default_email() {
	if ( version_compare( get_bloginfo( 'version' ), '5.5-alpha', '<' ) ) {
		$sitename = ! empty( $_SERVER['SERVER_NAME'] ) ?
			strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ) :
			wp_parse_url( get_home_url( get_current_blog_id() ), PHP_URL_HOST );
	} else {
		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
	}

	if ( 'www.' === substr( $sitename, 0, 4 ) ) {
		$sitename = substr( $sitename, 4 );
	}

	return 'admin@' . $sitename;
}
