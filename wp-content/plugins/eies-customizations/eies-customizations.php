<?php
/**
 * Plugin Name: EIES Customizations
 * Plugin URI:  https://eies.com.bo
 * Description: Site-specific PHP customizations for EIES (Escuela Internacional para la Educación Superior). Holds all custom tweaks on top of the theme and third-party plugins. Every change is logged inside the code with a FIX #NNN header.
 * Version:     1.0.0
 * Author:      EIES
 * Text Domain: eies-customizations
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * FIX LOG
 * =========================================================
 * FIX #001 — Plugin scaffolding
 *   Date:   2026-04-18
 *   Author: EIES dev
 *   Issue:  Custom PHP tweaks were scattered across the child
 *           theme functions.php with no changelog or ownership.
 *   Fix:    Created this plugin as the single source of truth
 *           for all site-specific PHP customizations. Loaded
 *           independently of the theme so redesigns don't lose
 *           custom behavior.
 *
 * FIX #002 — Dynamic live stats for the "Logros" homepage section
 *   Date:   2026-04-18
 *   Author: EIES dev
 *   Issue:  The four numbers in the "School Achievements" section
 *           (94,532 / 11,233 / 82,673 / 37,497) were hard-coded
 *           MasterStudy demo values. They did not reflect the
 *           real database.
 *   Fix:    Register [eies_stat] shortcode returning live counts
 *           (courses, students, instructors) plus a computed
 *           "years since founding" based on a configurable
 *           founding year. Values cached via transients.
 * =======================================================*/

define( 'EIES_CUSTOM_VERSION', '1.0.0' );
define( 'EIES_CUSTOM_PATH', plugin_dir_path( __FILE__ ) );
define( 'EIES_CUSTOM_URL', plugin_dir_url( __FILE__ ) );

require_once EIES_CUSTOM_PATH . 'includes/class-eies-settings.php';
require_once EIES_CUSTOM_PATH . 'includes/class-eies-stats.php';

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'eies-customizations', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	new EIES_Custom_Stats();
	if ( is_admin() ) {
		new EIES_Custom_Settings();
	}
} );
