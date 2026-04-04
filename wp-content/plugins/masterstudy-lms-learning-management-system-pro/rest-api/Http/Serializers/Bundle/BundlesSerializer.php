<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Bundle;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class BundlesSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		// Convert object to array safely
		$data = (array) $data;

		return array(
			'number'       => $data['number'] ?? 0,
			'course_name'  => $data['post_title'] ?? '',
			'course_slug'  => $data['post_name'] ?? '',
			'enrollments'  => $data['current_students'] ?? 0,
			'date_created' => $data['formatted_date'] ?? '',
			'course_id'    => isset( $data['ID'] ) ? intval( $data['ID'] ) : 0,
		);
	}
}
