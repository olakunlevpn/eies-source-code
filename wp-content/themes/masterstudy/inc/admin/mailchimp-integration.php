<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action(
	'admin_init',
	function() {
		// Define the pages where this logic should run.
		$theme_pages = array( 'stm-lms-license', 'stm-lms-settings-account' );

		// Exit early if we are not on the relevant pages.
		if ( ! isset( $_GET['page'] ) || ! in_array( $_GET['page'], $theme_pages, true ) ) {
			return;
		}

		// Step 1: Ensure the submodule's class is loaded from the plugin.
		if ( ! class_exists( 'STMMailChimpBase' ) ) {
			if ( defined( 'STM_LMS_LIBRARY' ) ) {
				$class_path = STM_LMS_LIBRARY . '/stm-mailchimp-integration/classes/STMMailChimpBase.php';
				if ( file_exists( $class_path ) ) {
					require_once $class_path;
				}
			}
		}

		$license_data = get_option( 'stm_fs_license_data' );
		if ( ! empty( $license_data ) && ! empty( $license_data['email'] ) ) {

			// The plugin should have already loaded the class. If not, we can't proceed.
			if ( ! class_exists( 'STMMailChimpBase' ) ) {
				return;
			}

			$name = '';
			if ( ! empty( $license_data['user_id'] ) ) {
				$user = get_user_by( 'id', $license_data['user_id'] );
				if ( $user ) {
					$name = $user->display_name;
				}
			}
			if ( empty( $name ) ) {
				$name = explode( '@', $license_data['email'] )[0];
			}

			// Subscribe the user, passing the theme's specific slug directly.
			$result = STMMailChimpBase::subscribeUserFromFrontend( $license_data['email'], $name, 'masterstudy' );
		}
	},
	20
);
