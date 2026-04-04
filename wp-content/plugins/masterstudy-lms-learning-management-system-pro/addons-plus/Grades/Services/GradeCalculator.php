<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Services;

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Enums\GradeType;

class GradeCalculator {
	private static $instance = null;
	private array $grades_table;

	private function __construct() {
		$default_value = function_exists( 'stm_lms_settings_grades_default_values' )
			? stm_lms_settings_grades_default_values()
			: array();

		$this->grades_table = \STM_LMS_Options::get_option( 'grades_table', $default_value );
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function calculate( $grade_value ) {
		if ( ! is_numeric( $grade_value ) ) {
			return null;
		}

		foreach ( $this->grades_table as $setting ) {
			if ( $grade_value >= $setting['range'][0] && $grade_value <= $setting['range'][1] ) {
				return array(
					'grade' => $setting['grade'],
					'point' => $setting['point'],
					'color' => $setting['color'],
				);
			}
		}

		return null;
	}

	public function get_grades_table() {
		return $this->grades_table;
	}

	public function get_min_by( string $type ) {
		return $this->grades_table[ count( $this->grades_table ) - 1 ][ $type ] ?? 0;
	}

	public function get_max_by( string $type ) {
		return $this->grades_table[0][ $type ] ?? 0;
	}

	public function get_min_range() {
		return $this->grades_table[ count( $this->grades_table ) - 1 ]['range'][0] ?? 0;
	}

	public function get_max_range() {
		return $this->grades_table[0]['range'][1] ?? 0;
	}

	public function get_passing_grade( $passing_grade, $type = '' ) {
		if ( empty( $type ) ) {
			$type = \STM_LMS_Options::get_option( 'grades_display', 'grade' );
		}

		if ( GradeType::PERCENT === $type ) {
			return "$passing_grade%";
		}

		$grade = $this->calculate( $passing_grade );

		return $grade[ $type ] ?? '';
	}

	public function get_percent_by_type( $value, $type ) {
		$values = array_column( $this->grades_table, $type );
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		$index = array_search( $value, $values );

		if ( false === $index ) {
			return 0;
		}

		return $this->grades_table[ $index ]['range'][1];
	}
}
