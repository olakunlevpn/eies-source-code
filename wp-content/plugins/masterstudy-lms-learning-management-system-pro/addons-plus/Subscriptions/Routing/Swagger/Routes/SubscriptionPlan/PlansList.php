<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class PlansList extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'     => array(
				'type'        => 'integer',
				'description' => 'Page number',
				'required'    => true,
			),
			'per_page' => array(
				'type'        => 'integer',
				'description' => 'Items per page',
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'subscription_plans' => array(
				'type'        => 'array',
				'description' => 'Subscription plans',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'                 => array(
							'type'        => 'integer',
							'description' => 'Subscription plan ID',
						),
						'name'               => array(
							'type'        => 'string',
							'description' => 'Subscription plan name',
						),
						'type'               => array(
							'type'        => 'string',
							'description' => 'Subscription plan type',
						),
						'price'              => array(
							'type'        => 'number',
							'description' => 'Subscription plan price',
						),
						'sale_price'         => array(
							'type'        => 'number',
							'description' => 'Subscription plan sale price',
						),
						'recurring_value'    => array(
							'type'        => 'integer',
							'description' => 'Subscription plan recurring value',
						),
						'recurring_interval' => array(
							'type'        => 'string',
							'description' => 'Subscription plan recurring interval',
						),
						'trial_period'       => array(
							'type'        => 'integer',
							'description' => 'Subscription plan trial period',
						),
						'is_featured'        => array(
							'type'        => 'boolean',
							'description' => 'Subscription plan is featured',
						),
						'is_certified'       => array(
							'type'        => 'boolean',
							'description' => 'Subscription plan is certified',
						),
						'is_enabled'         => array(
							'type'        => 'boolean',
							'description' => 'Subscription plan is enabled',
						),
						'plan_order'         => array(
							'type'        => 'integer',
							'description' => 'Subscription plan order',
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'List subscription plans';
	}

	public function get_description(): string {
		return 'List subscription plans';
	}
}
