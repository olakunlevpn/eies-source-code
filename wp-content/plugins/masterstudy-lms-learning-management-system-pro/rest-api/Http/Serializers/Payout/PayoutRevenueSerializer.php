<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Payout;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class PayoutRevenueSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'datasets_earnings' => $data['datasets_earnings'],
			'labels_earnings'   => $data['labels_earnings'],
		);
	}
}
