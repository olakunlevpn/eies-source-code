<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Custom_Stats {

	const CACHE_PREFIX = 'eies_stat_';
	const TYPES        = array( 'years', 'courses', 'students', 'instructors' );

	public function __construct() {
		add_shortcode( 'eies_stat', array( $this, 'render_shortcode' ) );
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'type'   => 'courses',
			'format' => 'number',
			'prefix' => '',
			'suffix' => '',
		), $atts, 'eies_stat' );

		$type = strtolower( $atts['type'] );
		if ( ! in_array( $type, self::TYPES, true ) ) {
			return '';
		}

		$value = self::get_stat( $type );

		if ( $atts['format'] === 'raw' ) {
			$formatted = (string) (int) $value;
		} else {
			$formatted = self::format_number( (int) $value );
		}

		return esc_html( $atts['prefix'] ) . esc_html( $formatted ) . esc_html( $atts['suffix'] );
	}

	public static function get_stat( $type ) {
		$cache_key = self::CACHE_PREFIX . $type;
		$cached    = get_transient( $cache_key );
		if ( $cached !== false ) {
			return (int) $cached;
		}

		$value = self::compute_stat( $type );

		if ( $value === null ) {
			// Computation failed — try previous cache, else 0.
			$previous = get_option( self::CACHE_PREFIX . $type . '_last_good', 0 );
			return (int) $previous;
		}

		$ttl = (int) EIES_Custom_Settings::get( 'cache_ttl', HOUR_IN_SECONDS );
		set_transient( $cache_key, $value, $ttl );
		update_option( self::CACHE_PREFIX . $type . '_last_good', $value, false );

		return (int) $value;
	}

	private static function compute_stat( $type ) {
		switch ( $type ) {
			case 'years':
				$founding = (int) EIES_Custom_Settings::get( 'founding_year', 0 );
				if ( $founding < 1900 ) {
					return 0;
				}
				return max( 0, (int) date( 'Y' ) - $founding );

			case 'courses':
				if ( ! post_type_exists( 'stm-courses' ) ) {
					return null;
				}
				$counts = wp_count_posts( 'stm-courses' );
				return isset( $counts->publish ) ? (int) $counts->publish : 0;

			case 'students':
				$roles = array_filter( array_map( 'trim', explode( ',', (string) EIES_Custom_Settings::get( 'student_roles', '' ) ) ) );
				return self::count_users_in_roles( $roles );

			case 'instructors':
				$role = trim( (string) EIES_Custom_Settings::get( 'instructor_role', '' ) );
				if ( $role === '' ) {
					return 0;
				}
				return self::count_users_in_roles( array( $role ) );
		}

		return null;
	}

	private static function count_users_in_roles( $roles ) {
		if ( empty( $roles ) ) {
			return 0;
		}
		$result = count_users();
		if ( empty( $result['avail_roles'] ) || ! is_array( $result['avail_roles'] ) ) {
			return 0;
		}
		$total = 0;
		foreach ( $roles as $role ) {
			if ( isset( $result['avail_roles'][ $role ] ) ) {
				$total += (int) $result['avail_roles'][ $role ];
			}
		}
		return $total;
	}

	public static function format_number( $n ) {
		$fmt = EIES_Custom_Settings::get( 'number_format', 'bo' );
		switch ( $fmt ) {
			case 'us':
				return number_format( (int) $n, 0, '.', ',' );
			case 'raw':
				return (string) (int) $n;
			case 'bo':
			default:
				return number_format( (int) $n, 0, ',', '.' );
		}
	}

	public static function flush_cache() {
		foreach ( self::TYPES as $type ) {
			delete_transient( self::CACHE_PREFIX . $type );
		}
	}

	public static function preview_all() {
		$out = array();
		foreach ( self::TYPES as $type ) {
			$out[ $type ] = self::format_number( self::get_stat( $type ) );
		}
		return $out;
	}
}
