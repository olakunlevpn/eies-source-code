<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Bundle;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class BundleDataSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'revenue' => floatval( $data['revenue'] ?? 0 ),
			'orders'  => intval( $data['orders'] ?? 0 ),
			'period'  => $data['period'] ?? array(),
			'values'  => $data['values'] ?? array(),
		);
	}
}
