<?php
/** @var \MasterStudy\Lms\Plugin $plugin */

// Load LMS Plugin Files
function masterstudy_lms_plus_load_lms_files( $plugin ) {
	// Load Pro Plus Routes
	$plugin->get_router()->load_routes( STM_LMS_PRO_PATH . '/rest-api/Routes/Analytics.php' );
	$plugin->get_router()->load_routes( STM_LMS_PRO_PATH . '/rest-api/Routes/LessonMarkers.php' );
	$plugin->get_router()->load_routes( STM_LMS_PRO_PATH . '/rest-api/Routes/Coupons.php' );
}
add_action( 'masterstudy_lms_plugin_loaded', 'masterstudy_lms_plus_load_lms_files' );

function masterstudy_lms_pro_update_course_video_preview( $post_id, $course ) {
	foreach ( masterstudy_lms_course_preview_get_fields_meta_map() as $property => $meta_key ) {
		if ( isset( $course[ $property ] ) && ! empty( $course[ $property ] ) ) {
			update_post_meta( $post_id, $meta_key, $course[ $property ] );
		}
		if ( 'ext_link' === $course[ $property ] || 'html' === $course[ $property ] ) {
			update_post_meta( $post_id, 'video_poster', $course['video_poster'] );
			update_post_meta( $post_id, 'video', $course['video'] ?? '' );
		}
	}
}
add_action( 'masterstudy_lms_course_video_saved', 'masterstudy_lms_pro_update_course_video_preview', 10, 2 );

function masterstudy_show_user_account_analytics_templates( $current_user ) {
	STM_LMS_Templates::show_lms_template( 'analytics/partials/short-report', array( 'current_user' => $current_user ) );
}
add_action( 'masterstudy_show_analytics_templates', 'masterstudy_show_user_account_analytics_templates' );

function masterstudy_add_admin_coupons_page() {
		add_submenu_page(
			'stm-lms-settings',
			esc_html__( 'Coupons', 'masterstudy-lms-learning-management-system-pro' ),
			'<span class="stm-lms-students-menu-title"><span class="stm-lms-menu-text">' . esc_html__( 'Coupons', 'masterstudy-lms-learning-management-system-pro' ) . '</span><span class="stm-lms-menu-status">' . esc_html__( 'new', 'masterstudy-lms-learning-management-system' ) . '</span></span>',
			'manage_options',
			'manage_coupons',
			'masterstudy_lms_render_coupons_page',
			stm_lms_addons_menu_position()
		);
}

function masterstudy_lms_render_coupons_page() {
	STM_LMS_Templates::show_lms_template( 'coupons' );
}

if ( is_ms_lms_coupons_enabled() ) {
	add_action( 'admin_menu', 'masterstudy_add_admin_coupons_page', 100001 );
}
