<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Reorder extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'plans' => array(
				'type'        => 'array',
				'description' => 'Subscription plans',
				'required'    => true,
				'items'       => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'id'         => array(
								'type'        => 'integer',
								'description' => 'Subscription plan ID',
							),
							'plan_order' => array(
								'type'        => 'integer',
								'description' => 'Subscription plan order',
							),
						),
					),
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
		return 'Reorder subscription plans';
	}

	public function get_description(): string {
		return 'Reorder subscription plans';
	}
}
