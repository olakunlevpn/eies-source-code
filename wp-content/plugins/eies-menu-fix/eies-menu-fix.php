<?php
/**
 * Plugin Name: EIES Menu Fix
 * Plugin URI: https://maylancer.org
 * Description: Rebinds broken ms_lms_mega_menu widget references in Elementor templates to a valid nav menu. Auto-runs on plugin activation. Manual re-run available under Tools → EIES Menu Fix.
 * Version: 1.0.0
 * Author: Olakunlevpn
 * Author URI: https://maylancer.org
 * Text Domain: eies-menu-fix
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EIES_MENU_FIX_VERSION', '1.0.0' );
define( 'EIES_MENU_FIX_PATH', plugin_dir_path( __FILE__ ) );
define( 'EIES_MENU_FIX_URL', plugin_dir_url( __FILE__ ) );

require_once EIES_MENU_FIX_PATH . 'includes/class-eies-menu-fix.php';
require_once EIES_MENU_FIX_PATH . 'includes/class-eies-menu-fix-admin.php';

register_activation_hook( __FILE__, array( 'EIES_Menu_Fix', 'activate' ) );

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'eies-menu-fix', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	if ( is_admin() ) {
		new EIES_Menu_Fix_Admin();
	}
} );
