<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class SubscriptionPlanSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'id'                 => $data['id'],
			'name'               => $data['name'],
			'type'               => $data['type'],
			'description'        => $data['description'],
			'recurring_value'    => $data['recurring_value'],
			'recurring_interval' => $data['recurring_interval'],
			'billing_cycles'     => $data['billing_cycles'],
			'price'              => $data['price'],
			'sale_price'         => $data['sale_price'],
			'sale_price_from'    => $data['sale_price_from'],
			'sale_price_to'      => $data['sale_price_to'],
			'plan_features'      => json_decode( $data['plan_features'] ),
			'enrollment_fee'     => $data['enrollment_fee'],
			'trial_period'       => $data['trial_period'],
			'is_featured'        => $data['is_featured'],
			'featured_text'      => $data['featured_text'],
			'is_certified'       => $data['is_certified'],
			'is_enabled'         => $data['is_enabled'],
			'plan_order'         => $data['plan_order'],
			'items'              => $this->get_items( $data['items'] ?? array() ),
		);
	}

	private function get_items( array $data ): array {
		return array_map(
			function ( $item ) {
				return array(
					'object_type' => $item['object_type'],
					'object_id'   => $item['object_id'],
				);
			},
			$data
		);
	}
}
