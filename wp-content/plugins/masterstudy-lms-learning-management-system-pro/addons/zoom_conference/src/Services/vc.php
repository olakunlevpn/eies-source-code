<?php
// phpcs:ignoreFile
use Zoom\Api\Users;

class MSLMS_StmZoom {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
		add_action( 'wp_head', array( $this, 'head' ) );
		add_shortcode( 'MSLMS_ZOOM_conference', array( $this, 'add_meeting_shortcode' ) );

		if ( ! is_plugin_active( self::$compability_old_version ) ) {
			add_shortcode( 'stm_zoom_conference', array( $this, 'add_meeting_shortcode' ) );
		}
		add_filter( 'template_include', array( $this, 'single_zoom_template' ), 200 );
	}

	public function single_zoom_template( $template ) {
		global $post;

		if ( isset( $post->post_type ) && in_array( $post->post_type, array( 'ms-zoom', 'stm-zoom', 'stm-zoom-webinar' ), true ) ) {
			$post_id = get_the_ID();
			if ( ! empty( $_GET['show_meeting'] ) ) { // phpcs:ignore
				$template = mslms_get_zoom_template( 'ui/view.php' );
			} elseif ( ! empty( $_GET['ical_export'] ) ) { // phpcs:ignore
				header( 'Content-type: text/calendar; charset=utf-8' );
				header( 'Content-Disposition: inline; filename=calendar_' . $post_id . '.ics' );
				echo mslms_stm_zoom_generate_ics_calendar();//phpcs:ignore
				exit();
			} else {
				$template = mslms_get_zoom_template( 'ui/main.php' );
			}
		}

		return $template;
	}

	public function frontend_enqueue() {
		if ( ! is_plugin_active( self::$compability_old_version ) ) {
			wp_enqueue_script( 'stm_jquery.countdown', MSLMS_ZOOM_URL . 'build/js/timer.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
			wp_enqueue_script( 'MSLMS_ZOOM_main', MSLMS_ZOOM_URL . 'build/js/base.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
			wp_enqueue_style( 'MSLMS_ZOOM_main', MSLMS_ZOOM_URL . 'build/css/front.css', false, STM_LMS_PRO_VERSION );
		}
	}

	/**
	 * Define Frontend Translation Variables
	 */
	public function head() {
		if ( ! is_plugin_active( self::$compability_old_version ) ) {
			?>
			<script>
				var daysStr = "<?php esc_html_e( 'Days', 'masterstudy-lms-learning-management-system-pro' ); ?>";
				var hoursStr = "<?php esc_html_e( 'Hours', 'masterstudy-lms-learning-management-system-pro' ); ?>";
				var minutesStr = "<?php esc_html_e( 'Minutes', 'masterstudy-lms-learning-management-system-pro' ); ?>";
				var secondsStr = "<?php esc_html_e( 'Seconds', 'masterstudy-lms-learning-management-system-pro' ); ?>";
			</script>
			<?php
		}
	}

	public function add_meeting_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'post_id'                   => '',
				'hide_content_before_start' => '',
			),
			$atts
		);

		$content                   = '';
		$hide_content_before_start = '';
		if ( ! empty( $atts['hide_content_before_start'] ) ) {
			$hide_content_before_start = '1';
		}
		if ( ! empty( $atts['post_id'] ) ) {
			$content = self::add_zoom_content( $atts['post_id'], $hide_content_before_start );
		}
		return $content;
	}

	public static function add_zoom_content( $post_id, $hide_content_before_start = '', $webinar = false ) {
		$content = '';
		if ( ! empty( $post_id ) ) {
			$post_id      = intval( $post_id );
			$meeting_data = self::meeting_time_data( $post_id );
		
			if ( ! empty( $meeting_data ) && ! empty( $meeting_data['meeting_start'] ) && ! empty( $meeting_data['meeting_date'] ) ) {
				$meeting_start = $meeting_data['meeting_start'];
				$meeting_date  = $meeting_data['meeting_date'];
				$is_started    = $meeting_data['is_started'];

				$zoom_data      = get_post_meta( $post_id, 'stm_zoom_data', true );
				
				$recurring_data = array();

				if ( class_exists( 'StmZoomRecurring' ) ) {
					$recurring_data = StmZoomRecurring::stm_product_recurring_meeting_data( $post_id, $zoom_data );

					//no fixed time
					if ( isset( $zoom_data['type'] ) && in_array( strval( $zoom_data['type'] ), MSLMS_StmZoomAPITypes::TYPES_NO_FIXED, true ) ) {
						$is_started = true;
					}
				}

				if ( ! $is_started ) {
					$content = self::countdown( $meeting_date, false, $webinar );
				} elseif ( isset( $recurring_data['next_meeting_start'] ) ) {
					$content = self::countdown( $recurring_data['next_meeting_start'], false, $webinar );
				} else {
					$hide_content_before_start = '';
				}

				if ( empty( $hide_content_before_start ) ) {
					$content .= self::zoom_content( $post_id, $meeting_start, $webinar, $zoom_data, $recurring_data );
				}
			}
		}
		return '<div class="stm_zoom_wrapper">' . $content . '</div>';
	}

	public static function meeting_time_data( $post_id ) {
		if ( empty( $post_id ) ) {
			return false;
		}

		$has_post = get_post_status( $post_id );
		if ( empty( $has_post ) ) {
			return false;
		}

		$r        = array();
		$post_id  = intval( $post_id );
		$provider = get_post_meta( $post_id, 'stm_select_gm_zoom', true );
		
		
		if ( empty( $provider ) || 'zoom' === $provider ) {
			$start_date = get_post_meta( $post_id, 'stm_date', true );
			$start_time = get_post_meta( $post_id, 'stm_time', true );
		} else {
			$start_date = get_post_meta( $post_id, 'stm_start_date', true );
			$start_time = get_post_meta( $post_id, 'stm_start_time', true );
		}

		$timezone      = get_post_meta( $post_id, 'stm_timezone', true );
		
		$sanitized_date = $start_date;
		
		$meeting_start = strtotime( 'today', ( $sanitized_date / 1000 ) );

		if ( ! empty( $start_time ) ) {
			$time = explode( ':', $start_time ?? '' );
			if ( is_array( $time ) && count( $time ) === 2 ) {
				$meeting_start = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $meeting_start );
			}
		}

		$meeting_start = date( 'Y-m-d H:i:s', $meeting_start ); //phpcs:ignore
		if ( empty( $timezone ) ) {
			$timezone = 'UTC';
		}

		$meeting_date = new DateTime( $meeting_start, new DateTimeZone( $timezone ) );
		$meeting_date = $meeting_date->format( 'U' );
		$is_started   = ! ( $meeting_date > time() );


		$r['meeting_start'] = $meeting_start;
		$r['meeting_date']  = $meeting_date;
		$r['is_started']    = $is_started;

		return $r;
	}

	public static function countdown( $time = '', $hide_title = false, $webinar = false ) {
		if ( ! empty( $time ) ) {
			$countdown = '<div class="zoom_countdown_wrap">';
			if ( ! $hide_title ) {
				$title      = ( $webinar ) ? esc_html__( 'Webinar starts in', 'masterstudy-lms-learning-management-system-pro' ) : esc_html__( 'Meeting starts in', 'masterstudy-lms-learning-management-system-pro' );
				$countdown .= '<h2 class="countdown_title">' . $title . '</h2>';
			}
			$countdown .= '<div class="stm_zooom_countdown" data-timer="' . esc_attr( $time ) . '"></div></div>';

			return $countdown;
		}
	}

	public static function zoom_content( $post_id, $meeting_start, $webinar = false, $zoom_data = false, $recurring_data = false ) {
		global $post;

		$zoom_post = get_post( $post_id );

		if ( ! $zoom_post || empty( $zoom_data ) || empty( $zoom_data['id'] ) ) {
			return '';
		}

			$meeting_id  = sanitize_text_field( $zoom_data['id'] );
			$title       = get_the_title( $post_id );
			$agenda      = get_post_meta( $post_id, 'stm_agenda', true );
			$password    = get_post_meta( $post_id, 'stm_password', true );
			$start_time  = get_post_meta( $post_id, 'stm_time', true );
			$time_zone   = get_post_meta( $post_id, 'stm_timezone', true );
			$option_ids  = get_option( 'stm_wc_product_meeting_ids', array() );
			$exclude_ids = array_keys( $option_ids );

			$config_calendar = array(
				'start'       => $meeting_start,
				'allDay'      => isset( $zoom_data['type'] ) && in_array( strval( $zoom_data['type'] ), MSLMS_StmZoomAPITypes::TYPES_NO_FIXED, true ),
				'address'     => '',
				'title'       => $title,
				'description' => $agenda,
				'start_time'  => $start_time,
				'timezone'    => $time_zone,
			);

			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );

		$old_post = null;

		if ( isset( $GLOBALS['post'] ) ) {
			$old_post = $GLOBALS['post'];
		}

		ob_start();

			if ( 'ms-zoom' === $post->post_type ||'stm-zoom' === $post->post_type  ) {
				the_content();
			}

			?>
			<div class="stm_zoom_content">
				<?php if ( has_post_thumbnail( $post_id ) ) { ?>
					<div class="zoom_image">
						<?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
					</div>
				<?php } ?>
				<div class="zoom_content">
					<div class="zoom_info">
						<h2><?php echo esc_html( $title ); ?></h2>
						<?php if ( isset( $zoom_data['type'] ) && in_array( strval( $zoom_data['type'] ), MSLMS_StmZoomAPITypes::TYPES_NO_FIXED, true ) ) { ?>
							<div class="zoom-recurring-no_fixed_time"><?php esc_html_e( 'No fixed time', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
						<?php } elseif ( isset( $zoom_data['type'] ) && in_array( strval( $zoom_data['type'] ), MSLMS_StmZoomAPITypes::TYPES_RECURRING, true ) && ! empty( $recurring_data ) ) { ?>
							<div class="zoom-recurring">
								<?php if ( isset( $recurring_data['start_date'] ) ) : ?>
									<div class="zoom-recurring__from">
										<span class="zoom-recurring--title"><?php esc_html_e( 'From:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
										<span class="zoom-recurring--content"><?php echo esc_html( date_i18n( $date_format . ' ' . $time_format, $recurring_data['next_meeting_start'] ) ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( isset( $recurring_data['end_date'] ) ) : ?>
									<div class="zoom-recurring__to">
										<span class="zoom-recurring--title"><?php esc_html_e( 'To:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
										<span class="zoom-recurring--content"><?php echo esc_html( date_i18n( $date_format . ' ' . $time_format, $recurring_data['end_meeting_date'] ) ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( isset( $recurring_data['repeat_interval'] ) ) : ?>
									<div class="zoom-recurring__interval">
										<span class="zoom-recurring--title">
											<?php ( $webinar ) ? esc_html_e( 'Webinar recurrence:', 'masterstudy-lms-learning-management-system-pro' ) : esc_html_e( 'Meeting recurrence:', 'masterstudy-lms-learning-management-system-pro' ); ?>
										</span>
									<span class="zoom-recurring--content"><?php echo esc_html( $recurring_data['repeat_interval'] ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						<?php } elseif ( ! empty( $meeting_start ) ) { ?>
							<div class="date">
								<span><?php ( $webinar ) ? esc_html_e( 'Webinar date', 'masterstudy-lms-learning-management-system-pro' ) : esc_html_e( 'Meeting date', 'masterstudy-lms-learning-management-system-pro' ); ?> </span>
								<b>
									<?php
									$timestamp = strtotime( $meeting_start );

									$formatted_date = wp_date(
										get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
										$timestamp
									);

									echo esc_html( $formatted_date );

									?>
								</b>
							</div>
						<?php } ?>
						<div class="stm-calendar-links">
							<span><?php echo esc_html__( 'Add to:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
							<a href="<?php echo esc_url( mslms_stm_zoom_generate_google_calendar( $config_calendar, $recurring_data ) ); ?>"><?php echo esc_html__( 'Google Calendar', 'masterstudy-lms-learning-management-system-pro' ); ?></a>
							,
							<a href="<?php echo esc_attr( add_query_arg( array( 'ical_export' => '1' ), get_permalink( $post_id ) ) ); ?>
								" class="" target="_blank">
								<?php echo esc_html__( 'iCal Export', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</a>
						</div>
						<?php if ( ! in_array( $post_id, $exclude_ids, true ) ) : ?>
							<?php if ( ! empty( $password ) ) { ?>
								<div class="password">
									<span><?php esc_html_e( 'Password: ', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
									<span class="value"><?php echo esc_html( $password ); ?></span>
								</div>
							<?php } ?>
							<a href="
							<?php
							echo esc_attr( add_query_arg( array( 'show_meeting' => '1' ), get_permalink( $post_id ) ) ); ?>
								" class="btn stm-join-btn join_in_menu" target="_blank">
								<?php esc_html_e( 'Join in browser', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</a>
							<a href="https://zoom.us/j/<?php echo esc_attr( $meeting_id ); ?>" class="btn stm-join-btn outline" target="_blank">
								<?php esc_html_e( 'Join in zoom app', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
				<div class="zoom_description">
					<?php if ( ! empty( $agenda ) ) { ?>
						<div class="agenda">
							<?php echo wp_kses_post( $agenda ); ?>
						</div>
					<?php } ?>
					<div id="zmmtg-root"></div>
					<div id="aria-notify-area"></div>
				</div>
			</div>
			<?php
		$output = ob_get_clean();
		// Restore original global post
		if ( $old_post ) {
			$GLOBALS['post'] = $old_post;
			setup_postdata( $old_post );
		} else {
			wp_reset_postdata();
		}

		return $output;
	}

	public static function MSLMS_ZOOM_get_users_list( $options = array() ) {

		$users_list = array();
		$users_data = new Users();
		
		try {
			$users_list = $users_data->userlist( $options );
		} catch ( Exception $e ) {
			error_log( 'MasterStudy: Zoom API Exception: ' . $e->getMessage() );
		}

		return $users_list;
	}

	public static function MSLMS_ZOOM_get_users() {
		$users = get_transient( 'mslms_zoom_users' );

		if ( empty( $users ) ) {
			$users_list = self::MSLMS_ZOOM_get_users_list( array( 'page_size' => 300 ) );
			
			if ( ! empty( $users_list ) && ! empty( $users_list['users'] ) ) {
				$users = $users_list['users'];
				set_transient( 'mslms_zoom_users', $users, 36000 );
			}
		}

		return $users;
	}

	public static function MSLMS_ZOOM_get_users_pagination( $page_number = 1 ) {
		$options = array(
			'page_size' => 300,
		);

		return self::MSLMS_ZOOM_get_users_list( $options );
	}

	public static function get_users_options() {
		$users = self::MSLMS_ZOOM_get_users();
		if ( ! empty( $users ) ) {
			foreach ( $users as $user ) {
				$first_name       = $user['first_name'];
				$last_name        = $user['last_name'];
				$email            = $user['email'];
				$id               = $user['id'];
				$user_list[ $id ] = $first_name . ' ' . $last_name . ' (' . $email . ')';
			}
		} else {
			return array();
		}
		return $user_list;
	}

	public static string $compability_old_version = 'eroom-zoom-meetings-webinar/eroom-zoom-meetings-webinar.php';

}
