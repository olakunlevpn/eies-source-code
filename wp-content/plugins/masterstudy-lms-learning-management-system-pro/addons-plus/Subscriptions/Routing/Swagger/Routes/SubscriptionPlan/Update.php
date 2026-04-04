<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Fields\SubscriptionPlanItem;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Update extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
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
				'enum'        => array( 'day', 'week', 'month', 'year' ),
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
			),
			'sale_price'         => array(
				'type'        => 'number',
				'description' => 'Subscription sale price',
				'required'    => false,
			),
			'sale_price_from'    => array(
				'type'        => 'string',
				'description' => 'Subscription sale price from',
				'required'    => false,
			),
			'sale_price_to'      => array(
				'type'        => 'string',
				'description' => 'Subscription sale price to',
				'required'    => false,
			),
			'plan_features'      => array(
				'type'        => 'array',
				'description' => 'Subscription plan features',
				'required'    => false,
			),
			'enrollment_fee'     => array(
				'type'        => 'number',
				'description' => 'Subscription enrollment fee',
				'required'    => false,
			),
			'trial_period'       => array(
				'type'        => 'integer',
				'description' => 'Subscription trial period',
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

	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Update subscription plan';
	}

	public function get_description(): string {
		return 'Update subscription plan';
	}
}
