<?php
add_action(
	'init',
	function () {
		if ( ! empty( $_GET['action'] ) && 'stm_install_plugin' === $_GET['action'] ) {
			update_option( 'stm_lms_wizard_redirect', true, false );
		}
	}
);

if ( is_admin() ) {
	add_action( 'wp_ajax_stm_demo_import_content', 'stm_demo_import_content' );
}

require_once $plugin_path . '/importer/helpers/content.php';
require_once $plugin_path . '/importer/helpers/theme_options.php';
require_once $plugin_path . '/importer/helpers/set_lms_content.php';
require_once $plugin_path . '/importer/helpers/slider.php';
require_once $plugin_path . '/importer/helpers/addons.php';
require_once $plugin_path . '/importer/helpers/widgets.php';
require_once $plugin_path . '/importer/helpers/set_content.php';
require_once $plugin_path . '/importer/helpers/set_hb_options.php';
require_once $plugin_path . '/importer/helpers/megamenu/config.php';

function stm_demo_import_content() {

	if ( current_user_can( 'manage_options' ) ) {

		check_ajax_referer( 'stm_demo_import_content', 'nonce' );
		$layout       = ! empty( $_GET['demo_template'] ) ? sanitize_title( $_GET['demo_template'] ) : 'default';
		$builder      = ! empty( $_GET['builder'] ) ? sanitize_title( $_GET['builder'] ) : 'js_composer';
		$import_data  = ! empty( $_GET['import_data'] ) ? sanitize_title( $_GET['import_data'] ) : '';
		$import_media = ! empty( $_GET['import_media'] ) ? ( 'true' === $_GET['import_media'] ) : false;

		update_option( 'stm_lms_layout', $layout );

		// Run demo import parts
		$res = stm_demo_import_content_cli( $layout, $builder, $import_data, $import_media );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( $res, 400 );
		}

		if ( ! empty( $import_data ) ) {
			wp_send_json(
				array(
					'imported' => $import_data,
				)
			);
		} else {
			wp_send_json(
				apply_filters(
					'stm_masterstudy_importer_done_data',
					array(
						'url'                 => get_bloginfo( 'url' ),
						'title'               => esc_html__( 'View site', 'stm-configurations' ),
						'theme_options_title' => esc_html__( 'Theme options', 'stm-configurations' ),
						'theme_options'       => esc_url_raw( admin_url( 'admin.php?page=stm_option_options' ) ),
					)
				)
			);
		}

		die();

	}

}

/**
 * Run Demo Import
 *
 * @param $layout
 * @param $builder
 * @param $import_data
 * @param $import_media
 *
 * @return array|bool|string|\WP_Error
 */
function stm_demo_import_content_cli( $layout, $builder, $import_data, $import_media ) {
	switch ( $import_data ) {
		case 'content':
			delete_option( 'sidebars_widgets' );
			stm_theme_before_import_content( $layout, $builder );
			/** Import content */
			stm_theme_import_content( $layout, $builder );

			/*Import products*/

			return stm_import_products_content( $layout, $builder );
		case 'theme_options':
			/** Import theme options */
			stm_set_layout_options( $layout );
			break;
		case 'sliders':
			/** Import sliders */
			stm_theme_import_sliders( $layout );
			break;
		case 'widgets':
			/** Import Widgets */
			stm_theme_import_widgets( $layout );
			/** Set menu and pages */
			stm_set_content_options( $layout, $builder );
			/**Set LMS Options*/
			stm_set_lms_options( $layout );
			/**Addons*/
			stm_theme_enable_addons( $layout );
			break;
		default:
			do_action( 'stm_generate_theme_styles' );
			do_action( 'stm_masterstudy_importer_done', $layout );
	}
}


function masterstudy_get_post_types_for_elementor() {

	$field = array();

	$post_types_objects = get_post_types(
		array(
			'public' => true,
		),
		'objects'
	);

	foreach ( $post_types_objects as $cpt_slug => $post_type ) {

		$field[ $cpt_slug ] = $post_type->labels->name;

	}

	unset( $field['elementor_library'] );
	unset( $field['attachment'] );

	return array_keys( $field );
}

function stm_theme_before_import_content( $layout, $builder ) {
	if ( 'elementor' === $builder ) {

		/*Update Options Elementor*/
		update_option( 'elementor_cpt_support', masterstudy_get_post_types_for_elementor() );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
		update_option( 'elementor_load_fa4_shim', 'yes' );
		update_option( 'elementor_container_width', 1200 );
		update_option( 'elementor_space_between_widgets', 0 );

	}
}


add_action( 'stm_masterstudy_importer_done', 'elementor_set_default_settings' );
function elementor_set_default_settings() {
	$active_kit = intval( get_option( 'elementor_active_kit', 0 ) );
	$meta       = get_post_meta( $active_kit, '_elementor_page_settings', true );

	if ( ! empty( $active_kit ) ) {
		$meta                                  = ( ! empty( $meta ) ) ? $meta : array();
		$meta['container_width']               = array(
			'size'  => '1200',
			'unit'  => 'px',
			'sizes' => array(),
		);
		$meta['space_between_widgets']['size'] = array(
			'size'  => '0',
			'unit'  => 'px',
			'sizes' => array(),
		);
		update_post_meta( $active_kit, '_elementor_page_settings', $meta );

		if ( class_exists( 'Elementor\Core\Breakpoints\Manager' ) ) {
			Elementor\Core\Breakpoints\Manager::compile_stylesheet_templates();
		}
	}

	// AddToAny Share Buttons
	$new_options       = array(
		'icon_size'                         => 20,
		'display_in_posts_on_front_page'    => '-1',
		'display_in_posts_on_archive_pages' => '-1',
		'display_in_excerpts'               => '-1',
		'display_in_posts'                  => '-1',
		'display_in_pages'                  => '-1',
		'display_in_attachments'            => '-1',
		'display_in_feed'                   => '-1',
	);
	$custom_post_types = array_values(
		get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			),
			'objects'
		)
	);
	foreach ( $custom_post_types as $custom_post_type_obj ) {
		$placement_name                                     = $custom_post_type_obj->name;
		$new_options[ 'display_in_cpt_' . $placement_name ] = '-1';
	}

	update_option( 'addtoany_options', $new_options );

	global $wpdb;

	$from = trim( 'http://lmsdemo.loc' );
	$to   = get_site_url();

	$rows_affected = $wpdb->query(
		$wpdb->prepare(
			"UPDATE {$wpdb->postmeta} 
			SET `meta_value` = REPLACE(`meta_value`, %s, %s) 
			WHERE `meta_key` = '_elementor_data' 
			AND `meta_value` 
			LIKE %s ;",
			array(
				str_replace( '/', '\\\/', $from ),
				str_replace( '/', '\\\/', $to ),
				'[%',
			)
		)
	);

	if ( class_exists( 'Elementor\Core\Breakpoints\Manager' ) ) {
		Elementor\Core\Breakpoints\Manager::compile_stylesheet_templates();
	}
}


function stm_import_products_exclude_layouts() {
	return array(
		'default',
		'ms',
		'language_center',
	);
}
