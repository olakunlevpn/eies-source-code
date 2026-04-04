<?php

add_action(
	'admin_init',
	function() {
		if ( function_exists( 'stm_admin_notices_init' ) ) {
			if ( defined( 'STM_LMS_PRO_FILE' ) ) {
				$theme_version = '4.8.36';
				$pro_version   = '4.3.5';
				$free_version  = '3.1.3';
				// Theme version check.
				$current_theme = wp_get_theme();
				$current_theme = $current_theme->parent() ? $current_theme->parent() : $current_theme;
				$is_theme_comp = version_compare( $theme_version, $current_theme->version ) > 0;
				// Free plugin version check.
				if ( ! defined( 'MS_LMS_FILE' ) ) {
					return;
				}
				$free_plugin  = get_plugin_data( MS_LMS_FILE );
				$is_free_comp = version_compare( $free_version, $free_plugin['Version'] ?? $free_version ) > 0;
				// Pro plugin version check.
				$pro_plugin  = get_plugin_data( STM_LMS_PRO_FILE );
				$is_pro_comp = version_compare( $pro_version, $pro_plugin['Version'] ?? $pro_version ) > 0;

				if ( $is_theme_comp || $is_pro_comp ) {

					$notices = array(
						'notice_type'          => 'animate-triangle-notice',
						'notice_logo'          => 'attent_circle.svg',
						'notice_title'         => esc_html__( 'Please update MasterStudy Theme and MasterStudy LMS Learning Management System PRO!', 'masterstudy' ),
						'notice_desc'          => esc_html__( 'The current version of MasterStudy LMS is not compatible with old versions of MasterStudy Theme and MasterStudy LMS Learning Management System PRO, some functionality may not work correctly or may stop working completely.', 'masterstudy' ),
						'notice_btn_one'       => 'https://docs.stylemixthemes.com/masterstudy-theme-documentation/getting-started/how-to-update-masterstudy',
						'notice_btn_one_attrs' => 'target=_blank',
						'notice_btn_one_title' => esc_html__( 'Update Theme', 'masterstudy' ),
						'notice_btn_two'       => esc_attr( get_admin_url() ) . 'admin.php?page=stm-admin-plugins#has_update',
						'notice_btn_two_title' => esc_html__( 'Update Plugin', 'masterstudy' ),
					);

					if ( $is_theme_comp && ! $is_pro_comp ) {
						$notices = array_merge(
							$notices,
							array(
								'notice_title'         => esc_html__( 'Please update MasterStudy Theme!', 'masterstudy' ),
								'notice_desc'          => esc_html__( 'The current version of MasterStudy LMS is not compatible with old versions of MasterStudy Theme, some functionality may not work correctly or may stop working completely.', 'masterstudy' ),
								'notice_btn_two'       => '',
								'notice_btn_two_title' => '',
							)
						);
					}

					if ( ! $is_theme_comp && $is_pro_comp ) {
						$notices = array_merge(
							$notices,
							array(
								'notice_title'         => esc_html__( 'Please update MasterStudy LMS Learning Management System PRO!', 'masterstudy' ),
								'notice_desc'          => esc_html__( 'The current version of MasterStudy LMS is not compatible with old versions of MasterStudy LMS Learning Management System PRO, some functionality may not work correctly or may stop working completely.', 'masterstudy' ),
								'notice_btn_one'       => esc_attr( get_admin_url() ) . 'admin.php?page=stm-admin-plugins#has_update',
								'notice_btn_one_title' => esc_html__( 'Update Plugin', 'masterstudy' ),
								'notice_btn_one_attrs' => '',
								'notice_btn_two'       => '',
								'notice_btn_two_title' => '',
							)
						);
					}

					stm_admin_notices_init( $notices );
				}
			}
		}
	}
);
