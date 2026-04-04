<?php
/*
Plugin Name: STM Configurations
Plugin URI: https://stylemixthemes.com/
Description: Configurations plugin for the Masterstudy theme
Author: StylemixThemes
Author URI: https://stylemixthemes.com/
Text Domain: stm-post-type
Version: 4.6.22
*/

define( 'STM_POST_TYPE', 'stm_post_type' );
define( 'STM_POST_TYPE_PATH', plugin_dir_path( __FILE__ ) );
define( 'STM_POST_TYPE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load translations early enough.
 * Translations should be loaded on init or later (WP 6.7+ notice).
 */
add_action( 'init', 'stm_post_type_load_textdomain', 0 );
function stm_post_type_load_textdomain() {
	load_plugin_textdomain(
		'stm-post-type',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}

$plugin_path = STM_POST_TYPE_PATH;

require_once $plugin_path . 'post_type/post_type.php';
require_once $plugin_path . 'importer/importer.php';
require_once $plugin_path . 'theme/helpers.php';
require_once $plugin_path . 'theme/mailchimp.php';
require_once $plugin_path . 'theme/share.php';
require_once $plugin_path . 'theme/payment.php';
require_once $plugin_path . 'theme/image_sizes.php';
require_once $plugin_path . 'theme/crop-images.php';
require_once $plugin_path . 'visual_composer/vc.php';
require_once $plugin_path . 'ajax/ajax.php';

$widgets_path = $plugin_path . 'widgets/';
require_once $widgets_path . 'mailchimp.php';
require_once $widgets_path . 'contacts.php';
require_once $widgets_path . 'pages.php';
require_once $widgets_path . 'socials.php';
require_once $widgets_path . 'recent_posts.php';
require_once $widgets_path . 'working_hours.php';
require_once $widgets_path . 'text.php';
require_once $widgets_path . 'menus.php';

add_action( 'init', 'stm_check_woo_plugin', 5 );
function stm_check_woo_plugin() {
	if ( class_exists( 'WooCommerce' ) ) {
		$plugin_path  = STM_POST_TYPE_PATH;
		$widgets_path = $plugin_path . 'widgets/';

		require_once $widgets_path . 'woo_popular_courses.php';
		require_once $plugin_path . 'theme/woocommerce_setups.php';
	}
}

require_once $plugin_path . 'redux-framework/admin-init.php';

if ( is_admin() ) {
	require_once $plugin_path . 'announcement/main.php';
}
