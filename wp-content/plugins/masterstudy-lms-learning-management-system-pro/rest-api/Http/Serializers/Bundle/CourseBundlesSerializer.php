<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Bundle;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class CourseBundlesSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		// Convert object to array safely
		$data = (array) $data;

		return array(
			'number'         => $data['number'] ?? 0,
			'bundle_id'      => $data['bundle_id'] ?? '',
			'bundle_name'    => $data['bundle_name'] ?? 0,
			'bundle_slug'    => $data['bundle_slug'] ?? 0,
			'formatted_date' => $data['formatted_date'] ?? '',
			'total_orders'   => $data['total_orders'] ?? '',
		);
	}
}
