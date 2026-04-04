<?php
namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Course;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class CourseLessonSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'lesson_name'   => $data['lesson_name'],
			'completed'     => sprintf( '%d%%', intval( $this->progressSerializeHelper( $data['completed'] ) ) ),
			'dropped'       => sprintf( '%d%%', intval( $this->progressSerializeHelper( $data['dropped'] ) ) ),
			'not_completed' => intval( $this->progressSerializeHelper( $data['not_completed'] ) ),
			'total'         => intval( $data['total'] ),
			'lesson_type'   => str_replace( '_', ' ', $data['lesson_type'] ),
			'lesson_id'     => intval( $data['lesson_id'] ),
			'date_created'  => $data['lesson_date'],
		);
	}

	public function progressSerializeHelper( $value ) {
		return max( 0, min( 100, intval( $value ) ) );
	}
}
