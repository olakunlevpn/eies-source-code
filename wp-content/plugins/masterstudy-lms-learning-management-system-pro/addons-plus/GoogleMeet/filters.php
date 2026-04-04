<?php

use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleOpenAuth;

/**
 * Hide Google Meet content from course items
 */
function masterstudy_lms_hide_google_meet( $show, $post_id, $item_id ) {
	if ( masterstudy_lms_is_google_meet( $item_id ) ) {
		return false;
	}

	return $show;
}
add_filter( 'stm_lms_show_item_content', 'masterstudy_lms_hide_google_meet', 10, 3 );

/**
 * Course item content
 */
function masterstudy_lms_google_meet_content( $content, $post_id, $item_id, $data ) {
	if ( masterstudy_lms_is_google_meet( $item_id ) ) {
		ob_start();
		STM_LMS_Templates::show_lms_template( 'course-player/google-meet', compact( 'post_id', 'item_id', 'data' ) );
		return ob_get_clean();
	}
	return $content;
}
add_filter( 'stm_lms_course_item_content', 'masterstudy_lms_google_meet_content', 10, 4 );

/**
 * Adding Google Meet menu item
 */
function masterstudy_lms_google_meet_menu_item( $menus ) {
	$current_slug = masterstudy_get_current_account_slug();

	$menus[] = array(
		'order'        => 41,
		'id'           => 'google_meets',
		'slug'         => 'google-meets',
		'lms_template' => 'stm-lms-google-meets',
		'menu_title'   => esc_html__( 'Google Meet', 'masterstudy-lms-learning-management-system-pro' ),
		'menu_icon'    => 'stmlms-menu-google-meet',
		'menu_url'     => ms_plugin_user_account_url( 'google-meets' ),
		'menu_place'   => 'main',
		'is_active'    => 'google-meets' === $current_slug,
		'section'      => 'communication',
	);

	return $menus;
}

$user      = new \stmLms\Classes\Models\StmUser( get_current_user_id() );
$user_role = $user->getRole();
if ( is_admin() || ! current_user_can( 'administrator' ) && STM_LMS_Instructor::is_instructor( get_current_user_id() ) || $user_role && 'stm_lms_instructor' === $user_role['id'] ) {
	add_filter( 'stm_lms_menu_items', 'masterstudy_lms_google_meet_menu_item' );
}

/**
 * Add Google Meet post type
 */
function masterstudy_lms_add_google_meet_post_type( $post_types ) {
	$post_types[] = 'stm-google-meets';

	return $post_types;
}
add_filter( 'stm_lms_post_types', 'masterstudy_lms_add_google_meet_post_type' );

/**
 * Add Google Meet post type detials
 */
function masterstudy_lms_add_google_meet_post_type_array( $post_types ) {
	$user_id                       = get_current_user_id();
	$google_api_credentials        = get_user_meta( $user_id, GoogleOpenAuth::TOKEN_NAME, true );
	$google_api_credentials_config = get_user_meta( $user_id, GoogleOpenAuth::CONFIG_NAME, true );

	if ( isset( $google_api_credentials['access_token'], $google_api_credentials_config['web']['client_id'], $google_api_credentials_config['web']['client_secret'] ) ) {
		$post_types['stm-google-meets'] = array(
			'single' => esc_html__( 'Meeting', 'masterstudy-lms-learning-management-system-pro' ),
			'plural' => esc_html__( 'Meetings', 'masterstudy-lms-learning-management-system-pro' ),
			'args'   => array(
				'public'              => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_menu'        => 'admin.php?page=stm-lms-settings',
				'supports'            => array( 'title' ),
				'capability_type'     => 'stm_lms_post',
				'capabilities'        => array(
					'publish_posts'       => 'publish_stm_lms_posts',
					'edit_posts'          => 'edit_stm_lms_posts',
					'delete_posts'        => 'delete_stm_lms_posts',
					'edit_post'           => 'edit_stm_lms_post',
					'delete_post'         => 'delete_stm_lms_post',
					'read_post'           => 'read_stm_lms_posts',
					'edit_others_posts'   => 'edit_others_stm_lms_posts',
					'delete_others_posts' => 'delete_others_stm_lms_posts',
					'read_private_posts'  => 'read_private_stm_lms_posts',
				),
				'menu_position'       => 1,
			),
		);

		return $post_types;
	}

	return $post_types;
}
add_filter( 'stm_lms_post_types_array', 'masterstudy_lms_add_google_meet_post_type_array' );

/**
 * Add Google Meet post type admin columns
 */
function masterstudy_lms_add_google_meet_post_type_columns( $columns ) {
	$columns['gm_date']        = esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' );
	$columns['gm_meeting_url'] = esc_html__( 'Meeting URL', 'masterstudy-lms-learning-management-system-pro' );
	$columns['gm_host']        = esc_html__( 'Meeting Host', 'masterstudy-lms-learning-management-system-pro' );
	$columns['gm_actions']     = esc_html__( 'Actions', 'masterstudy-lms-learning-management-system-pro' );

	if ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] ) { // phpcs:ignore
		unset( $columns['gm_actions'] );
	}

	unset( $columns['date'] );

	return $columns;
}
add_filter( 'manage_stm-google-meets_posts_columns', 'masterstudy_lms_add_google_meet_post_type_columns' );

function masterstudy_lms_save_gm_fields( $post_id ) {
// phpcs:ignore WordPress.Security.NonceVerification
	if ( ( '/wp-admin/post-new.php?post_type=stm-google-meets' === wp_get_referer() ) && ( empty( $_POST['stm_gma_start_date'] ) || empty( $_POST['stm_gma_end_date'] ) ) ) {
		wp_die( 'Please fill the empty fields. <a href="' . esc_url( wp_get_referer() ) . '">Go back</a>' );
	}
}
add_action( 'pre_post_update', 'masterstudy_lms_save_gm_fields', 10, 1 );
