<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Serializers\Revenue;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;
use STM_LMS_Cart;

final class StudentSerializer extends AbstractSerializer {
	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		if ( STM_LMS_Cart::woocommerce_checkout_enabled() ) {
			return array(
				'number'           => $data['number'] ?? 0,
				'student_id'       => intval( $data['student_id'] ) ?? 0,
				'courses'          => ( $data['courses'] + ( intval( $data['bundles'] ) / 2 ) + $data['purchased_groups'] ) ?? 0,
				'total_orders'     => $data['total_orders'] ?? 0,
				'name'             => $data['name'],
				'bundles'          => $data['bundle_count'],
				'revenue'          => $data['revenue'] ?? 0,
				'purchased_groups' => $data['purchased_groups'] ?? 0,
			);
		}

		return array(
			'number'           => $data['number'] ?? 0,
			'student_id'       => intval( $data['student_id'] ) ?? 0,
			'courses'          => empty( $data['mix_ids'] ) ? 0 : count( explode( ',', $data['mix_ids'] ) ),
			'total_orders'     => $data['total_orders'],
			'name'             => $data['name'],
			'bundles'          => $data['bundles'],
			'revenue'          => $data['revenue'] / 10,
			'purchased_groups' => $this->isEnterpriceGroups( explode( ',', $data['mix_ids'] ), explode( ',', $data['order_ids'] ), ),
		);
	}

	public function isEnterpriceGroups( $post_ids, $order_ids ) {
		$groups_count = 0;

		foreach ( $order_ids as $order_id ) {
			if ( 'stm-orders' !== get_post_type( $order_id ) ) {
				continue;
			}

			$cart_items = get_post_meta( $order_id, 'items', true );

			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $cart_item ) {
					if ( ! empty( $cart_item['enterprise'] ) ) {
						$groups_count++;
					}
				}
			}
		}

		return $groups_count;
	}
}
