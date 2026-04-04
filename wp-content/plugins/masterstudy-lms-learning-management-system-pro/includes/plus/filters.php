<?php

// Load Pro Plus Addons
add_filter(
	'masterstudy_lms_plugin_addons',
	function ( $addons ) {
		return array_merge(
			$addons,
			array(
				new MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\GoogleMeet(),
				new MasterStudy\Lms\Pro\AddonsPlus\ComingSoon\ComingSoon(),
				new MasterStudy\Lms\Pro\AddonsPlus\QuestionMedia\QuestionMedia(),
				new MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\SocialLogin(),
				new MasterStudy\Lms\Pro\AddonsPlus\AudioLesson\AudioLesson(),
				new MasterStudy\Lms\Pro\AddonsPlus\Grades\Grades(),
				new MasterStudy\Lms\Pro\AddonsPlus\AiLab\AiLab(),
				new MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Subscriptions(),
			)
		);
	}
);

function masterstudy_lms_pro_course_preview_serializer_callback( array $data ): array {
	foreach ( masterstudy_lms_course_preview_get_fields_meta_map() as $property => $meta_key ) {
		if ( 'video_poster' === $meta_key || 'video' === $meta_key ) {
			$data[ $property ] = masterstudy_lms_course_preview_get_wp_attachment_by_id( (int) ( get_post_meta( $data['id'], $meta_key, true ) ) );
		} else {
			$data[ $property ] = get_post_meta( $data['id'], $meta_key, true );
		}
	}

	return $data;
}
add_filter( 'masterstudy_lms_pro_course_serialize', 'masterstudy_lms_pro_course_preview_serializer_callback', 10, 1 );

function masterstudy_analytics_menu_item( $menus ) {
	$current_slug = masterstudy_get_current_account_slug();

	if ( ( STM_LMS_Instructor::is_instructor() || current_user_can( 'administrator' ) ) && STM_LMS_Options::get_option( 'instructors_reports', true ) ) {
		$menus[] = array(
			'order'        => 155,
			'id'           => 'analytics',
			'slug'         => 'analytics',
			'lms_template' => 'analytics/revenue',
			'menu_title'   => esc_html__( 'Analytics', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_icon'    => 'stmlms-menu-analytics',
			'menu_url'     => ms_plugin_user_account_url( 'analytics' ),
			'menu_place'   => 'main',
			'is_active'    => 'analytics' === $current_slug,
			'section'      => 'progress',
		);
	}

	if ( STM_LMS_Instructor::is_instructor() ) {
		$menus[] = array(
			'order'        => 156,
			'id'           => 'sales',
			'slug'         => 'sales',
			'lms_template' => 'my-sales',
			'menu_title'   => esc_html__( 'My Sales', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_icon'    => 'stmlms-menu-sales',
			'menu_url'     => ms_plugin_user_account_url( 'sales' ),
			'menu_place'   => 'main',
			'is_active'    => 'sales' === $current_slug,
			'section'      => 'finance',
		);
	}

	return $menus;
}
add_filter( 'stm_lms_menu_items', 'masterstudy_analytics_menu_item' );

function masterstudy_show_analytics_link( $post, $id ) {
	$post['analytics_link'] = STM_LMS_User::login_page_url() . "analytics/course/$id";

	return $post;
}
add_filter( 'masterstudy_add_analytics_link', 'masterstudy_show_analytics_link', 10, 2 );
