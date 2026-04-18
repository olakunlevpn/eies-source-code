<?php
/**
 * WordPress Auto-Setup Script
 * Run after fresh-install.sh completes the file setup
 */

$site_dir  = '/home/marceloeies/public_html/testeoprevio';
$site_url  = 'https://testeoprevio.eies.com.bo';
$admin_user = 'mayahn';
$admin_pass = 'Blackrap@12';
$admin_email = 'juankore@hotmail.com';

define( 'ABSPATH', $site_dir . '/' );
define( 'WP_INSTALLING', true );
define( 'WP_ADMIN', true );

$_SERVER['HTTP_HOST']       = 'testeoprevio.eies.com.bo';
$_SERVER['REQUEST_URI']     = '/wp-admin/install.php';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

require_once ABSPATH . 'wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

// Install WordPress
wp_install( 'EIES', $admin_user, $admin_email, true, '', wp_slash( $admin_pass ) );
echo "WordPress installed\n";

// Set URLs
update_option( 'siteurl', $site_url );
update_option( 'home', $site_url );
echo "URLs set to {$site_url}\n";

// Activate MasterStudy theme
switch_theme( 'masterstudy' );
echo "MasterStudy theme activated\n";

// Activate plugins
$plugins = array(
	'masterstudy-lms-learning-management-system/masterstudy-lms-learning-management-system.php',
	'masterstudy-lms-learning-management-system-pro/masterstudy-lms-learning-management-system-pro.php',
	'masterstudy-elementor-widgets/masterstudy-elementor-widgets.php',
	'stm-post-type/stm-post-type.php',
	'stm-gdpr-compliance/stm-gdpr-compliance.php',
	'elementor/elementor.php',
	'header-footer-elementor/header-footer-elementor.php',
	'js_composer/js_composer.php',
	'revslider/revslider.php',
	'woocommerce/woocommerce.php',
	'contact-form-7/wp-contact-form-7.php',
	'breadcrumb-navxt/breadcrumb-navxt.php',
	'buddypress/bp-loader.php',
	'paid-memberships-pro/paid-memberships-pro.php',
	'add-to-any/addtoany.php',
	'envato-market/envato-market.php',
	'eies-migration/eies-migration.php',
	'eies-certificates/eies-certificates.php',
	'eies-customizations/eies-customizations.php',
);

$activated = 0;
foreach ( $plugins as $p ) {
	if ( file_exists( WP_PLUGIN_DIR . '/' . $p ) ) {
		$result = activate_plugin( $p );
		if ( ! is_wp_error( $result ) ) {
			$activated++;
		}
	}
}
echo "{$activated} plugins activated\n";

// Create migration mapping table
global $wpdb;
$charset = $wpdb->get_charset_collate();
$table   = $wpdb->prefix . 'eies_migration_map';
$wpdb->query( "CREATE TABLE IF NOT EXISTS {$table} (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	entity_type varchar(50) NOT NULL,
	moodle_id bigint(20) unsigned NOT NULL,
	wp_id bigint(20) unsigned NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY entity_moodle (entity_type, moodle_id),
	KEY wp_id (wp_id)
) {$charset}" );
echo "Migration table created\n";

echo "\nSetup complete!\n";
echo "Admin: {$site_url}/wp-admin/\n";
echo "User: {$admin_user}\n";
