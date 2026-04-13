<?php

/**
 * EIES: Override Elementor demo data with real stats on the frontend.
 * This runs at render time so Elementor saves can't overwrite it.
 */
add_filter( 'elementor/frontend/the_content', 'eies_fix_homepage_data' );
function eies_fix_homepage_data( $content ) {
	if ( ! is_front_page() ) {
		return $content;
	}

	global $wpdb;

	// Fix stats counters with real data
	$students = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" );
	$courses  = (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
		'stm-courses', 'publish'
	) );

	// Replace counter values
	$content = preg_replace( '/data-value="2000"/', 'data-value="' . $students . '"', $content );
	$content = preg_replace( '/>2000</', '>' . $students . '<', $content );
	$content = preg_replace( '/data-value="950"/', 'data-value="' . $courses . '"', $content );
	$content = preg_replace( '/>950</', '>' . $courses . '<', $content );

	// Fix category sorting buttons — replace demo IDs 31-37 with real categories
	$real_cats = $wpdb->get_col(
		"SELECT t.term_id FROM {$wpdb->terms} t
		 JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
		 WHERE tt.taxonomy = 'stm_lms_course_taxonomy' AND tt.parent = 0 AND tt.count > 0
		 ORDER BY tt.count DESC LIMIT 7"
	);

	$demo_ids = array( 31, 32, 33, 34, 35, 36, 37 );
	foreach ( $demo_ids as $i => $demo_id ) {
		if ( isset( $real_cats[ $i ] ) ) {
			$real_id   = $real_cats[ $i ];
			$real_name = $wpdb->get_var( $wpdb->prepare(
				"SELECT name FROM {$wpdb->terms} WHERE term_id = %d", $real_id
			) );
			// Replace sorting button
			$content = str_replace(
				'data-id="' . $demo_id . '" class="ms_lms_courses_grid__sorting_button ">',
				'data-id="' . $real_id . '" class="ms_lms_courses_grid__sorting_button ">' . esc_html( $real_name ),
				$content
			);
		}
	}

	return $content;
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
	function theme_enqueue_styles() {
	    // Styles
		wp_enqueue_style( 'boostrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', NULL, STM_THEME_VERSION, 'all' ); 
		wp_enqueue_style( 'font-awesome-min', get_template_directory_uri() . '/assets/css/font-awesome.min.css', NULL, STM_THEME_VERSION, 'all' ); 
		wp_enqueue_style( 'font-icomoon', get_template_directory_uri() . '/assets/css/icomoon.fonts.css', NULL, STM_THEME_VERSION, 'all' ); 
        wp_enqueue_style( 'fancyboxcss', get_template_directory_uri() . '/assets/css/jquery.fancybox.css', NULL, STM_THEME_VERSION, 'all' );
        wp_enqueue_style( 'select2-min', get_template_directory_uri() . '/assets/css/select2.min.css', NULL, STM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'theme-style-less', get_template_directory_uri() . '/assets/css/styles.css', NULL, STM_THEME_VERSION, 'all' );
		
		// Animations
		if ( !wp_is_mobile() ) {
			wp_enqueue_style( 'theme-style-animation', get_template_directory_uri() . '/assets/css/animation.css', NULL, STM_THEME_VERSION, 'all' );
		}
		
		// Theme main stylesheet
		wp_enqueue_style( 'theme-style', get_stylesheet_uri(), null, STM_THEME_VERSION, 'all' );
		
		// FrontEndCustomizer
		wp_enqueue_style( 'skin_red_green', get_template_directory_uri() . '/assets/css/skins/skin_red_green.css', NULL, STM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'skin_blue_green', get_template_directory_uri() . '/assets/css/skins/skin_blue_green.css', NULL, STM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'skin_red_brown', get_template_directory_uri() . '/assets/css/skins/skin_red_brown.css', NULL, STM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'skin_custom_color', get_template_directory_uri() . '/assets/css/skins/skin_custom_color.css', NULL, STM_THEME_VERSION, 'all' );
	}