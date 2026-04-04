<?php

// Enqueue admin styles and scripts
use MasterStudy\Lms\Pro\addons\media_library\Services\Client;

function masterstudy_lms_media_library_enqueue_admin_scripts() {
	wp_register_style( 'masterstudy-lms-media-library-settings', STM_LMS_PRO_URL . 'assets/css/media-library/settings.css', array(), STM_LMS_PRO_VERSION );

	wp_register_script( 'masterstudy-lms-media-library-settings', STM_LMS_PRO_URL . 'assets/js/media-library/settings.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'masterstudy_lms_media_library_enqueue_admin_scripts' );

function smt_lms_media_library_flush_cache() {
	check_ajax_referer( 'stm_lms_media_library_clear_integration_cache', 'nonce' );
	Client::flush_all_cache();

	wp_send_json( 'Success' );
}
add_action( 'wp_ajax_stm_lms_flush_media_library_cache', 'smt_lms_media_library_flush_cache' );
