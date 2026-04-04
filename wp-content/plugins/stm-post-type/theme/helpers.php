<?php

function stm_post_type_filtered_output( $data ) {
	return apply_filters( 'stm_post_type_filtered_output', $data );
}

if ( function_exists( 'vc_add_shortcode_param' ) ) {
	// Datepicker field
	vc_add_shortcode_param( 'stm_datepicker_vc', 'stm_datepicker_vc_st', get_template_directory_uri() . '/inc/vc_extend/jquery.stmdatetimepicker.js' );
	function stm_datepicker_vc_st( $settings, $value ) {
		return '<div class="stm_datepicker_vc_field">'
			. '<input type="text" name="' . esc_attr( $settings['param_name'] ) . '" class="stm_datepicker_vc wpb_vc_param_value wpb-textinput ' .
			esc_attr( $settings['param_name'] ) . ' ' .
			esc_attr( $settings['type'] ) . '_field" type="text" value="' . esc_attr( $value ) . '" />' .
			'</div>';
	}

	// Add number field
	vc_add_shortcode_param( 'number_field', 'number_field_vc_st' );
	function number_field_vc_st( $settings, $value ) {
		return '<div class="stm_number_field_block">'
			. '<input type="number" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
			esc_attr( $settings['param_name'] ) . ' ' .
			esc_attr( $settings['type'] ) . '_field" type="text" value="' . esc_attr( $value ) . '" />' .
			'</div>'; // This is html markup that will be outputted in content elements edit form
	}
}

add_action( 'stm_is_redux_field_in_use', 'stm_is_redux_field_in_use' );

function stm_is_redux_field_in_use() {
	wp_dequeue_script( 'wpb_ace' );
	wp_deregister_script( 'wpb_ace' );
}

add_action( 'stm_redux_register_select2', 'stm_redux_register_select2' );

function stm_redux_register_select2() {
	wp_deregister_script( 'jquerySelect2' );
	wp_dequeue_script( 'jquerySelect2' );
	wp_dequeue_style( 'jquerySelect2Style' );
}

function stm_base64( $content ) {
	//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	return base64_decode( $content );
}

function stm_default_colors() {
	return array(
		'default'           => array(
			'primary_color'   => '#eab830',
			'secondary_color' => '#48a7d4',
			'link_color'      => '#48a7d4',
		),
		'online-light'      => array(
			'primary_color'   => '#4ed7a8',
			'secondary_color' => '#195ec8',
			'link_color'      => '#195ec8',
		),
		'online-dark'       => array(
			'primary_color'   => '#4aac9a',
			'secondary_color' => '#195ec8',
			'link_color'      => '#195ec8',
		),
		'academy'           => array(
			'primary_color'   => '#ff1f59',
			'secondary_color' => '#195ec8',
			'link_color'      => '#195ec8',
		),
		'course_hub'        => array(
			'primary_color'   => '#2f4371',
			'secondary_color' => '#19c895',
			'link_color'      => '#19c895',
		),
		'classic_lms'       => array(
			'primary_color'   => '#eab830',
			'secondary_color' => '#2c75e4',
			'link_color'      => '#2c75e4',
		),
		'udemy'             => array(
			'primary_color'   => '#ab30c5',
			'secondary_color' => '#2c75e4',
			'link_color'      => '#2c75e4',
		),
		'single_instructor' => array(
			'primary_color'   => '#f2b91e',
			'secondary_color' => '#2d4649',
			'link_color'      => '#2d4649',
		),
		'language_center'   => array(
			'primary_color'   => '#ff5161',
			'secondary_color' => '#ff5161',
			'link_color'      => '#053f81',
		),
		'rtl-demo'          => array(
			'primary_color'   => '#ff5161',
			'secondary_color' => '#ff5161',
			'link_color'      => '#053f81',
		),
		'buddypress-demo'   => array(
			'primary_color'   => '#ff5161',
			'secondary_color' => '#ff5161',
			'link_color'      => '#053f81',
		),
		'classic-lms-2'     => array(
			'primary_color'   => '#eab830',
			'secondary_color' => '#002e5b',
			'link_color'      => '#002e5b',
		),
		'distance-learning' => array(
			'primary_color'   => '#eab830',
			'secondary_color' => '#2c75e4',
			'link_color'      => '#2c75e4',
		),
		'cooking'           => array(
			'primary_color'   => '#eab830',
			'secondary_color' => '#2c75e4',
			'link_color'      => '#2c75e4',
		),
		'tech'              => array(
			'primary_color'   => '#00c691',
			'secondary_color' => '#042d89',
			'link_color'      => '#00c691',
		),
		'architecture'      => array(
			'primary_color'   => '#ff1f59',
			'secondary_color' => '#195ec8',
			'link_color'      => '#195ec8',
		),
	);
}

if ( ! function_exists( 'stm_get_layout' ) ) {
	function stm_get_layout() {
		return get_option( 'stm_lms_layout', '' );
	}
}

add_filter( 'stm_theme_post_date', 'stm_configurations_theme_post_date', 100, 2 );

function stm_configurations_theme_post_date( $html, $post_id ) {
	ob_start(); ?>
	<div class='date-d'><?php echo get_the_date( 'd', $post_id ); ?></div>
	<div class='date-m date-m-plugin'><?php echo get_the_date( 'M', $post_id ); ?></div>
	<?php
	return ob_get_clean();
}

add_filter( 'stm_post_classes', 'stm_configurations_stm_post_classes' );

function stm_configurations_stm_post_classes( $classes ) {
	$classes[] = 'plugin_style';

	return $classes;
}

function stm_layout_icons_sets() {
	return array(
		'Language Center' => 'language_center',
	);
}

function stm_layout_icons_loader() {
	$fonts        = array();
	$layout_icons = stm_layout_icons_sets();

	foreach ( $layout_icons as $layout_name => $layout_icon ) {
		$icons_json = get_template_directory() . "/assets/layout_icons/{$layout_icon}/selection.json";

		if ( file_exists( $icons_json ) && is_string( wp_remote_get( $icons_json ) ) ) {

			$fonts[ $layout_name ] = array();
			$icons                 = json_decode( wp_remote_get( $icons_json ), true );
			$prefix                = $icons['preferences']['fontPref']['prefix'];
			$icons                 = $icons['icons'];
			foreach ( $icons as $icon ) {
				$icon_name               = $icon['properties']['name'];
				$fonts[ $layout_icon ][] = array(
					$prefix . $icon_name => $icon_name,
				);
			}
		}
	}

	return $fonts;
}

add_action( 'wp_enqueue_scripts', 'stm_conf_enqueue_ss' );

function stm_conf_enqueue_ss() {
	$assets = STM_POST_TYPE_URL . 'theme/assets';

	wp_register_script( 'lazysizes.js', $assets . '/lazysizes.min.js', array(), 'false', false );
	wp_enqueue_script( 'stm_lms_lazysizes', $assets . '/stm_lms_lazyload.js', array( 'jquery', 'lazysizes.js' ), 'false', false );
	wp_enqueue_style( 'stm_lazyload_init', $assets . '/lazyload.css', array(), 'false' );
}

function stm_conf_layload_image( $image ) {

	if ( ! class_exists( 'STM_LMS_Options' ) ) {
		return $image;
	}

	$lazyload = STM_LMS_Options::get_option( 'enable_lazyload', false );

	if ( ! $lazyload ) {
		return $image;
	}

	$image_wrapper  = '<div class="stm_lms_lazy_image">';
	$image_wrapper .= str_replace(
		array( 'sizes="', 'srcset="', 'src="', 'class="' ),
		array( 'data-sizes="', 'data-srcset="', 'data-src="', 'class="lazyload ' ),
		$image
	);
	$image_wrapper .= '</div>';

	return $image_wrapper;
}

if ( ! function_exists( 'A2A_SHARE_SAVE_shortcode' ) ) {
	add_shortcode(
		'addtoany',
		function () {
			return null;
		}
	);
}

// Remove dashboard widget
function redux_remove_dashboard_widget() {
	remove_meta_box( 'redux_dashboard_widget', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'redux_remove_dashboard_widget', 100 );
