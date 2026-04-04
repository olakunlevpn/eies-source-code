<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

final class CourseGradeSerializer extends AbstractSerializer {
	private $grade_calculator;
	private $max_point;

	public function __construct() {
		$this->grade_calculator = GradeCalculator::get_instance();
		$this->max_point        = $this->grade_calculator->get_max_by( 'point' );
	}

	public function toArray( $data ): array {
		$grade = $this->grade_calculator->calculate( intval( $data['final_grade'] ?? 0 ) );

		return array(
			'user_course_id'       => $data['user_course_id'] ?? 0,
			'course'               => $data['course_title'] ?? '',
			'student'              => $data['display_name'] ?? '',
			'enroll_date'          => gmdate( 'Y-m-d H:i:s', $data['start_time'] ?? time() ),
			'course_complete_date' => ! empty( $data['end_time'] )
				? gmdate( 'Y-m-d H:i:s', $data['end_time'] )
				: 'N/A',
			'grade'                => array(
				'badge'     => $grade['grade'] ?? '',
				'current'   => $grade['point'] ?? 0,
				'max_point' => $this->max_point,
				'range'     => $data['final_grade'] ?? 0,
				'color'     => $grade['color'] ?? '',
			),
			'exams'                => $this->serialize_exams( $data['exams'] ?? array() ),
		);
	}

	public function serialize_exams( $exams = array() ): array {
		if ( empty( $exams ) ) {
			return array();
		}

		$data = array();

		foreach ( $exams as $exam ) {
			$grade  = $this->grade_calculator->calculate( intval( $exam['grade'] ?? 0 ) );
			$result = array(
				'type'     => $exam['type'] ?? '',
				'title'    => $exam['title'] ?? '',
				'attempts' => $exam['attempts'] ?? '',
				'grade'    => array(),
			);

			if ( ! empty( $exam['attempts'] ) && ! empty( $grade ) ) {
				$result['grade'] = array(
					'badge'     => $grade['grade'] ?? '',
					'current'   => $grade['point'] ?? 0,
					'max_point' => $this->max_point,
					'range'     => $exam['grade'] ?? 0,
					'color'     => $grade['color'] ?? '',
				);
			}

			$data[] = $result;
		}

		return $data;
	}
}
