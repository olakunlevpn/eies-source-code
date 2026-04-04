<?php 
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