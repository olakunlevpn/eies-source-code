<?php
// phpcs:ignoreFile

class MSLMS_ZoomAdminMenus {

	/**
	 * @return StmZoomAdminMenus constructor.
	 */
	public function __construct() {
		add_action(
			'admin_menu',
			function () {
				// Create top-level Zoom Conference menu near Analytics
				add_menu_page(
					__( 'Zoom Conference', 'masterstudy-lms-learning-management-system-pro' ),
					__( 'Zoom Conference', 'masterstudy-lms-learning-management-system-pro' ),
					'manage_options',
					'mslms_zoom',
					'mslms_admin_pages',
					'dashicons-video-alt2',
					4
				);

				self::admin_submenu_pages();
			},
			100
		);

		if ( is_admin() ) {
			// admin_settings_page() moved to MasterStudy integration
			add_filter(
				'stm_wpcfto_autocomplete_stm_alternative_hosts',
				array(
					$this,
					'get_autocomplete_users_options',
				),
				100
			);
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ), 100 );

		add_action( 'admin_head', array( $this, 'admin_head' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( MSLMS_ZOOM_FILE ), array( $this, 'plugin_action_links' ) );

	}

	/**
	 * Get Users for Autocomplete
	 *
	 * @return array
	 */
	public static function get_autocomplete_users_options() {
		$users  = MSLMS_StmZoom::get_users_options();
		$result = array();
		foreach ( $users as $id => $user ) {
			$result[] = array(
				'id'        => $id,
				'title'     => $user,
				'post_type' => '',
			);
		}

		return $result;
	}

	/**
	 * Creating Submenu Pages under Zoom menu
	 */
	public static function admin_submenu_pages() {
		$pages = array(
			array(
				'slug'      => 'mslms_zoom_users',
				'menu_slug' => 'mslms_zoom_users',
				'label'     => esc_html__( 'Users', 'masterstudy-lms-learning-management-system-pro' ),
			),
			array(
				'slug'      => 'mslms_zoom_add_user',
				'menu_slug' => 'mslms_zoom_add_user',
				'label'     => esc_html__( 'Add user', 'masterstudy-lms-learning-management-system-pro' ),
			),
			array(
				'slug'      => 'mslms_zoom_reports',
				'menu_slug' => 'mslms_zoom_reports',
				'label'     => esc_html__( 'Statistics', 'masterstudy-lms-learning-management-system-pro' ),
			),
			array(
				'slug'      => 'mslms_zoom_assign_host_id',
				'menu_slug' => 'mslms_zoom_assign_host_id',
				'label'     => esc_html__( 'Assign host id', 'masterstudy-lms-learning-management-system-pro' ),
				'hidden'    => true,
			),
		);

		// Webinars submenu is now handled by the post type registration
		// No need to add it manually

		foreach ( $pages as $page ) {
			add_submenu_page(
				'mslms_zoom',
				$page['label'],
				$page['label'],
				'manage_options',
				$page['menu_slug'],
				'mslms_admin_pages'
			);
		}

		foreach ( $pages as $page ) {
			if ( ! empty( $page['hidden'] ) ) {
				remove_submenu_page( 'mslms_zoom', $page['menu_slug'] );
			}
		}

		/* Remove default top-level duplicate submenu */
		remove_submenu_page( 'mslms_zoom', 'mslms_zoom' );

		do_action( 'MSLMS_ZOOM_admin_submenu_pages' );
	}

	/**
	 * Creating Plugin Settings
	 */

	/**
	 * Enqueue Admin Styles & Scripts
	 */
	public function admin_enqueue() {
		wp_enqueue_style( 'MSLMS_ZOOM_admin', MSLMS_ZOOM_URL . 'build/css/main.css', false, STM_LMS_PRO_VERSION );
	}

	/**
	 * Define WP Admin Ajax URL
	 */
	public function admin_head() { ?>
		<script type="text/javascript">
			var MSLMS_ZOOM_ajaxurl = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
		</script>
		<?php
	}

	/**
	 * Add Custom Links to Plugins page
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=mslms_zoom_settings' ), esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ) );
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Add Meetings Synchronize Scripts
	 */
}
