<?php
/**
 * Plugin Name: EIES Moodle Migration
 * Description: Migrates data from Moodle LMS to MasterStudy LMS
 * Version: 1.0.0
 * Author: EIES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EIES_MIGRATION_PATH', plugin_dir_path( __FILE__ ) );
define( 'EIES_MIGRATION_URL', plugin_dir_url( __FILE__ ) );

// C1 FIX: Moodle DB config from wp-config.php defines or fallback to local dev
// On server, add these to wp-config.php:
// define( 'MOODLE_DB_HOST', 'localhost' );
// define( 'MOODLE_DB_NAME', 'marceloeies_moodle' );
// define( 'MOODLE_DB_USER', 'marceloeies_soporte' );
// define( 'MOODLE_DB_PASS', 'your_password_here' );
// define( 'MOODLE_DATA_PATH', '/home/marceloeies/public_html/moodle-datos/filedir/' );
if ( ! defined( 'MOODLE_DB_HOST' ) ) {
	define( 'MOODLE_DB_HOST', 'localhost' );
}
if ( ! defined( 'MOODLE_DB_NAME' ) ) {
	define( 'MOODLE_DB_NAME', 'moodle_eies' );
}
if ( ! defined( 'MOODLE_DB_USER' ) ) {
	define( 'MOODLE_DB_USER', 'root' );
}
if ( ! defined( 'MOODLE_DB_PASS' ) ) {
	define( 'MOODLE_DB_PASS', '' );
}
if ( ! defined( 'MOODLE_DATA_PATH' ) ) {
	define( 'MOODLE_DATA_PATH', '' );
}
if ( ! defined( 'MOODLE_DB_PREFIX' ) ) {
	define( 'MOODLE_DB_PREFIX', 'mdl_' );
}

require_once EIES_MIGRATION_PATH . 'includes/class-migration-base.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-categories.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-users.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-courses.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-quizzes.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-enrollments.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-files.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-wp-data.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-wp-users.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-wp-products.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-wp-orders.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-wp-content.php';
require_once EIES_MIGRATION_PATH . 'includes/class-migrate-wp-settings.php';

add_action( 'admin_menu', 'eies_migration_menu' );

function eies_migration_menu() {
	add_management_page(
		__( 'EIES Migration', 'eies-migration' ),
		__( 'EIES Migration', 'eies-migration' ),
		'manage_options',
		'eies-migration',
		'eies_migration_page'
	);
}

function eies_migration_page() {
	require_once EIES_MIGRATION_PATH . 'admin/migration-page.php';
}

// Handle AJAX migration requests
add_action( 'wp_ajax_eies_run_migration', 'eies_handle_migration' );

function eies_handle_migration() {
	check_ajax_referer( 'eies_migration_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized' );
	}

	$step = sanitize_text_field( $_POST['step'] ?? '' );

	@set_time_limit( 0 );
	@ini_set( 'memory_limit', '1024M' );

	$result = array( 'success' => false, 'message' => 'Unknown step' );

	switch ( $step ) {
		case 'categories':
			$migrator = new EIES_Migrate_Categories();
			$result   = $migrator->run();
			break;
		case 'users':
			$migrator = new EIES_Migrate_Users();
			$result   = $migrator->run();
			break;
		case 'courses':
			$migrator = new EIES_Migrate_Courses();
			$result   = $migrator->run();
			break;
		case 'quizzes':
			$migrator = new EIES_Migrate_Quizzes();
			$result   = $migrator->run();
			break;
		case 'enrollments':
			$migrator = new EIES_Migrate_Enrollments();
			$result   = $migrator->run();
			break;
		case 'files':
			$migrator = new EIES_Migrate_Files();
			$result   = $migrator->run();
			break;
		case 'wp_data':
			$migrator = new EIES_Migrate_WP_Data();
			$result   = $migrator->run();
			break;
		case 'wp_users':
			$migrator = new EIES_Migrate_WP_Users();
			$result   = $migrator->run();
			break;
		case 'wp_products':
			$migrator = new EIES_Migrate_WP_Products();
			$result   = $migrator->run();
			break;
		case 'wp_orders':
			$migrator = new EIES_Migrate_WP_Orders();
			$result   = $migrator->run();
			break;
		case 'wp_content':
			$migrator = new EIES_Migrate_WP_Content();
			$result   = $migrator->run();
			break;
		case 'wp_settings':
			$migrator = new EIES_Migrate_WP_Settings();
			$result   = $migrator->run();
			break;
		case 'reset':
			$base = new EIES_Migration_Base();
			$result = $base->reset_all();
			break;
	}

	wp_send_json( $result );
}

// Create mapping table on activation
register_activation_hook( __FILE__, 'eies_migration_activate' );

function eies_migration_activate() {
	global $wpdb;
	$charset = $wpdb->get_charset_collate();
	$table   = $wpdb->prefix . 'eies_migration_map';

	$sql = "CREATE TABLE IF NOT EXISTS {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		entity_type varchar(50) NOT NULL,
		moodle_id bigint(20) unsigned NOT NULL,
		wp_id bigint(20) unsigned NOT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY entity_moodle (entity_type, moodle_id),
		KEY wp_id (wp_id)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
