<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\Course;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class CourseInfoSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		$course = array();

		if ( isset( $data['basic_info'] ) ) {
			$course['basic_info'] = $data['basic_info'];
		}

		if ( isset( $data['requirements'] ) ) {
			$course['requirements'] = $data['requirements'];
		}

		if ( isset( $data['intended_audience'] ) ) {
			$course['intended_audience'] = $data['intended_audience'];
		}

		return $course;
	}
}
