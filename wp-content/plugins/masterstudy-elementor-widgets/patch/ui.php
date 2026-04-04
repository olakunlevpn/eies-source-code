<?php

new CEW_Patch_UI();

class CEW_Patch_UI {


	public function __construct() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active( 'js_composer/js_composer.php' ) ) {
			add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

	}

	public function add_menu_page() {
		add_menu_page(
			__( 'WPB > Elementor patch', 'textdomain' ),
			__( 'WPB > Elementor', 'textdomain' ),
			'manage_options',
			'cew_patch',
			array( $this, 'patch_page' ),
			'dashicons-flag',
			3
		);
	}

	public function patch_page() {
		require_once STM_CEW_PATH . '/patch/templates/main.php';
	}

	public function enqueue( $hook ) {
		if ( 'toplevel_page_cew_patch' === $hook ) {

			$patch_nonce = wp_create_nonce( 'stm_lms_wpb_patch' );
			wp_enqueue_style( 'cew_patch', STM_CEW_URL . '/assets/css/masterstudy-elementor-patch.css', array(), time() );

			/*APP Files*/
			wp_enqueue_style( 'cew_patch_app', STM_CEW_URL . 'patch-app/dist/css/app.css', array(), time() );
			wp_enqueue_style( 'cew_patch_font', 'https://fonts.googleapis.com/css?family=Raleway:400,700&display=swap', array(), time() );

			wp_enqueue_script( 'cew_patch_app_vendors', STM_CEW_URL . 'patch-app/dist/js/chunk-vendors.js', array(), time(), true );
			wp_enqueue_script( 'cew_patch_app', STM_CEW_URL . 'patch-app/dist/js/app.js', array( 'cew_patch_app_vendors' ), time(), true );
			wp_localize_script(
				'cew_patch_app',
				'cew_patch_vars',
				array(
					'endpoints' => array(
						'post_types_list' => admin_url( 'admin-ajax.php' ) . '?action=cew_get_post_types',
						'retrieve_post'   => admin_url( 'admin-ajax.php' ) . '?action=cew_retrieve_post_to_patch',
						'patch_post'      => add_query_arg(
							array(
								'action' => 'cew_patch_post',
								'nonce'  => $patch_nonce,
							),
							admin_url( 'admin-ajax.php' )
						),
						'site_url'        => get_site_url( get_current_blog_id() ),
					),
				)
			);
		}
	}
}
