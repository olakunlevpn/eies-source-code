<?php
// phpcs:ignoreFile

use Zoom\Controllers\Meetings;

class MSLMS_StmZoomPostTypes {

	/**
	 * @return StmZoomPostTypes constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'MSLMS_ZOOM_register_post_type' ), 10 );

		if ( is_admin() ) {
			add_filter( 'manage_stm-zoom_posts_columns', array( $this, 'stm_provider_column_title' ) );
			add_action( 'manage_stm-zoom_posts_custom_column', array( $this, 'stm_provider_column' ), 5, 2 );
		}

		add_action( 'wp_ajax_MSLMS_ZOOM_meeting_sign', array( $this, 'generate_signature' ) );

		add_action( 'wp_ajax_nopriv_MSLMS_ZOOM_meeting_sign', array( $this, 'generate_signature' ) );
	}

	/**
	 * Generate Signature
	 */
	public function generate_signature() {

		$request = file_get_contents( 'php://input' );

		$request        = json_decode( $request );
		$api_key        = $request->api_key;
		$meeting_number = $request->meetingNumber;
		$role           = $request->role;
		$settings       = get_option( 'ms_zoom_settings', array() );
		$api_secret     = ! empty( $settings['api_secret'] ) ? $settings['api_secret'] : '';

		$time = time() * 1000 - 30000;
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$data = base64_encode( $api_key . $meeting_number . $time . $role );
		$hash = hash_hmac( 'sha256', $data, $api_secret, true );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$_sig = $api_key . '.' . $meeting_number . '.' . $time . '.' . $role . '.' . base64_encode( $hash );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$res = rtrim( strtr( base64_encode( $_sig ), '+/', '-_' ), '=' );

		echo wp_json_encode( array( $res ) );

		wp_die();
	}

	public function MSLMS_ZOOM_register_post_type() {
		register_post_type(
			'ms-zoom',
			array(
				'labels'             => array(
					'name'          => esc_html__( 'Zoom Meetings', 'masterstudy-lms-learning-management-system-pro' ),
					'singular_name' => esc_html__( 'Zoom Meeting', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'public'             => true,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'publicly_queryable' => true,
				'exclude_from_search'=> false,
				'has_archive'        => false,
				'rewrite'            => array(
					'slug'       => 'ms-zoom',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'editor' ),
			)
		);
		if ( class_exists( 'StmZoom' ) ) {
			return;
		}

		// Fallback CPT registration used ONLY when eRoom is absent.
		register_post_type(
			'stm-zoom',
			array(
				'labels' => array(
					'name'          => __( 'Zoom Meetings', 'masterstudy-lms-learning-management-system-pro' ),
					'singular_name' => __( 'Zoom Meeting', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'public'             => true,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'publicly_queryable' => true,
				'exclude_from_search'=> false,
				'has_archive'        => false,
				'rewrite'            => array(
					'slug'       => 'stm-zoom',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'editor' ),
			)
		);
	}
	/**
	 * Delete Meeting & Webinar from Zoom
	 *
	 * @param $post_id
	 */
	public function stm_provider_column_title( $columns ) {
		$columns = array_slice( $columns, 0, 3, true ) +
			array( 'provider' => esc_html__( 'Provider', 'masterstudy-lms-learning-management-system-pro' ) ) +
			array_slice( $columns, 3, count( $columns ) - 1, true );
		return $columns;
	}
	public function stm_provider_column( $column_key, $post_id ) {
		if ( 'provider' === $column_key ) {
			$provider = get_post_meta( $post_id, 'stm_select_gm_zoom', true );
			if ( 'zoom' === $provider || empty( $provider ) ) {
				echo '<i class="stm-zoom-icon" title="' . esc_attr__( 'Zoom', 'masterstudy-lms-learning-management-system-pro' ) . '"></i>';
			} else {
				echo '<i class="stm-google-meet-icon" title="' . esc_attr__( 'Google Meet', 'masterstudy-lms-learning-management-system-pro' ) . '"></i>';
			}
		}
	}
}
