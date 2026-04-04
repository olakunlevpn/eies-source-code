<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetInstructorSubscriptions extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'recordsTotal'    => array(
				'type'        => 'integer',
				'description' => 'Total number of subscriptions',
			),
			'recordsFiltered' => array(
				'type'        => 'integer',
				'description' => 'Number of filtered subscriptions',
			),
			'data'            => array(
				'type'        => 'array',
				'description' => 'Subscriptions data',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'subscription_id'   => array(
							'type'        => 'integer',
							'description' => 'Subscription ID',
						),
						'user_info'         => array(
							'type'        => 'string',
							'description' => 'User full name',
						),
						'plan_name'         => array(
							'type'        => 'string',
							'description' => 'Plan name',
						),
						'status'            => array(
							'type'        => 'string',
							'description' => 'Subscription status',
						),
						'start_date'        => array(
							'type'        => 'string',
							'format'      => 'date-time',
							'description' => 'Subscription start date',
						),
						'next_payment_date' => array(
							'type'        => 'string',
							'format'      => 'date-time',
							'description' => 'Next payment date',
						),
						'amount'            => array(
							'type'        => 'number',
							'format'      => 'float',
							'description' => 'Subscription amount',
						),
						'user'              => array(
							'type'       => 'object',
							'properties' => array(
								'first_name' => array(
									'type'        => 'string',
									'description' => 'User first name',
								),
								'last_name'  => array(
									'type'        => 'string',
									'description' => 'User last name',
								),
								'email'      => array(
									'type'        => 'string',
									'description' => 'User email',
								),
							),
						),
						'plan'              => array(
							'type'       => 'object',
							'properties' => array(
								'id'   => array(
									'type'        => 'integer',
									'description' => 'Plan ID',
								),
								'name' => array(
									'type'        => 'string',
									'description' => 'Plan name',
								),
							),
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Get Instructor Subscriptions';
	}

	public function get_description(): string {
		return 'Get instructor subscriptions with pagination, search and sorting';
	}
}
