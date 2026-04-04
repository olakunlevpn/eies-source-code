<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Fields\SubscriptionPlanItem;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Get extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'id'                 => array(
				'type'        => 'integer',
				'description' => 'Subscription plan ID',
			),
			'type'               => array(
				'type'        => 'string',
				'description' => 'Subscription type',
				'enum'        => array( 'full_site', 'category', 'course' ),
			),
			'name'               => array(
				'type'        => 'string',
				'description' => 'Subscription name',
				'required'    => true,
			),
			'description'        => array(
				'type'        => 'string',
				'description' => 'Subscription description',
				'required'    => false,
			),
			'recurring_value'    => array(
				'type'        => 'integer',
				'description' => 'Subscription recurring value',
				'required'    => false,
			),
			'recurring_interval' => array(
				'type'        => 'string',
				'description' => 'Subscription recurring interval',
				'enum'        => array( 'hour', 'day', 'week', 'month', 'year' ),
				'required'    => false,
			),
			'billing_cycles'     => array(
				'type'        => 'integer',
				'description' => 'Subscription billing cycles',
				'required'    => false,
			),
			'price'              => array(
				'type'        => 'number',
				'description' => 'Subscription price',
				'required'    => true,
			),
			'sale_price'         => array(
				'type'        => 'number',
				'description' => 'Subscription sale price',
				'required'    => false,
			),
			'sale_price_from'    => array(
				'type'        => 'string',
				'format'      => 'date-time',
				'description' => 'Subscription sale price from',
				'required'    => false,
			),
			'sale_price_to'      => array(
				'type'        => 'string',
				'format'      => 'date-time',
				'description' => 'Subscription sale price to',
				'required'    => false,
			),
			'plan_features'      => array(
				'type'        => 'array',
				'description' => 'Subscription plan features',
				'required'    => false,
				'items'       => array(
					'type' => 'string',
				),
			),
			'enrollment_fee'     => array(
				'type'        => 'number',
				'description' => 'Subscription enrollment fee',
				'required'    => false,
			),
			'trial_period'       => array(
				'type'        => 'integer',
				'description' => 'Subscription trial days',
				'required'    => false,
			),
			'is_featured'        => array(
				'type'        => 'boolean',
				'description' => 'Subscription is featured',
				'required'    => false,
			),
			'featured_text'      => array(
				'type'        => 'string',
				'description' => 'Subscription featured text',
				'required'    => false,
			),
			'is_certified'       => array(
				'type'        => 'boolean',
				'description' => 'Subscription is certified',
				'required'    => false,
			),
			'is_enabled'         => array(
				'type'        => 'boolean',
				'description' => 'Subscription is enabled',
				'required'    => false,
			),
			'items'              => SubscriptionPlanItem::as_array(),
		);
	}

	public function get_summary(): string {
		return 'Get a subscription plan';
	}

	public function get_description(): string {
		return 'Get a subscription plan';
	}
}
