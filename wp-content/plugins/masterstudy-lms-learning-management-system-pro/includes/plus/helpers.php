<?php
function masterstudy_lms_course_preview_get_fields_meta_map(): array {
	return array(
		'video_type'   => 'video_type',
		'external_url' => 'external_url',
		'vdocipher_id' => 'vdocipher_id',
		'embed_ctx'    => 'embed_ctx',
		'shortcode'    => 'shortcode',
		'youtube_url'  => 'youtube_url',
		'video'        => 'video',
		'video_poster' => 'video_poster',
		'video_width'  => 'video_width',
		'vimeo_url'    => 'vimeo_url',
	);
}

function masterstudy_lms_course_preview_get_wp_attachment_by_id( ?int $attachment_id ): ?array {
	$attachment = get_post( $attachment_id );
	if ( $attachment ) {
		return array(
			'id'    => $attachment->ID,
			'title' => $attachment->post_title,
			'type'  => get_post_mime_type( $attachment->ID ),
			'url'   => wp_get_attachment_url( $attachment->ID ),
		);
	}

	return null;
}

function masterstudy_lms_analytics_menu() {
	add_submenu_page(
		'revenue',
		esc_html__( 'Engagement', 'masterstudy-lms-learning-management-system-pro' ),
		esc_html__( 'Engagement', 'masterstudy-lms-learning-management-system-pro' ),
		'manage_options',
		'engagement',
		'masterstudy_lms_analytics_engagement_page'
	);

	add_submenu_page(
		'revenue',
		esc_html__( 'Users', 'masterstudy-lms-learning-management-system-pro' ),
		esc_html__( 'Users', 'masterstudy-lms-learning-management-system-pro' ),
		'manage_options',
		'users',
		'masterstudy_lms_analytics_users_page'
	);

	if ( STM_LMS_Options::get_option( 'course_tab_reviews', true ) ) {
		add_submenu_page(
			'revenue',
			esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system-pro' ),
			esc_html__( 'Reviews', 'masterstudy-lms-learning-management-system-pro' ),
			'manage_options',
			'reviews',
			'masterstudy_lms_analytics_reviews_page'
		);
	}

	global $submenu;

	if ( isset( $submenu['revenue'][0] ) ) {
		$submenu['revenue'][0][0] = esc_html__( 'Revenue', 'masterstudy-lms-learning-management-system-pro' );
	}
}
add_action( 'admin_menu', 'masterstudy_lms_analytics_menu' );

function masterstudy_lms_analytics_engagement_page() {
	masterstudy_lms_show_analytics_template( 'analytics/engagement' );
}

function masterstudy_lms_analytics_users_page() {
	masterstudy_lms_show_analytics_template( 'analytics/users' );
}

function masterstudy_lms_analytics_reviews_page() {
	masterstudy_lms_show_analytics_template( 'analytics/reviews' );
}

function masterstudy_lms_show_analytics_template( $default_template ) {
	if ( ! empty( $_GET['course_id'] ) ) {
		STM_LMS_Templates::show_lms_template( 'analytics/course' );

		return;
	}

	if ( ! empty( $_GET['user_id'] ) ) {
		$role = ! empty( $_GET['role'] ) ? sanitize_text_field( wp_unslash( $_GET['role'] ) ) : 'student';

		STM_LMS_Templates::show_lms_template( "analytics/$role" );

		return;
	}

	STM_LMS_Templates::show_lms_template( $default_template );
}
