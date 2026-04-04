<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class ListRoute extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'              => array(
				'type'        => 'integer',
				'description' => 'Page number for pagination',
				'required'    => false,
				'default'     => 1,
			),
			'per_page'          => array(
				'type'        => 'integer',
				'description' => 'Number of items per page',
				'required'    => false,
				'default'     => 20,
			),
			'sort'              => array(
				'type'        => 'string',
				'description' => 'Sort order (asc/desc)',
				'enum'        => array( 'asc', 'desc' ),
				'required'    => false,
			),
			'date_from'         => array(
				'type'        => 'string',
				'format'      => 'date',
				'description' => 'Filter subscriptions from this date (YYYY-MM-DD)',
				'required'    => false,
			),
			'date_to'           => array(
				'type'        => 'string',
				'format'      => 'date',
				'description' => 'Filter subscriptions to this date (YYYY-MM-DD)',
				'required'    => false,
			),
			'status'            => array(
				'type'        => 'string',
				'description' => 'Filter by subscription status',
				'enum'        => array( 'active', 'cancelled', 'expired', 'trial', 'pending' ),
				'required'    => false,
			),
			'subscription_type' => array(
				'type'        => 'string',
				'description' => 'Filter by subscription type',
				'enum'        => array( 'course', 'bundle', 'category' ),
				'required'    => false,
			),
			'search'            => array(
				'type'        => 'string',
				'description' => 'Search in plan names and user names',
				'required'    => false,
			),
			'plan_type'         => array(
				'type'        => 'string',
				'description' => 'Filter by plan type',
				'enum'        => array( 'course', 'bundle', 'category' ),
				'required'    => false,
			),
		);
	}

	public function response(): array {
		return array(
			'subscriptions' => array(
				'type'        => 'array',
				'description' => 'List of subscriptions',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'        => array(
							'type'        => 'string',
							'description' => 'Subscription ID',
						),
						'plan'      => array(
							'type'        => 'string',
							'description' => 'Subscription plan name',
						),
						'amount'    => array(
							'type'        => 'number',
							'format'      => 'float',
							'description' => 'Subscription amount',
						),
						'interval'  => array(
							'type'        => 'string',
							'description' => 'Billing interval',
							'enum'        => array( 'hour', 'day', 'week', 'month', 'year' ),
						),
						'type'      => array(
							'type'        => 'string',
							'description' => 'Subscription type',
							'enum'        => array( 'course', 'bundle', 'category' ),
						),
						'user'      => array(
							'type'        => 'string',
							'description' => 'User display name',
						),
						'date'      => array(
							'type'        => 'integer',
							'description' => 'Subscription start date (Unix timestamp)',
						),
						'autoRenew' => array(
							'type'        => 'integer',
							'description' => 'Next payment date (Unix timestamp)',
						),
						'status'    => array(
							'type'        => 'string',
							'description' => 'Subscription status',
							'enum'        => array( 'active', 'cancelled', 'expired', 'trial', 'pending' ),
						),
					),
				),
			),
			'total'         => array(
				'type'        => 'integer',
				'description' => 'Total number of subscriptions',
			),
		);
	}

	public function get_summary(): string {
		return 'List subscriptions';
	}

	public function get_description(): string {
		return 'Get a paginated list of subscriptions with optional filtering and search capabilities';
	}
}
