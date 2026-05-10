<?php
/**
 * Plugin Name: EIES STM Shims
 * Plugin URI: https://maylancer.org
 * Description: No-op shims for masterstudy parent theme helpers (stm_module_styles, stm_module_scripts, stm_get_template_part). Prevents fatal errors when running ms-lms-starter-theme without the full MasterStudy parent theme, but masterstudy-elementor-widgets / similar plugins are still active.
 * Version: 1.0.0
 * Author: Olakunlevpn
 * Author URI: https://maylancer.org
 * Text Domain: eies-stm-shims
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * masterstudy parent theme registers per-module stylesheet enqueueing. The
 * ms-lms-starter-theme does NOT inherit this — but masterstudy-elementor-widgets
 * still calls it during Elementor preview, causing a fatal. Provide a no-op so
 * the call chain completes without ill effect.
 */
if ( ! function_exists( 'stm_module_styles' ) ) {
	function stm_module_styles( $module = '', $deps = array(), $ver = false, $media = 'all' ) {
		return;
	}
}

if ( ! function_exists( 'stm_module_scripts' ) ) {
	function stm_module_scripts( $module = '', $deps = array(), $ver = false, $in_footer = true ) {
		return;
	}
}

/**
 * Parent theme template-part loader. Plugins that target masterstudy expect
 * this helper. Falls through to get_template_part for graceful degradation.
 */
if ( ! function_exists( 'stm_get_template_part' ) ) {
	function stm_get_template_part( $slug, $name = null, $args = array() ) {
		if ( function_exists( 'get_template_part' ) ) {
			get_template_part( $slug, $name, $args );
		}
	}
}

/**
 * Some masterstudy-elementor-widgets controls call this to resolve the active
 * theme directory URI. Starter theme paths differ; return a sensible default.
 */
if ( ! function_exists( 'stm_get_theme_uri' ) ) {
	function stm_get_theme_uri() {
		return get_stylesheet_directory_uri();
	}
}

/**
 * Defensive: a few demo importers query for this helper.
 */
if ( ! function_exists( 'stm_lms_get_template' ) ) {
	function stm_lms_get_template( $template = '', $args = array() ) {
		if ( function_exists( 'masterstudy_lms_get_template' ) ) {
			masterstudy_lms_get_template( $template, $args );
		}
	}
}
