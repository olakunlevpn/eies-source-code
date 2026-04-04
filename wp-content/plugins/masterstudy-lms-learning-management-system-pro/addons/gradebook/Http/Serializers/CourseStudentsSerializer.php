<?php

namespace MasterStudy\Lms\Pro\addons\gradebook\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class CourseStudentsSerializer extends AbstractSerializer {

	public function toArray( $data ): array {

		return array(
			'assignments_progress' => $data['assignments_progress'],
			'curriculum'           => $data['curriculum'],
			'lessons_progress'     => $data['lessons_progress'],
			'progress_percent'     => $data['progress_percent'],
			'quizzes_progress'     => $data['quizzes_progress'],
			'start_date'           => $data['start_date'],
			'user_data'            => $data['user_data'],
		);
	}
}
