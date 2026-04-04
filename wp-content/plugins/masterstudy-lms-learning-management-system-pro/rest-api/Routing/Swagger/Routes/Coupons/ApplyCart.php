<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\CouponSchema;

class ApplyCart extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'code' => array(
				'type'        => 'string',
				'description' => 'Coupon code to apply to the current cart',
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'status'           => array(
				'type'        => 'string',
				'description' => 'Result status of applying the coupon',
				'enum'        => array( 'success', 'error' ),
			),
			'message'          => array(
				'type'        => 'string',
				'description' => 'Human-readable message describing the result',
			),
			'coupon'           => array(
				'type'        => 'object',
				'description' => 'Coupon data after applying',
				'properties'  => CouponSchema::coupon_properties(),
			),
			'applied_item_ids' => array(
				'type'        => 'array',
				'description' => 'IDs of items/bundles the coupon was applied to',
				'items'       => array(
					'type' => 'integer',
				),
			),
			'applied_items'    => array(
				'type'        => 'array',
				'description' => 'Detailed list of cart line items affected by this coupon',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'              => array(
							'type'        => 'integer',
							'description' => 'Primary object ID (course, bundle, membership)',
						),
						'item_id'         => array(
							'type'        => 'integer',
							'description' => 'Course or plan ID',
						),
						'bundle_id'       => array(
							'type'        => 'integer',
							'description' => 'Bundle ID if applicable, otherwise 0',
						),
						'is_subscription' => array(
							'type'        => 'boolean',
							'description' => 'Whether the item is a subscription/membership',
						),
						'price'           => array(
							'type'        => 'number',
							'description' => 'Unit price of the item',
						),
						'quantity'        => array(
							'type'        => 'integer',
							'description' => 'Quantity of the item in cart',
						),
						'line_subtotal'   => array(
							'type'        => 'number',
							'description' => 'Subtotal for this line (price * quantity) before discounts',
						),
					),
				),
			),
			'applied_subtotal' => array(
				'type'        => 'number',
				'description' => 'Subtotal of all eligible items before coupon discount',
			),
		);
	}

	public function get_summary(): string {
		return 'Apply coupon to cart';
	}

	public function get_description(): string {
		return 'Validate and apply a coupon code to the current user cart. Returns coupon details and the list of items it affects.';
	}
}
