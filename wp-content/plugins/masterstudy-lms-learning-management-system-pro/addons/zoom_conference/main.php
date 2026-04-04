<?php
// phpcs:ignoreFile

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ms_zoom_migrate_settings() {
	// If new option already exists, do nothing.
	//TODO remove rewrite flush rules on 2026 year!
	$ms_settings = get_option( 'ms_zoom_settings', null );
	if ( null !== $ms_settings ) {
		flush_rewrite_rules( true );

		return;
	}

	// Get old settings.
	$stm_settings = get_option( 'stm_zoom_settings', null );
	if ( null === $stm_settings ) {
		flush_rewrite_rules( true );

		// Nothing to migrate.
		return;
	}

	// Create new option with same value.
	add_option( 'ms_zoom_settings', $stm_settings );
	flush_rewrite_rules( true );
}

// Run early, but only needs to be in PHP once.
add_action( 'admin_init', 'ms_zoom_migrate_settings' );
/**
 * Enqueue frontend assets
 */
function mslms_zoom_conference_enqueue_assets() {
	wp_enqueue_style(
		'mslms-zoom-frontend',
		MSLMS_ZOOM_CONFERENCE_URL . '/build/css/front.css',
		array(),
		MSLMS_ZOOM_CONFERENCE_VERSION
	);

	wp_enqueue_script(
		'mslms-zoom-frontend',
		MSLMS_ZOOM_CONFERENCE_URL . '/build/js/base.js',
		array( 'jquery' ),
		MSLMS_ZOOM_CONFERENCE_VERSION,
		true
	);

	wp_enqueue_script(
		'mslms-zoom-countdown',
		MSLMS_ZOOM_CONFERENCE_URL . '/build/js/timer.js',
		array( 'jquery' ),
		MSLMS_ZOOM_CONFERENCE_VERSION,
		true
	);
}

/**
 * Enqueue admin assets
 */
function mslms_zoom_conference_admin_assets() {
	wp_enqueue_style(
		'mslms-zoom-admin',
		MSLMS_ZOOM_CONFERENCE_URL . '/build/css/main.css',
		array(),
		MSLMS_ZOOM_CONFERENCE_VERSION
	);

}

// Define MasterStudy constants
define( 'MSLMS_ZOOM_CONFERENCE_VERSION', STM_LMS_PRO_VERSION );
define( 'MSLMS_ZOOM_CONFERENCE_PATH', STM_LMS_PRO_ADDONS . '/zoom_conference' );
define( 'MSLMS_ZOOM_CONFERENCE_URL', STM_LMS_PRO_URL . 'addons/zoom_conference/' );

require_once MSLMS_ZOOM_CONFERENCE_PATH . '/src/Support/core.php';
require_once MSLMS_ZOOM_CONFERENCE_PATH . '/src/Services/token.php';
require_once MSLMS_ZOOM_CONFERENCE_PATH . '/src/Services/conference.php';
require_once MSLMS_ZOOM_CONFERENCE_PATH . '/src/Services/userapi.php';

// Enqueue zoom assets
add_action( 'wp_enqueue_scripts', 'mslms_zoom_conference_enqueue_assets' );
add_action( 'admin_enqueue_scripts', 'mslms_zoom_conference_admin_assets' );
