<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class ToggleEnabled extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'is_enabled' => array(
				'type'        => 'boolean',
				'description' => 'Subscription plan enabled status',
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'status'     => array(
				'type'        => 'string',
				'description' => 'ok',
			),
			'plan_id'    => array(
				'type'        => 'integer',
				'description' => 'Subscription plan ID',
			),
			'is_enabled' => array(
				'type'        => 'boolean',
				'description' => 'Subscription plan enabled status',
			),
		);
	}

	public function get_summary(): string {
		return 'Toggle a subscription plan enabled status';
	}

	public function get_description(): string {
		return 'Toggle a subscription plan enabled status';
	}
}
