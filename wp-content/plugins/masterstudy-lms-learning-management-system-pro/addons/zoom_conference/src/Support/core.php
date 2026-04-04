<?php
// phpcs:ignoreFile
/**
 * Main file
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} //Exit if accessed directly

if ( ! defined( 'MSLMS_ZOOM_DB_VERSION' ) ) {
	define( 'MSLMS_ZOOM_DB_VERSION', '2.0' );
}
if ( ! defined( 'MSLMS_ZOOM_FILE' ) ) {
	define( 'MSLMS_ZOOM_FILE', MSLMS_ZOOM_CONFERENCE_PATH . '/src/Support/core.php' );
}
if ( ! defined( 'MSLMS_ZOOM_DIR' ) ) {
	define( 'MSLMS_ZOOM_DIR', MSLMS_ZOOM_CONFERENCE_PATH );
}
if ( ! defined( 'MSLMS_ZOOM_PATH' ) ) {
	define( 'MSLMS_ZOOM_PATH', MSLMS_ZOOM_CONFERENCE_PATH );
}
if ( ! defined( 'MSLMS_ZOOM_URL' ) ) {
	define( 'MSLMS_ZOOM_URL', MSLMS_ZOOM_CONFERENCE_URL );
}

// Load Composer autoloader (pro plugin loads it in includes/pro.php, but ensure it's loaded here too)
if ( file_exists( dirname( MSLMS_ZOOM_PATH, 2 ) . '/vendor/autoload.php' ) ) {
	require_once dirname( MSLMS_ZOOM_PATH, 2 ) . '/vendor/autoload.php';
}

// Fallback: Load Zoom API classes manually only if autoloader didn't load them
// This prevents conflicts with other plugins that might have the same classes
if ( ! class_exists( 'Zoom\Contracts\AuthService' ) ) {
	require_once MSLMS_ZOOM_PATH . '/src/Api/Contracts/AuthService.php';
}
if ( ! class_exists( 'Zoom\Contracts\Request' ) ) {
	require_once MSLMS_ZOOM_PATH . '/src/Api/Contracts/Request.php';
}
if ( ! class_exists( 'Zoom\Api\Users' ) ) {
	require_once MSLMS_ZOOM_PATH . '/src/Api/Controllers/UsersController.php';
}
if ( ! class_exists( 'Zoom\Api\Meetings' ) ) {
	require_once MSLMS_ZOOM_PATH . '/src/Api/Controllers/MeetingsController.php';
}
if ( ! class_exists( 'Zoom\Api\Reports' ) ) {
	require_once MSLMS_ZOOM_PATH . '/src/Api/Controllers/ReportsController.php';
}

require_once MSLMS_ZOOM_PATH . '/src/Support/utility.php';

// Include zoom-conference classes
require_once MSLMS_ZOOM_PATH . '/src/Services/vc.php';
require_once MSLMS_ZOOM_PATH . '/src/Admin/vc-menus.php';
require_once MSLMS_ZOOM_PATH . '/src/Admin/vc-types.php';
require_once MSLMS_ZOOM_PATH . '/src/Services/vc-apis.php';

// Create objects
new MSLMS_StmZoom;
new MSLMS_ZoomAdminMenus;
new MSLMS_StmZoomPostTypes;

if ( is_admin() ) {

	require_once MSLMS_ZOOM_PATH . '/src/Layouts/backend/alert.php';
}
