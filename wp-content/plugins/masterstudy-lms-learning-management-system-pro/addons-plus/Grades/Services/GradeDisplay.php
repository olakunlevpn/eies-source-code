<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Services;

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Enums\GradeType;

class GradeDisplay {
	private static $instance = null;
	private $grades_display;
	private $separator;
	private $grades_calculator;

	private function __construct() {
		$this->grades_calculator = GradeCalculator::get_instance();
		$this->grades_display    = \STM_LMS_Options::get_option( 'grades_display', 'grade' );
		$this->separator         = esc_js( \STM_LMS_Options::get_option( 'grades_scores_separator', '/' ) );
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function render( $grade_value ) {
		$grade_data = $this->grades_calculator->calculate( $grade_value );

		if ( ! $grade_data ) {
			return '';
		}

		$formatted_grade = '';

		switch ( $this->grades_display ) {
			case GradeType::GRADE:
				$formatted_grade = esc_html( $grade_data['grade'] );
				break;
			case GradeType::POINT:
				$formatted_grade = sprintf(
					'%.2f%s%d',
					$grade_data['point'],
					$this->separator,
					$this->grades_calculator->get_max_by( 'point' )
				);
				break;
			case GradeType::PERCENT:
				$formatted_grade = sprintf( '%d%%', $grade_value );
				break;
		}

		return sprintf(
			'<span style="background: %s;">%s</span>',
			esc_attr( $grade_data['color'] ),
			$formatted_grade
		);
	}

	public function detailed_render( $grade_value ): string {
		$grade_data = $this->grades_calculator->calculate( $grade_value );

		if ( $grade_data ) {
			return sprintf(
				'<span class="grade-badge" style="background: %s;">%s</span> (%.2f%s%d)',
				esc_html( $grade_data['color'] ),
				esc_html( $grade_data['grade'] ),
				esc_html( $grade_data['point'] ),
				$this->separator,
				$this->grades_calculator->get_max_by( 'point' )
			);
		}

		return '';
	}

	public function simple_render( $grade_value, $only_value = false ): string {
		$grade_data = $this->grades_calculator->calculate( $grade_value );

		switch ( $this->grades_display ) {
			case GradeType::GRADE:
				return esc_html( $grade_data['grade'] ?? 0 );
			case GradeType::POINT:
				$point = $grade_data['point'] ?? 0;
				return $only_value
					? $point
					: sprintf(
						/* translators: %s: Points */
						esc_html__( '%s Points', 'masterstudy-lms-learning-management-system-pro' ),
						$point
					);
			case GradeType::PERCENT:
				return sprintf( '%d%%', $grade_value );
			default:
				return '';
		}
	}
}
