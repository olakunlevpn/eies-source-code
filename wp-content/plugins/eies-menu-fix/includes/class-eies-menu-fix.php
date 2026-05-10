<?php
/**
 * Core scan + patch logic. Iterates every post that stores an `_elementor_data`
 * blob, finds `ms_lms_mega_menu` widget instances whose `menu` field points at
 * a non-existent term, and rewrites it to a valid menu term id. Idempotent.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Menu_Fix {

	const THEME_LOCATION = 'ms-lms-starter-theme-main-menu';

	/**
	 * Plugin activation hook entry point. Runs auto-patch with default target.
	 */
	public static function activate() {
		self::run_auto_fix();
	}

	/**
	 * Resolve the preferred target menu id:
	 *   1. Menu assigned to the starter theme header location.
	 *   2. First non-empty menu on the site.
	 *   3. First menu on the site.
	 */
	public static function get_target_menu_id() {
		$locations = get_nav_menu_locations();
		if ( ! empty( $locations[ self::THEME_LOCATION ] ) ) {
			return (int) $locations[ self::THEME_LOCATION ];
		}

		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ) {
			if ( (int) $menu->count > 0 ) {
				return (int) $menu->term_id;
			}
		}

		if ( ! empty( $menus ) ) {
			return (int) $menus[0]->term_id;
		}

		return 0;
	}

	/**
	 * Auto-detect target menu then patch.
	 *
	 * @return array { fixed:int, target:int, message:string }
	 */
	public static function run_auto_fix() {
		$target = self::get_target_menu_id();
		if ( ! $target ) {
			return array(
				'fixed'   => 0,
				'target'  => 0,
				'message' => __( 'No nav menu found on this site.', 'eies-menu-fix' ),
			);
		}
		return self::patch_all( $target );
	}

	/**
	 * Patch every `_elementor_data` row that references ms_lms_mega_menu and
	 * still points at a non-existent menu term.
	 *
	 * @param int $target_menu_id Term id that broken refs are rebound to.
	 * @return array { fixed:int, scanned:int, target:int, message:string }
	 */
	public static function patch_all( $target_menu_id ) {
		global $wpdb;

		$target_menu_id = (int) $target_menu_id;
		if ( ! $target_menu_id || ! wp_get_nav_menu_object( $target_menu_id ) ) {
			return array(
				'fixed'   => 0,
				'scanned' => 0,
				'target'  => $target_menu_id,
				'message' => __( 'Target menu does not exist.', 'eies-menu-fix' ),
			);
		}

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT post_id, meta_value FROM {$wpdb->postmeta}
			 WHERE meta_key = '_elementor_data' AND meta_value LIKE %s",
			'%ms_lms_mega_menu%'
		) );

		$valid_ids = array_map( 'intval', wp_list_pluck( wp_get_nav_menus(), 'term_id' ) );
		$scanned   = count( $rows );
		$fixed     = 0;

		foreach ( $rows as $row ) {
			$data = (string) $row->meta_value;

			$new_data = preg_replace_callback(
				'/"menu":"(\d+)"/',
				function ( $m ) use ( $valid_ids, $target_menu_id ) {
					$id = (int) $m[1];
					if ( in_array( $id, $valid_ids, true ) ) {
						return $m[0];
					}
					return '"menu":"' . $target_menu_id . '"';
				},
				$data
			);

			if ( $new_data !== null && $new_data !== $data ) {
				update_post_meta( $row->post_id, '_elementor_data', wp_slash( $new_data ) );
				$fixed++;
			}
		}

		if ( $fixed > 0 && class_exists( '\\Elementor\\Plugin' ) ) {
			$elementor = \Elementor\Plugin::$instance;
			if ( $elementor && isset( $elementor->files_manager ) ) {
				$elementor->files_manager->clear_cache();
			}
		}

		return array(
			'fixed'   => $fixed,
			'scanned' => $scanned,
			'target'  => $target_menu_id,
			'message' => sprintf(
				/* translators: 1: number of templates patched, 2: number scanned, 3: target menu id */
				__( 'Patched %1$d of %2$d Elementor template(s); rebound broken refs to menu #%3$d.', 'eies-menu-fix' ),
				$fixed,
				$scanned,
				$target_menu_id
			),
		);
	}
}
