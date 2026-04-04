<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

class CouponSchema {
	public static function coupon_properties(): array {
		return array(
			'id'                  => array(
				'type'        => 'integer',
				'description' => 'Coupon ID',
			),
			'title'               => array(
				'type'        => 'string',
				'description' => 'Coupon title',
			),
			'coupon_status'       => array(
				'type'        => 'string',
				'description' => 'Coupon status (including derived statuses like "expired")',
				'enum'        => array( 'active', 'inactive', 'trash', 'expired' ),
			),
			'code'                => array(
				'type'        => 'string',
				'description' => 'Coupon code',
			),
			'discount_type'       => array(
				'type'        => 'string',
				'description' => 'Type of discount',
				'enum'        => array( 'percent', 'amount' ),
			),
			'discount'            => array(
				'type'        => 'number',
				'description' => 'Discount value',
			),
			'discount_formatted'  => array(
				'type'        => 'string',
				'description' => 'Formatted discount (e.g. "15%" or "$10")',
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
			),
			'usage_limit'         => array(
				'type'        => 'integer',
				'description' => 'Global usage limit (0 or null means unlimited)',
				'nullable'    => true,
			),
			'used_count'          => array(
				'type'        => 'integer',
				'description' => 'How many times the coupon has been used',
			),
			'usage_remaining'     => array(
				'type'        => 'integer',
				'description' => 'Remaining uses (0 if limit is reached, null if unlimited)',
				'nullable'    => true,
			),
			'user_usage_limit'    => array(
				'type'        => 'integer',
				'description' => 'Per-user usage limit (null means unlimited)',
				'nullable'    => true,
			),
			'min_purchase_amount' => array(
				'type'        => 'number',
				'description' => 'Minimum order total required to apply this coupon',
				'nullable'    => true,
			),
			'min_course_quantity' => array(
				'type'        => 'integer',
				'description' => 'Minimum number of eligible items in cart',
				'nullable'    => true,
			),
			'start_date'          => array(
				'type'        => 'integer',
				'description' => 'Start date in milliseconds since Unix epoch (midnight in site timezone)',
				'nullable'    => true,
			),
			'end_date'            => array(
				'type'        => 'integer',
				'description' => 'End date in milliseconds since Unix epoch (midnight in site timezone)',
				'nullable'    => true,
			),
			'start_time'          => array(
				'type'        => 'string',
				'description' => 'Start time in HH:MM format',
				'nullable'    => true,
			),
			'end_time'            => array(
				'type'        => 'string',
				'description' => 'End time in HH:MM format',
				'nullable'    => true,
			),
			'start_at'            => array(
				'type'        => 'string',
				'description' => 'Full start datetime in site format',
				'nullable'    => true,
			),
			'end_at'              => array(
				'type'        => 'string',
				'description' => 'Full end datetime in site format',
				'nullable'    => true,
			),
			'items'               => array(
				'type'        => 'array',
				'description' => 'Raw IDs associated with the coupon (courses, bundles, categories, membership plans)',
				'items'       => array(
					'type' => 'integer',
				),
				'nullable'    => true,
			),
			'detail_items'        => array(
				'type'        => 'array',
				'description' => 'Resolved item details for the current product_type',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'object_id'   => array(
							'type'        => 'integer',
							'description' => 'Object ID (course, bundle, category, membership)',
						),
						'object_type' => array(
							'type'        => 'string',
							'description' => 'Object type',
							'enum'        => array( 'course', 'bundle', 'category', 'membership' ),
						),
						'object'      => array(
							'type'       => 'object',
							'properties' => array(
								'name'       => array(
									'type'        => 'string',
									'description' => 'Object name/title',
								),
								'price'      => array(
									'type'        => 'string',
									'description' => 'Formatted price',
									'nullable'    => true,
								),
								'sale_price' => array(
									'type'        => 'string',
									'description' => 'Formatted sale price (for courses)',
									'nullable'    => true,
								),
								'image_url'  => array(
									'type'        => 'string',
									'description' => 'Image URL, if available',
									'nullable'    => true,
								),
							),
						),
					),
				),
			),
			'is_active'           => array(
				'type'        => 'boolean',
				'description' => 'Whether the coupon is currently active (status + dates)',
			),
			'is_expired'          => array(
				'type'        => 'boolean',
				'description' => 'Whether the coupon is considered expired based on end date',
			),
		);
	}
}
