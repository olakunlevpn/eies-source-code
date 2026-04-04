<?php
/**
 * Add Upgrade PRO PLUS submenu and page
 */
add_action( 'admin_menu', 'stm_add_upgrade_submenu', 15 );

function stm_add_upgrade_submenu() {
	$parent_slug = 'stm-admin';
	$capability  = 'manage_options';

	$license_data      = get_option( 'stm_fs_license_data' );
	$token_data        = get_option( 'stm_masterstudy_token' );
	$pro_plugin_active = is_plugin_active( 'masterstudy-lms-learning-management-system-pro/masterstudy-lms-learning-management-system-pro.php' );

	$is_valid_token = is_array( $token_data ) && isset( $token_data['token'] ) && ! empty( trim( $token_data['token'] ) );

	// Check:
	// 1. Whether the MasterStudy menu exists
	// 2. Whether a valid token is present (required!)
	// 3. Whether an activated license does NOT already exist
	// 4. Whether PRO plugin is NOT already active
	if ( ! menu_page_url( $parent_slug, false ) || ! $is_valid_token || ! empty( $license_data ) || ! $pro_plugin_active || function_exists( 'mslms_fs' ) ) {
		return;
	}

	add_submenu_page(
		$parent_slug,
		'Upgrade',
		'<span class="stm-update-pro-plus"><span class="stm-update-pro-plus__trial"><img src="' . esc_url( STM_TEMPLATE_URI . '/assets/admin/images/ms.svg' ) . '" width="32" height="32" alt="MasterStudy PRO Plus"><span>MasterStudy<strong>PRO Plus</strong></span></span><span class="stm-update-pro-plus__date">3 months free</span></span>',
		$capability,
		$parent_slug . '-upgrade',
		'masterstudy_pro_plus_plugin_page_callback'
	);

	add_submenu_page(
		null,
		'Upgrade Confirmation',
		'',
		'manage_options',
		$parent_slug . '-upgrade-pro-plus',
		'masterstudy_pro_plus_plugin_wizard_callback'
	);
}

add_action( 'admin_menu', 'add_license_menu_item', 100004 );

function add_license_menu_item() {
	if ( ! is_admin() || ! function_exists( 'mslms_fs' ) ) {
		return;
	}

	$license_data = get_option( 'stm_fs_license_data' );
	$expires_date = $license_data['expires'] ?? '';

	if ( empty( $expires_date ) || false === strtotime( $expires_date ) ) {
		return;
	}

	$now          = time();
	$seconds_left = strtotime( $expires_date ) - $now;
	$days_left    = floor( $seconds_left / DAY_IN_SECONDS );
	$hours_left   = floor( $seconds_left / HOUR_IN_SECONDS );
	$minutes_left = floor( $seconds_left / MINUTE_IN_SECONDS );
	$status_class = '';
	$status_text  = '';

	if ( $seconds_left <= 0 ) {
		$status_class = 'license-expired';
		$status_text  = 'EXPIRED';
	} elseif ( $days_left <= 10 ) {
		$status_class = 'license-warning';
		if ( $days_left < 1 ) {
			if ( $hours_left < 1 ) {
				$status_text = $minutes_left . ' minute' . ( 1 === $minutes_left ? '' : 's' ) . ' left';
			} else {
				$status_text = $hours_left . ' hour' . ( 1 === $hours_left ? '' : 's' ) . ' left';
			}
		} else {
			$status_text = $days_left . ' day' . ( 1 === $days_left ? '' : 's' ) . ' left';
		}
	} else {
		$status_class = 'license-activate';
		$status_text  = $days_left . ' days left';
	}

	add_submenu_page(
		'stm-admin',
		esc_html__( 'License', 'masterstudy' ),
		'<span class="wp-menu-license-status ' . esc_attr( $status_class ) . '">' . esc_html__( 'License', 'masterstudy' ) . ' <strong>' . esc_html( $status_text ) . '</strong></span>',
		'manage_options',
		'stm-lms-license',
		'render_license_block_if_on_account_page'
	);
}

function masterstudy_pro_plus_plugin_page_callback() {
	require_once STM_TEMPLATE_DIR . '/inc/admin/pro_plus_upgrade/templates/pro_plus_main.php';
}

function masterstudy_pro_plus_plugin_wizard_callback() {
	require_once STM_TEMPLATE_DIR . '/inc/admin/pro_plus_upgrade/templates/pro_plus_wizard.php';
}

add_action( 'admin_notices', 'render_license_block_if_on_account_page' );

function render_license_block_if_on_account_page() {
	require_once STM_TEMPLATE_DIR . '/inc/admin/pro_plus_upgrade/templates/pro_plus_status.php';
}

/**
 * Add Scripts and Styles
 */
add_action( 'admin_enqueue_scripts', 'masterstudy_pro_plus_upgrade_enqueue_assets' );

function masterstudy_pro_plus_upgrade_enqueue_assets() {
	wp_enqueue_style( 'masterstudy-theme-admin', STM_TEMPLATE_URI . '/assets/admin/css/masterstudy-theme-admin.css', null, STM_ADMIN_VERSION, 'all' );

	if ( isset( $_GET['page'] ) && 'stm-admin-upgrade' === $_GET['page'] || isset( $_GET['page'] ) && 'stm-admin-upgrade-pro-plus' === $_GET['page'] || isset( $_GET['page'] ) && 'stm-lms-settings-account' === $_GET['page'] || isset( $_GET['page'] ) && 'stm-lms-license' === $_GET['page'] || isset( $_GET['page'] ) && 'stm-lms-settings' === $_GET['page'] ) {
		$license_data = get_option( 'stm_fs_license_data' );

		wp_enqueue_style( 'language_center', get_template_directory_uri() . '/assets/layout_icons/language_center/style.css', null, STM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'masterstudy-theme-pro-plus-upgrade', STM_TEMPLATE_URI . '/assets/admin/css/pro-plus-upgrade.css', null, STM_ADMIN_VERSION, 'all' );
		wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', null, true, STM_ADMIN_VERSION );
		wp_enqueue_script( 'ScrollTrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'gsap' ), true, STM_ADMIN_VERSION );
		wp_enqueue_script( 'masterstidy-theme-pro-plus-upgrade', STM_TEMPLATE_URI . '/assets/admin/js/pro-plus-upgrade.js', array( 'gsap', 'ScrollTrigger' ), true, STM_ADMIN_VERSION );
		wp_localize_script(
			'masterstidy-theme-pro-plus-upgrade',
			'masterstidy_theme_pro_plus_upgrade',
			array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'masterstidy_theme_pro_plus_upgrade_nonce' ),
				'activation_pending' => ! empty( $license_data['activation_pending'] ),
			)
		);
	}
}

add_action( 'wp_ajax_register_trial_user', 'masterstudy_register_trial_user_callback' );

function masterstudy_register_trial_user_callback() {
	check_ajax_referer( 'masterstidy_theme_pro_plus_upgrade_nonce', '_ajax_nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error( 'Invalid email address.' );
	}

	$transient_key = 'trial_email_' . md5( $email );
	if ( get_transient( $transient_key ) ) {
		wp_send_json_error( 'Please wait a few minutes before requesting again.' );
	}
	set_transient( $transient_key, time(), 5 * MINUTE_IN_SECONDS );

	$response = stm_register_trial_user_and_license( $email );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( $response->get_error_message() );
	}

	wp_send_json_success( $response );
}

function stm_get_trial_request_headers() {
	return array(
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		'token' => base64_encode( gmdate( 'Ymd' ) ),
	);
}

function stm_register_trial_user_and_license( $email ) {
	$plugin_slug      = 'masterstudy-lms-learning-management-system-pro';
	$plugin_dir       = WP_PLUGIN_DIR . '/' . $plugin_slug;
	$plugin_main_file = $plugin_slug . '/' . $plugin_slug . '.php';

	if ( is_plugin_active( $plugin_main_file ) ) {
		deactivate_plugins( $plugin_main_file );
	}

	if ( file_exists( $plugin_dir ) ) {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		$wp_filesystem->delete( $plugin_dir, true );
	}

	$response = wp_remote_post(
		'https://microservices.stylemixthemes.com/masterstudy-trial/?action=create_license',
		array(
			'headers' => stm_get_trial_request_headers(),
			'body'    => array(
				'email' => $email,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body_resp = wp_remote_retrieve_body( $response );
	$data      = json_decode( $body_resp, true );

	if ( empty( $data['license_key'] ) ) {
		return new WP_Error( 'create_license_failed', 'License not created or key missing.' );
	}

	$plugin_main_file = stm_install_pro_plugin_zip();
	if ( $plugin_main_file ) {
		if ( ! is_plugin_active( $plugin_main_file ) ) {
			$result = activate_plugin( $plugin_main_file );
		}

		wp_cache_delete( 'plugins', 'plugins' );

		stm_activate_trial_license( $data['license_key'] );
	}

	$license_data = array(
		'email'              => $email,
		'user_id'            => $data['user_id'],
		'license_id'         => $data['license_id'],
		'license_key'        => $data['license_key'],
		'plan_id'            => $data['plan_id'],
		'pricing_id'         => $data['pricing_id'],
		'expires'            => $data['expires_at'],
		'activated_at'       => current_time( 'mysql' ),
		'install_data'       => array(),
		'activation_pending' => true,
	);

	update_option( 'stm_fs_license_data', $license_data );

	return $license_data;
}

function stm_install_pro_plugin_zip() {
	$premium_slug = 'masterstudy-lms-learning-management-system-pro';

	$response = wp_remote_get(
		'https://microservices.stylemixthemes.com/masterstudy-trial/?action=get_download_url',
		array(
			'headers' => stm_get_trial_request_headers(),
		)
	);

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( empty( $data['download_url'] ) || empty( $data['token'] ) ) {
		return false;
	}

	$temp_file = wp_tempnam( 'pro_plugin' );
	if ( ! $temp_file ) {
		return false;
	}

	// phpcs:disable WordPress.WP.AlternativeFunctions
	$fp = fopen( $temp_file, 'w+' );
	if ( ! $fp ) {
		return false;
	}

	$ch = curl_init();
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_URL            => $data['download_url'],
			CURLOPT_RETURNTRANSFER => false,
			CURLOPT_FILE           => $fp,
			CURLOPT_HEADER         => false,
			CURLOPT_HTTPHEADER     => array(
				'Authorization: Bearer ' . $data['token'],
			),
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT        => 120,
		)
	);
	curl_exec( $ch );
	$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );
	fclose( $fp );
	// phpcs:enable

	if ( 200 !== $http_code || ! file_exists( $temp_file ) || filesize( $temp_file ) === 0 ) {
		unlink( $temp_file );
		return false;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	WP_Filesystem();
	$plugins_dir  = WP_PLUGIN_DIR;
	$unzip_result = unzip_file( $temp_file, $plugins_dir );
	unlink( $temp_file );

	if ( is_wp_error( $unzip_result ) ) {
		return false;
	}

	$main_file   = $premium_slug . '/' . $premium_slug . '.php';
	$plugin_path = WP_PLUGIN_DIR . '/' . $main_file;

	if ( file_exists( $plugin_path ) ) {
		return $main_file;
	}

	return false;
}

function stm_activate_trial_license( $license_key ) {
	if ( ! function_exists( 'mslms_fs' ) ) {
		return array( 'error' => 'Freemius not available' );
	}

	$fs = mslms_fs();
	if ( ! $fs ) {
		return array( 'error' => 'Freemius instance not found' );
	}

	$result = $fs->activate_migrated_license( $license_key );

	if ( is_wp_error( $result ) ) {
		return array( 'error' => $result->get_error_message() );
	}

	return array(
		'success'     => true,
		'license_key' => $license_key,
	);
}

add_action( 'wp_ajax_stm_upgrade_pro_plugin', 'stm_ajax_upgrade_pro_plugin' );

function stm_ajax_upgrade_pro_plugin() {
	check_ajax_referer( 'masterstidy_theme_pro_plus_upgrade_nonce', 'nonce' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( 'Insufficient permissions.' );
	}

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$plugin_slug = 'masterstudy-lms-learning-management-system-pro';

	if ( ! function_exists( 'stm_get_pro_plugin_version' ) ) {
		require_once get_template_directory() . '/inc/tgm/tgm-plugin-registration.php';
	}

	$plugin_version   = stm_get_pro_plugin_version();
	$source           = stm_get_download_url( $plugin_slug, $plugin_version );
	$plugin_main_file = $plugin_slug . '/' . $plugin_slug . '.php';
	$plugin_dir       = WP_PLUGIN_DIR . '/' . $plugin_slug;

	// Deactivate if active
	if ( is_plugin_active( $plugin_main_file ) ) {
		deactivate_plugins( $plugin_main_file );
	}

	// Remove plugin directory if exists
	if ( file_exists( $plugin_dir ) ) {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		$wp_filesystem->delete( $plugin_dir, true );
	}

	// Install plugin
	$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( $source );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( $result->get_error_message() );
	}

	// Activate plugin
	$activate = activate_plugin( $plugin_main_file );

	if ( is_wp_error( $activate ) ) {
		wp_send_json_error( 'Activation error: ' . $activate->get_error_message() );
	}

	wp_send_json_success( array( 'message' => 'Plugin successfully installed and activated' ) );
}

add_action(
	'wp_ajax_stm_activate_trial_license_ajax',
	function() {
		check_ajax_referer( 'masterstidy_theme_pro_plus_upgrade_nonce', 'nonce' );
		$license_data = get_option( 'stm_fs_license_data' );
		if ( empty( $license_data['license_key'] ) ) {
			wp_send_json_error( 'No license key found.' );
		}
		$result = stm_activate_trial_license( $license_data['license_key'] );
		if ( isset( $result['success'] ) && $result['success'] ) {
			$license_data['activation_pending'] = false;
			$license_data['install_data']       = $result;
			update_option( 'stm_fs_license_data', $license_data );
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result['error'] ?? 'Unknown error' );
		}
	}
);

// AJAX for deleting option when clicking on button
add_action(
	'wp_ajax_stm_delete_license_data',
	function() {
		check_ajax_referer( 'masterstidy_theme_pro_plus_upgrade_nonce', 'nonce' );
		if ( ! empty( get_option( 'stm_fs_license_data' ) ) ) {
			delete_option( 'stm_fs_license_data' );
		}
	}
);
