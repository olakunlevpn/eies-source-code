<?php

add_action(
	'admin_enqueue_scripts',
	function () {
		$version = ( WP_DEBUG ) ? time() : STM_LMS_VERSION;

		wp_register_style( 'masterstudy-email-smart-tag-styles', STM_LMS_PRO_URL . 'addons/email_manager/assets/smart.css', array( 'wp-admin' ), $version );
		wp_register_script( 'masterstudy-email-smart-tag-scripts', STM_LMS_PRO_URL . 'addons/email_manager/assets/smart.js', array( 'jquery', 'vtrumbowyg' ), $version, true );
	}
);
