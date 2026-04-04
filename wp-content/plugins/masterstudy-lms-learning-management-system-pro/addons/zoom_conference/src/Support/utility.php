<?php
// phpcs:ignoreFile
/**
 * @return array of timezones
 */

function mslms_stm_zoom_generate_ics_calendar_build( $config, $recurring_data ) {
	$ics_props = array(
		'BEGIN:VCALENDAR',
		'VERSION:2.0',
		'PRODID:-// MasterStudy LMS plugin //NONSGML v1.0//EN',
		'CALSCALE:GREGORIAN',
		'BEGIN:VEVENT',
		'UID:mslms-' . time(),
		'SUMMARY:' . $config['title'],
		'LOCATION:' . $config['address'],
		'DESCRIPTION' . $config['description'],
		'URL;VALUE=URI:https://wordpress.org/plugins/masterstudy-lms-learning-management-system/',
	);

	$ics_props_param = mslms_stm_zoom_generate_calendar_params( $config, true );
	$ics_props       = array_merge( $ics_props, $ics_props_param );

	if ( isset( $recurring_data['calendar_options']['rrule'] ) ) {
		$ics_props[] = 'RRULE:' . $recurring_data['calendar_options']['rrule'];
	}

	$ics_props[] = 'BEGIN:VALARM';
	$ics_props[] = 'ACTION:DISPLAY';
	$ics_props[] = 'RIGGER;RELATED=START:-PT00H15M00S';
	$ics_props[] = 'BEGIN:VALARM';

	// Build ICS properties - add footer
	$ics_props[] = 'END:VEVENT';
	$ics_props[] = 'END:VCALENDAR';

	return implode( "\r\n", $ics_props );
}

/**
 * Require Admin Templates
 */
function mslms_admin_pages() {
	require_once MSLMS_ZOOM_PATH . '/src/Layouts/backend/dashboard.php';
}

/**
 * Template Manager
 *
 * @param $file
 *
 * @return bool|string
 */
function mslms_get_zoom_template( $file ) {
	$templates = array(
		get_stylesheet_directory() . '/zoom_templates/',
		get_template_directory() . '/zoom_templates/',
		MSLMS_ZOOM_PATH . '/src/Layouts/',
	);

	$templates = apply_filters( 'MSLMS_ZOOM_template_pathes', $templates );

	foreach ( $templates as $template ) {
		if ( file_exists( $template . $file ) ) {
			return $template . $file;
		}
	}

	return false;
}


/**
 * Generate url link to Google Calendar
 *
 * @param $config
 * @param $options
 *
 * @return string
 * @throws Exception
 */

function mslms_stm_zoom_generate_google_calendar( $config, $options ) {
	$url = 'https://calendar.google.com/calendar/render?action=TEMPLATE';

	$url .= mslms_stm_zoom_generate_calendar_params( $config, false );

	$url .= '&text=' . rawurlencode( $config['title'] );
	$url .= '&details=' . rawurlencode( $config['description'] );
	$url .= '&location=' . rawurlencode( $config['address'] );
	$url .= '&sf=true&output=xml';

	if ( isset( $options['calendar_options']['rrule'] ) ) {
		$url .= '&recur=RRULE:' . rawurlencode( $options['calendar_options']['rrule'] );
	}

	return $url;
}

function mslms_stm_zoom_generate_calendar_params( $config, $ics = false ) {

	//set timezone
	$timezone_set = isset( $config['timezone'] ) ? $config['timezone'] : 'UTC';
	date_default_timezone_set( $timezone_set ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions

	$duration = ! empty( $config['duration'] ) ? intval( $config['duration'] ) : 60;

	$start_date_time = strtotime( $config['start'] );
	$end_date_time   = strtotime( "+{$duration} minutes", $start_date_time );

	$utc_timezone        = new DateTimeZone( 'UTC' );
	$utc_start_date_time = new DateTime( '@' . $start_date_time, $utc_timezone );
	$utc_end_date_time   = new DateTime( '@' . $end_date_time, $utc_timezone );

	$date_format      = 'Ymd'; //no fixed time
	$date_time_format = 'Ymd\THis\Z';

	if ( $ics ) {

		$utc_stamp = new DateTime( 'now', $utc_timezone );

		$props[] = 'DTSTART:' . $utc_start_date_time->format( $date_time_format );
		$props[] = 'DTEND:' . $utc_end_date_time->format( $date_time_format );
		$props[] = 'DTSTAMP:' . $utc_stamp->format( $date_time_format );

		return $props;
	}

	$date_time_format = ! empty( $config['allDay'] ) ? $date_format : $date_time_format;

	return '&dates=' . $utc_start_date_time->format( $date_time_format ) . '/' . $utc_end_date_time->format( $date_time_format );
}

/**
 * Generate iCal Calendar
 *
 * @param $post_id
 *
 * @return string
 * @throws Exception
 */
function mslms_stm_zoom_generate_ics_calendar( $post_id = '' ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$zoom_data = get_post_meta( $post_id, 'stm_zoom_data', true );

	if ( ! empty( $post_id ) && ! empty( $zoom_data ) && ! empty( $zoom_data['id'] ) ) {
		$title         = get_the_title( $post_id );
		$agenda        = get_post_meta( $post_id, 'stm_agenda', true );
		$start_time    = get_post_meta( $post_id, 'stm_time', true );
		$timezone      = get_post_meta( $post_id, 'stm_timezone', true );
		$meeting_data  = MSLMS_StmZoom::meeting_time_data( $post_id );
		$meeting_start = $meeting_data['meeting_start'];

		$recurring_data = array();
		if ( class_exists( 'StmZoomRecurring' ) ) {
			$recurring_data = StmZoomRecurring::stm_product_recurring_meeting_data( $post_id, $zoom_data );
		}

		$config_calendar = array(
			'start'       => $meeting_start,
			'allDay'      => isset( $zoom_data['type'] ) && in_array( $zoom_data['type'], MSLMS_StmZoomAPITypes::TYPES_NO_FIXED, true ),
			'address'     => '',
			'title'       => $title,
			'description' => $agenda,
			'start_time'  => $start_time,
			'timezone'    => $timezone,
		);

		return mslms_stm_zoom_generate_ics_calendar_build( $config_calendar, $recurring_data );
	}
}