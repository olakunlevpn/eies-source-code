<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Student;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

class StudentMembershipSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'name'            => $data->membership_name,
			'price'           => $data->initial_payment,
			'date_subscribed' => gmdate( 'd.m.Y H:i', strtotime( $data->startdate ) ),
			'date_canceled'   => '0000-00-00 00:00:00' === $data->enddate ? '' : gmdate( 'd.m.Y H:i', strtotime( $data->enddate ) ),
		);
	}
}
