<?php

use \MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleOpenAuth;

function masterstudy_lms_get_google_meet_date_time( $post_id, $start ) {
	$date_key   = $start ? 'stm_gma_start_date' : 'stm_gma_end_date';
	$time_key   = $start ? 'stm_gma_start_time' : 'stm_gma_end_time';
	$meta_value = get_post_meta( $post_id, $date_key, true );
	$time_value = get_post_meta( $post_id, $time_key, true );

	$current_date = masterstudy_lms_validate_google_meet_start_date( $meta_value );
	$date_time    = gmdate( 'F j, Y', $current_date / 1000 );
	$time         = gmdate( 'g:i a', strtotime( $time_value ) );

	return "$date_time, $time";
}

function masterstudy_lms_validate_google_meet_start_date( $start_date ) {
	if ( is_numeric( $start_date ) && 0 !== $start_date ) {
		return $start_date;
	}

	return strtotime( 'today' ) . '000';
}

function masterstudy_lms_get_current_timezone() {
	$timezone_string = get_option( 'timezone_string' );
	if ( ! empty( $timezone_string ) ) {
		return $timezone_string;
	}

	$offset  = get_option( 'gmt_offset' );
	$hours   = (int) $offset;
	$minutes = abs( ( $offset - (int) $offset ) * 60 );
	$seconds = $hours * 60 * 60 + $minutes * 60;

	$timezone = timezone_name_from_abbr( '', $seconds, 1 );
	if ( false === $timezone ) {
		$timezone = timezone_name_from_abbr( '', $seconds, 0 );
	}

	return $timezone;
}

function masterstudy_lms_google_meet_config_passed() {
	if ( ! function_exists( 'wp_get_current_user' ) ) {
		include ABSPATH . 'wp-includes/pluggable.php';
	}

	$current_user_id  = ( \wp_get_current_user()->ID );
	$user_meet_config = get_user_meta( $current_user_id, GoogleOpenAuth::CONFIG_NAME, true );
	$user_meet_token  = get_user_meta( $current_user_id, GoogleOpenAuth::TOKEN_NAME, true );

	return ( ! empty( $user_meet_config ) && ! empty( $user_meet_token ) );
}

function masterstudy_lms_is_google_meet_page() {
	$query_params = $_SERVER['QUERY_STRING'];
	parse_str( $query_params, $params );

	return end( $params ) === 'google_meet_settings';
}

function masterstudy_lms_is_google_meet( $post_id ) {
	return 'stm-google-meets' === get_post_type( $post_id );
}

function masterstudy_lms_google_meet_start_time( $item_id ) {
	$start_date = get_post_meta( $item_id, 'stm_gma_start_date', true );
	$start_time = get_post_meta( $item_id, 'stm_gma_start_time', true );
	$time_zone  = get_post_meta( $item_id, 'stm_gma_timezone', true );

	if ( empty( $start_date ) ) {
		return '';
	}

	$meet_timezone     = new DateTimeZone( $time_zone );
	$offset            = $meet_timezone->getOffset( new DateTime() ) * - 1;
	$google_meet_start = strtotime( 'today', ( $start_date / 1000 ) ) + $offset;

	if ( ! empty( $start_time ) ) {
		$time = explode( ':', $start_time );
		if ( is_array( $time ) && count( $time ) === 2 ) {
			$google_meet_start = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $google_meet_start );
		}
	}

	return $google_meet_start;
}

function masterstudy_lms_is_google_meet_started( $item_id ) {
	$google_meet_start = masterstudy_lms_google_meet_start_time( $item_id );

	return empty( $google_meet_start ) || $google_meet_start <= time();
}

function masterstudy_lms_google_meet_visibility_types() {
	return array(
		'default' => esc_html__( 'Default', 'masterstudy-lms-learning-management-system-pro' ),
		'public'  => esc_html__( 'Public', 'masterstudy-lms-learning-management-system-pro' ),
		'private' => esc_html__( 'Private', 'masterstudy-lms-learning-management-system-pro' ),
	);
}
