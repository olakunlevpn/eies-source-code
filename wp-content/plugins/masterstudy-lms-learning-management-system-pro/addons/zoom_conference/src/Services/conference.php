<?php
// phpcs:ignoreFile


use Zoom\Api\Meetings;

new STM_LMS_Zoom_Conference();

class STM_LMS_Zoom_Conference {

	public function __construct() {
		$this->migrate_from_old_settings();

		add_filter( 'stm_lms_course_item_content', array( $this, 'course_item_content' ), 10, 4 );

		// Handle both lesson creation and updates via masterstudy_lms_save_lesson hook
		add_action( 'masterstudy_lms_save_lesson', array( $this, 'handle_lesson_save' ), 10, 2 );

		add_filter( 'stm_lms_show_item_content', array( $this, 'show_item_content' ), 10, 3 );

		add_filter( 'wp_ajax_install_zoom_addon', array( $this, 'install_zoom_addon' ), 10, 2 );

		add_filter(
			'stm_lms_duration_field_type',
			function () {
				return 'number';
			}
		);
		add_action( 'wpcfto_options_page_setup', array( $this, 'mslms_zoom_settings_page' ) );
	}

	public static function install_zoom_addon() {
		check_ajax_referer( 'install_zoom_addon', 'nonce' );
		$install_plugin = STM_LMS_PRO_Plugin_Installer::install_plugin(
			array(
				'slug' => 'masterstudy-lms-learning-management-system-pro',
			)
		);
		wp_send_json( $install_plugin );
	}

	public static function show_item_content( $show, $post_id, $item_id ) {
		if ( self::is_stream( $item_id ) ) {
			return false;
		}

		return $show;
	}

	public static function course_item_content( $content, $post_id, $item_id, $data ) {
		if ( self::is_stream( $item_id ) ) {
			ob_start();
			STM_LMS_Templates::show_lms_template( 'course-player/zoom-conference', compact( 'post_id', 'item_id', 'data' ) );

			return ob_get_clean();
		}

		return $content;
	}

	public static function is_stream( $post_id ) {
		$type = get_post_meta( $post_id, 'type', true );

		return 'zoom_conference' === $type;

	}

	public static function stream_end_time( $item_id ) {
		$end_date = get_post_meta( $item_id, 'stream_end_date', true );
		$end_time = get_post_meta( $item_id, 'stream_end_time', true );
		$timezone = get_post_meta( $item_id, 'timezone', true );

		if ( empty( $end_date ) ) {
			return '';
		}

		$stream_end = strtotime( 'today', $end_date / 1000 );

		if ( ! empty( $end_time ) ) {
			$time = explode( ':', $end_time ?? '' );
			if ( is_array( $time ) && count( $time ) === 2 && ! empty( $timezone ) ) {
				$stream_end = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $stream_end );

				$date = new DateTime( '@' . $stream_end );
				$date->setTimezone( new DateTimeZone( $timezone ) );
				$stream_end = (int) $date->format( 'U' );
			}
		}

		return $stream_end;
	}

	public static function is_stream_ended( $item_id ) {
		$stream_end = self::stream_end_time( $item_id );

		if ( empty( $stream_end ) ) {
			return true;
		}

		if ( $stream_end > time() ) {
			return false;
		}

		return true;
	}

	/**
	 * Create Zoom meeting via API
	 */
	public static function create_zoom_meeting_via_api( $meeting_id ) {
		// Get meeting data
		$title      = get_the_title( $meeting_id );
		$agenda     = get_post_meta( $meeting_id, 'stm_agenda', true );
		$start_date = get_post_meta( $meeting_id, 'stm_date', true );
		$start_time = get_post_meta( $meeting_id, 'stm_time', true );
		$timezone   = get_post_meta( $meeting_id, 'stm_timezone', true );
		$password   = get_post_meta( $meeting_id, 'stm_password', true );
		$host_id    = get_post_meta( $meeting_id, 'stm_host', true );

		// Get Zoom settings - use MasterStudy settings (ms_zoom_settings)
		$settings = get_option( 'ms_zoom_settings', array() );

		$api_key    = ! empty( $settings['auth_client_id'] ) ? $settings['auth_client_id'] : '';
		$api_secret = ! empty( $settings['auth_client_secret'] ) ? $settings['auth_client_secret'] : '';

		if ( empty( $api_key ) || empty( $api_secret ) || empty( $host_id ) ) {
			return;
		}

		// Convert start date/time to proper format
		$meeting_start = strtotime( 'today', ( $start_date / 1000 ) );
		if ( ! empty( $start_time ) ) {
			$time = explode( ':', $start_time ?? '' );
			if ( is_array( $time ) && count( $time ) === 2 ) {
				$meeting_start = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $meeting_start );
			}
		}

		$meeting_start = gmdate( 'Y-m-d\TH:i:s', $meeting_start );

		// Get meeting settings from post meta
		$join_before_host   = get_post_meta( $meeting_id, 'stm_join_before_host', true );
		$host_video         = get_post_meta( $meeting_id, 'stm_host_join_start', true );
		$participants_video = get_post_meta( $meeting_id, 'stm_start_after_participants', true );
		$mute_participants  = get_post_meta( $meeting_id, 'stm_mute_participants', true );
		$enforce_login      = get_post_meta( $meeting_id, 'stm_enforce_login', true );

		// Prepare meeting data for Zoom API
		$data = array(
			'topic'      => $title,
			'type'       => 2,
			'start_time' => $meeting_start,
			'agenda'     => $agenda,
			'timezone'   => $timezone,
			'settings'   => array(
				'join_before_host'       => $join_before_host ? true : false,
				'host_video'             => $host_video ? true : false,
				'participant_video'      => $participants_video ? true : false,
				'mute_upon_entry'        => $mute_participants ? true : false,
				'meeting_authentication' => $enforce_login ? true : false,
			),
		);

		if ( ! empty( $password ) ) {
			// Zoom password max length is 10 characters
			$data['password'] = substr( $password ?? '', 0, 10 );
		}

		// Create meeting via Zoom API
		try {
			$zoom_endpoint = new Meetings();

			$new_meeting = $zoom_endpoint->create( $host_id, $data );

			if ( ! empty( $new_meeting['id'] ) ) {
				update_post_meta( $meeting_id, 'stm_zoom_data', $new_meeting );

				do_action( 'stm_zoom_after_create_meeting', $meeting_id );
			}
		} catch ( Exception $e ) {
			// Silent fail
		}
	}
	public function stm_lms_settings_page( $setups ) {
		$setups[] = array(
			'page'        => array(
				'parent_slug' => 'stm-lms-settings',
				'page_title'  => 'Zoom Conference Settings',
				'menu_title'  => 'Zoom Settings2',
				'menu_slug'   => 'mslms_zoom_settings',
			),
			'fields'      => $this->stm_lms_settings(),
			'option_name' => 'ms_zoom_settings',
		);

		return $setups;

	}

	public function stm_lms_settings() {
		return apply_filters( 'ms_zoom_settings', array() );
	}

	/**
	 * Zoom Addon settings page integration
	 */
	public function mslms_zoom_settings_page( $setup ) {
		$fields = array(
			'tab_1' => array(
				'name'   => esc_html__( 'Main settings', 'masterstudy-lms-learning-management-system-pro' ),
				'fields' => array(
					'auth_account_id'    => array(
						'type'        => 'text',
						'group_title' => esc_html__( 'Server to Server Oauth Credentials', 'masterstudy-lms-learning-management-system-pro' ),
						'label'       => esc_html__( 'Account ID', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => '',
						'group'       => 'started',
					),
					'auth_client_id'     => array(
						'type'        => 'text',
						'label'       => esc_html__( 'Client ID', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => '',
						'description' => sprintf( '%1s <a href="https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/zoom-video-conferencing#server-to-server-oauth-credentials" target="_blank">%2s</a>  %3s ', esc_html__( 'Please follow this ', 'masterstudy-lms-learning-management-system-pro' ), esc_html__( 'guide', 'masterstudy-lms-learning-management-system-pro' ), esc_html__( ' to generate API values from Zoom App Marketplace using your Zoom account.', 'masterstudy-lms-learning-management-system-pro' ) ),
					),
					'auth_client_secret' => array(
						'type'  => 'text',
						'label' => esc_html__( 'Client Secret', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => '',
						'group' => 'ended',
					),
					'sdk_key'            => array(
						'group_title' => esc_html__( 'Meeting SDK credentials', 'masterstudy-lms-learning-management-system-pro' ),
						'group'       => 'started',
						'type'        => 'text',
						'label'       => esc_html__( 'Client ID', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => '',
						'description' => sprintf( '%1s <a href="https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/zoom-video-conferencing#meeting-sdk-credentials" target="_blank">%2s</a> ', esc_html__( 'To make Join in Browser option work please generate API following this', 'masterstudy-lms-learning-management-system-pro' ), esc_html__( 'guide', 'masterstudy-lms-learning-management-system-pro' ) ),
					),
					'sdk_secret'         => array(
						'type'  => 'text',
						'label' => esc_html__( 'Client Secret', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => '',
						'group' => 'ended',
					),
					'generate_password'  => array(
						'type'        => 'checkbox',
						'label'       => esc_html__( 'Generate password', 'masterstudy-lms-learning-management-system-pro' ),
						'value'       => false,
						'description' => esc_html__( 'Auto-generation of the password for meeting/webinar if left empty', 'masterstudy-lms-learning-management-system-pro' ),
					),
				),
			),
		);

		$setup[] = array(
			'option_name' => 'ms_zoom_settings',
			'page'        => array(
				'page_title'  => esc_html__( 'Zoom Settings', 'masterstudy-lms-learning-management-system-pro' ),
				'menu_title'  => '<span class="stm-lms-settings-menu">' . esc_html__( 'Zoom Settings', 'masterstudy-lms-learning-management-system-pro' ) . '</span>',
				'menu_slug'   => 'mslms_zoom_settings',
				'icon'        => 'dashicons-video-alt2',
				'position'    => ( stm_lms_addons_menu_position() + 6 ),
				'parent_slug' => 'stm-lms-settings',
			),
			'fields'      => $fields,
		);

		return $setup;
	}


	/**
	 * Handle lesson save (both creation and updates)
	 */
	public function handle_lesson_save( $lesson_id, $lesson_data ) {
		if ( isset( $lesson_data['type'] ) && $lesson_data['type'] === 'zoom_conference' ) {
			$meeting_id = get_post_meta( $lesson_id, 'meeting_created', true );

			if ( empty( $meeting_id ) ) {
				// This is a new lesson - create the meeting
				$this->create_zoom_meeting_post( $lesson_id, $lesson_data );
			} else {
				// This is an existing lesson - update the meeting
				$this->update_zoom_meeting_post( $lesson_id, $lesson_data, $meeting_id );
			}
		}
	}

	/**
	 * Update existing Zoom meeting post and sync to zoom.us
	 */
	private function update_zoom_meeting_post( $lesson_id, $lesson_data, $meeting_id ) {
		$user_id   = get_current_user_id();
		$user_host = get_the_author_meta( 'stm_lms_zoom_host', $user_id );

		if ( empty( $user_host ) ) {
			return;
		}

		$agenda                    = $lesson_data['excerpt'] ?? '';
		$timezone                  = $lesson_data['zoom_conference_timezone'] ?? 'UTC';
		$password                  = $lesson_data['zoom_conference_password'] ?? '';
		$stream_start_date         = $lesson_data['zoom_conference_start_date'] ?? '';
		$stream_start_time         = $lesson_data['zoom_conference_start_time'] ?? '';
		$join_before_host          = $lesson_data['zoom_conference_join_before_host'] ?? '';
		$option_host_video         = $lesson_data['zoom_conference_host_video'] ?? '';
		$option_participants_video = $lesson_data['zoom_conference_participants_video'] ?? '';
		$option_mute_participants  = $lesson_data['zoom_conference_mute_participants'] ?? '';
		$option_enforce_login      = $lesson_data['zoom_conference_enforce_login'] ?? '';

		// Update the meeting post meta
		update_post_meta( $meeting_id, 'stm_host', $user_host );
		update_post_meta( $meeting_id, 'stm_agenda', $agenda );
		update_post_meta( $meeting_id, 'stm_date', $stream_start_date );
		update_post_meta( $meeting_id, 'stm_time', $stream_start_time );
		update_post_meta( $meeting_id, 'stm_timezone', $timezone );
		update_post_meta( $meeting_id, 'stm_password', $password );
		update_post_meta( $meeting_id, 'stm_join_before_host', $join_before_host );
		update_post_meta( $meeting_id, 'stm_host_join_start', $option_host_video );
		update_post_meta( $meeting_id, 'stm_start_after_participants', $option_participants_video );
		update_post_meta( $meeting_id, 'stm_mute_participants', $option_mute_participants );
		update_post_meta( $meeting_id, 'stm_enforce_login', $option_enforce_login );

		// Also store in the format expected by the hydrate filter
		update_post_meta( $lesson_id, 'join_before_host', $join_before_host );
		update_post_meta( $lesson_id, 'option_host_video', $option_host_video );
		update_post_meta( $lesson_id, 'option_participants_video', $option_participants_video );
		update_post_meta( $lesson_id, 'option_mute_participants', $option_mute_participants );
		update_post_meta( $lesson_id, 'option_enforce_login', $option_enforce_login );

		// Update the meeting title
		wp_update_post( array(
			'ID'         => $meeting_id,
			'post_title' => get_the_title( $lesson_id ),
		) );

		// Update the meeting on zoom.us via API
		$this->update_zoom_meeting_via_api( $meeting_id, $stream_start_date, $stream_start_time, $timezone, $password, $user_host );
	}

	/**
	 * Update existing Zoom meeting via API
	 */
	private function update_zoom_meeting_via_api( $meeting_id, $start_date, $start_time, $timezone, $password, $host_id ) {
		// Get meeting data
		$title  = get_the_title( $meeting_id );
		$agenda = get_post_meta( $meeting_id, 'stm_agenda', true );

		// Get existing Zoom data
		$existing_zoom_data = get_post_meta( $meeting_id, 'stm_zoom_data', true );
		if ( empty( $existing_zoom_data['id'] ) ) {
			return;
		}

		$zoom_meeting_id = $existing_zoom_data['id'];

		// Get Zoom settings
		$settings   = get_option( 'ms_zoom_settings', array() );
		$api_key    = ! empty( $settings['auth_client_id'] ) ? $settings['auth_client_id'] : '';
		$api_secret = ! empty( $settings['auth_client_secret'] ) ? $settings['auth_client_secret'] : '';

		if ( empty( $api_key ) || empty( $api_secret ) || empty( $host_id ) ) {
			return;
		}

		// Convert start date/time to proper format
		$meeting_start = strtotime( 'today', ( $start_date / 1000 ) );
		if ( ! empty( $start_time ) ) {
			$time = explode( ':', $start_time ?? '' );
			if ( is_array( $time ) && count( $time ) === 2 ) {
				$meeting_start = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $meeting_start );
			}
		}

		$meeting_start = gmdate( 'Y-m-d\TH:i:s', $meeting_start );

		// Get meeting settings from meta
		$join_before_host   = get_post_meta( $meeting_id, 'stm_join_before_host', true );
		$host_video         = get_post_meta( $meeting_id, 'stm_host_join_start', true );
		$participants_video = get_post_meta( $meeting_id, 'stm_start_after_participants', true );
		$mute_participants  = get_post_meta( $meeting_id, 'stm_mute_participants', true );
		$enforce_login      = get_post_meta( $meeting_id, 'stm_enforce_login', true );

		// Prepare meeting data for Zoom API
		$data = array(
			'topic'      => $title,
			'type'       => 2,
			'start_time' => $meeting_start,
			'agenda'     => $agenda,
			'timezone'   => $timezone,
			'settings'   => array(
				'join_before_host'       => $join_before_host ? true : false,
				'host_video'             => $host_video ? true : false,
				'participant_video'      => $participants_video ? true : false,
				'mute_upon_entry'        => $mute_participants ? true : false,
				'meeting_authentication' => $enforce_login ? true : false,
			),
		);

		if ( ! empty( $password ) ) {
			// Zoom password max length is 10 characters
			$data['password'] = substr( $password ?? '', 0, 10 );
		}

		// Update meeting via Zoom API
		try {
			$zoom_endpoint   = new Meetings();
			$updated_meeting = $zoom_endpoint->update( $zoom_meeting_id, $data );

			if ( ! empty( $updated_meeting ) ) {
				// Update the stored Zoom data
				$existing_zoom_data = array_merge( $existing_zoom_data, $data );
				update_post_meta( $meeting_id, 'stm_zoom_data', $existing_zoom_data );
				do_action( 'stm_zoom_after_update_meeting', $meeting_id );
			}
		} catch ( Exception $e ) {
			// Silent fail
		}
	}

	/**
	 * Create Zoom meeting post without triggering save_post hooks
	 */
	private function create_zoom_meeting_post( $lesson_id, $lesson_data ) {
		$is_edit   = get_post_meta( $lesson_id, 'meeting_created', true );
		$user_id   = get_current_user_id();
		$user_host = get_the_author_meta( 'stm_lms_zoom_host', $user_id );

		if ( empty( $user_host ) ) {
			// Check Zoom API credentials first
			$settings   = get_option( 'ms_zoom_settings', array() );
			$api_key    = ! empty( $settings['auth_client_id'] ) ? $settings['auth_client_id'] : '';
			$api_secret = ! empty( $settings['auth_client_secret'] ) ? $settings['auth_client_secret'] : '';

			// Try to get any available Zoom host from settings or existing meetings
			$zoom_users = MSLMS_StmZoom::get_users_options();

			if ( ! empty( $zoom_users ) ) {
				// Use the first available Zoom host
				$available_hosts = array_keys( $zoom_users );
				$user_host       = $available_hosts[0];

				// Save this host to the user profile for future use
				update_user_meta( $user_id, 'stm_lms_zoom_host', $user_host );
			} else {
				return;
			}
		}

		$agenda                    = $lesson_data['excerpt'] ?? '';
		$timezone                  = $lesson_data['zoom_conference_timezone'] ?? 'UTC';
		$password                  = $lesson_data['zoom_conference_password'] ?? '';
		$stream_start_date         = $lesson_data['zoom_conference_start_date'] ?? '';
		$stream_start_time         = $lesson_data['zoom_conference_start_time'] ?? '';
		$join_before_host          = $lesson_data['zoom_conference_join_before_host'] ?? '';
		$option_host_video         = $lesson_data['zoom_conference_host_video'] ?? '';
		$option_participants_video = $lesson_data['zoom_conference_participants_video'] ?? '';
		$option_mute_participants  = $lesson_data['zoom_conference_mute_participants'] ?? '';
		$option_enforce_login      = $lesson_data['zoom_conference_enforce_login'] ?? '';

		$post_data = array(
			'post_title'  => get_the_title( $lesson_id ),
			'post_status' => 'publish',
			'post_author' => $user_id,
			'post_type'   => 'ms-zoom',
		);

		if ( $is_edit ) {
			$post_data['ID'] = intval( $is_edit );
		}

		$meeting_id = wp_insert_post( $post_data );

		update_post_meta( $lesson_id, 'meeting_created', $meeting_id );

		if ( ! empty( $meeting_id ) ) {
			update_post_meta( $meeting_id, 'stm_host', $user_host );
			update_post_meta( $meeting_id, 'stm_agenda', $agenda );
			update_post_meta( $meeting_id, 'stm_date', $stream_start_date );
			update_post_meta( $meeting_id, 'stm_time', $stream_start_time );
			update_post_meta( $meeting_id, 'stm_timezone', $timezone );
			update_post_meta( $meeting_id, 'stm_password', $password );
			update_post_meta( $meeting_id, 'stm_join_before_host', $join_before_host );
			update_post_meta( $meeting_id, 'stm_host_join_start', $option_host_video );
			update_post_meta( $meeting_id, 'stm_start_after_participants', $option_participants_video );
			update_post_meta( $meeting_id, 'stm_mute_participants', $option_mute_participants );
			update_post_meta( $meeting_id, 'stm_enforce_login', $option_enforce_login );

			// Also store in the format expected by the hydrate filter
			update_post_meta( $lesson_id, 'join_before_host', $join_before_host );
			update_post_meta( $lesson_id, 'option_host_video', $option_host_video );
			update_post_meta( $lesson_id, 'option_participants_video', $option_participants_video );
			update_post_meta( $lesson_id, 'option_mute_participants', $option_mute_participants );
			update_post_meta( $lesson_id, 'option_enforce_login', $option_enforce_login );

			// Create Zoom meeting via API
			$this->create_zoom_meeting_via_api( $meeting_id );
		}
	}


	public function migrate_from_old_settings() {
		$mslms_settings = get_option( 'ms_zoom_settings', array() );
		$old_settings   = get_option( 'ms_zoom_settings', array() );

		if ( empty( $mslms_settings ) && ! empty( $old_settings ) ) {
			$mslms_settings = array();

			// Migrate key credentials
			if ( ! empty( $old_settings['auth_client_id'] ) ) {
				$mslms_settings['auth_client_id'] = $old_settings['auth_client_id'];
			}
			if ( ! empty( $old_settings['auth_client_secret'] ) ) {
				$mslms_settings['auth_client_secret'] = $old_settings['auth_client_secret'];
			}
			if ( ! empty( $old_settings['auth_account_id'] ) ) {
				$mslms_settings['auth_account_id'] = $old_settings['auth_account_id'];
			}

			update_option( 'ms_zoom_settings', $mslms_settings );
		}
	}


}
