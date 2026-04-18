<?php
/**
 * Plugin Name: EIES Certificates
 * Plugin URI: https://maylancer.org
 * Description: Certificate verification system for EIES. Imports issued certificates from Moodle customcert and provides a public verification endpoint.
 * Version: 1.0.0
 * Author: Olakunlevpn
 * Author URI: https://maylancer.org
 * Text Domain: eies-certificates
 * Domain Path: /languages
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EIES_CERT_VERSION', '1.0.0' );
define( 'EIES_CERT_PATH', plugin_dir_path( __FILE__ ) );
define( 'EIES_CERT_URL', plugin_dir_url( __FILE__ ) );
define( 'EIES_CERT_TABLE', 'eies_certificates' );

require_once EIES_CERT_PATH . 'includes/class-eies-cert-install.php';
require_once EIES_CERT_PATH . 'includes/class-eies-cert-importer.php';
require_once EIES_CERT_PATH . 'includes/class-eies-cert-verify.php';
require_once EIES_CERT_PATH . 'includes/class-eies-cert-admin.php';

register_activation_hook( __FILE__, array( 'EIES_Cert_Install', 'activate' ) );

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'eies-certificates', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	new EIES_Cert_Verify();
	if ( is_admin() ) {
		new EIES_Cert_Admin();
	}
} );
