<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\CourseDataCache;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

final class GradeSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		$grade_calculator  = GradeCalculator::get_instance();
		$max_point         = $grade_calculator->get_max_by( 'point' );
		$grade             = $grade_calculator->calculate( intval( $data['final_grade'] ?? 0 ) );
		$course_data       = CourseDataCache::get_instance()->get_course_data( $data['course_id'], $data['featured_image_id'] ?? null );
		$total_quizzes     = $course_data['quizzes'] ?? 0;
		$total_assignments = $course_data['assignments'] ?? 0;

		return array(
			'start_time'  => gmdate( 'Y-m-d H:i:s', $data['start_time'] ?? time() ),
			'student'     => array(
				'id'    => $data['user_id'] ?? 0,
				'name'  => $data['display_name'] ?? '',
				'email' => $data['user_email'] ?? '',
			),
			'course'      => array(
				'id'             => $data['course_id'] ?? 0,
				'title'          => $data['course_title'] ?? '',
				'img'            => $course_data['image'] ?? '',
				'user_course_id' => $data['user_course_id'] ?? 0,
			),
			'quiz'        => array(
				'complete' => min( $total_quizzes, $data['passed_quizzes'] ?? 0 ),
				'total'    => $total_quizzes,
			),
			'assignment'  => array(
				'complete' => min( $total_assignments, $data['passed_assignments'] ?? 0 ),
				'total'    => $total_assignments,
			),
			'final_grade' => array(
				'badge'     => $grade['grade'] ?? '',
				'current'   => $grade['point'] ?? 0,
				'max_point' => $max_point,
				'color'     => $grade['color'] ?? '',
			),
		);
	}
}
