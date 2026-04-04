<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Payout;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class PayoutOrderSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		$data = (array) $data;
		return array(
			'id'                     => $data['id'],
			'date_created'           => $data['date_created'],
			'date_created_formatted' => \STM_LMS_Helpers::format_date( $data['date_created'] ),
			'amount'                 => $data['price'],
			'amount_formatted'       => \STM_LMS_Helpers::display_price( $data['price'] ),
			'status'                 => $data['status'],
			'method'                 => $data['payment_code'],
		);
	}
}
