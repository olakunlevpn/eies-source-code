<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Student;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

class StudentCourseSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'name'        => $data['name'],
			'started'     => gmdate( 'd.m.Y H:i', intval( $data['started'] ) ),
			'ended'       => $data['ended'] ? gmdate( 'd.m.Y H:i', intval( $data['ended'] ) ) : '-',
			'lessons'     => $data['lessons'],
			'quizzes'     => $data['quizzes'],
			'url'         => $data['url'],
			'assignments' => $data['assignments'] ?? '',
			'progress'    => (int) $data['progress'],
		);
	}
}
