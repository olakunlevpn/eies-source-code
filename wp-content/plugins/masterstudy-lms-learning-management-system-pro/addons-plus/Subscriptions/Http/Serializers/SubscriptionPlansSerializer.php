<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class SubscriptionPlansSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'id'                 => $data['id'],
			'name'               => $data['name'],
			'type'               => $data['type'],
			'price'              => $data['price'],
			'sale_price'         => $data['sale_price'],
			'recurring_value'    => $data['recurring_value'],
			'recurring_interval' => $data['recurring_interval'],
			'trial_period'       => $data['trial_period'],
			'is_featured'        => $data['is_featured'],
			'featured_text'      => $data['featured_text'],
			'is_certified'       => $data['is_certified'],
			'is_enabled'         => $data['is_enabled'],
			'plan_order'         => $data['plan_order'],
		);
	}
}
