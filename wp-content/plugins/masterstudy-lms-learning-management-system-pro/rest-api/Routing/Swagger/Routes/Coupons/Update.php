<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Update extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'title'               => array(
				'type'        => 'string',
				'description' => 'Coupon title',
				'required'    => true,
			),
			'coupon_status'       => array(
				'type'        => 'string',
				'description' => 'Coupon status',
				'enum'        => array( 'active', 'inactive', 'trash' ),
				'required'    => true,
			),
			'code'                => array(
				'type'        => 'string',
				'description' => 'Unique coupon code',
				'required'    => true,
			),
			'discount_type'       => array(
				'type'        => 'string',
				'description' => 'Type of discount',
				'enum'        => array( 'percent', 'amount' ),
				'required'    => true,
			),
			'discount'            => array(
				'type'        => 'number',
				'description' => 'Discount value (percentage or fixed amount)',
				'required'    => true,
			),
			'product_type'        => array(
				'type'        => 'string',
				'description' => 'What this coupon applies to',
				'enum'        => array(
					'all',
					'all-courses',
					'all-bundles',
					'all-membership-plans',
					'specific-membership-plans',
					'all-courses-and-bundles',
					'specific-courses',
					'specific-categories',
					'specific-bundles',
				),
				'required'    => true,
			),
			'usage_limit'         => array(
				'type'        => 'integer',
				'description' => 'Global usage limit for this coupon (NULL = unlimited)',
				'required'    => false,
			),
			'user_usage_limit'    => array(
				'type'        => 'integer',
				'description' => 'Usage limit per user (NULL = unlimited)',
				'required'    => false,
			),
			'min_purchase_amount' => array(
				'type'        => 'number',
				'description' => 'Minimum order total required to apply this coupon',
				'required'    => false,
			),
			'min_course_quantity' => array(
				'type'        => 'integer',
				'description' => 'Minimum number of eligible items required in the cart',
				'required'    => false,
			),
			'start_date'          => array(
				'type'        => 'integer',
				'description' => 'Start date in milliseconds since Unix epoch (used with start_time)',
				'required'    => false,
			),
			'start_time'          => array(
				'type'        => 'string',
				'description' => 'Start time in HH:MM format (local WordPress timezone)',
				'required'    => false,
			),
			'end_date'            => array(
				'type'        => 'integer',
				'description' => 'End date in milliseconds since Unix epoch (used with end_time)',
				'required'    => false,
			),
			'end_time'            => array(
				'type'        => 'string',
				'description' => 'End time in HH:MM format (local WordPress timezone)',
				'required'    => false,
			),
			'items'               => array(
				'type'        => 'array',
				'description' => 'IDs of courses, bundles, categories or membership plans depending on product_type',
				'required'    => false,
				'items'       => array(
					'type' => 'integer',
				),
			),
		);
	}

	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Update coupon';
	}

	public function get_description(): string {
		return 'Update coupon data (status, discount, product targeting, usage limits, schedule).';
	}
}
