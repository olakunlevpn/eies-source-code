<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\SubscriptionPlan;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Delete extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Delete a subscription plan';
	}

	public function get_description(): string {
		return 'Delete a subscription plan. Returns an error if the plan is currently being used by active students.';
	}

	public function get_responses(): array {
		return array(
			200 => array(
				'description' => 'Plan deleted successfully',
				'content'     => array(
					'application/json' => array(
						'schema' => array(
							'type'       => 'object',
							'properties' => array(
								'status' => array(
									'type'    => 'string',
									'example' => 'ok',
								),
							),
						),
					),
				),
			),
			409 => array(
				'description' => 'Plan cannot be deleted because it is in use',
				'content'     => array(
					'application/json' => array(
						'schema' => array(
							'type'       => 'object',
							'properties' => array(
								'error'   => array(
									'type'    => 'boolean',
									'example' => true,
								),
								'message' => array(
									'type'    => 'string',
									'example' => 'Cannot delete this subscription plan. It is currently being used by 5 active student(s). Please cancel or wait for all subscriptions to expire before deleting plan #123.',
								),
								'code'    => array(
									'type'    => 'string',
									'example' => 'plan_in_use',
								),
								'data'    => array(
									'type'       => 'object',
									'properties' => array(
										'plan_id'       => array(
											'type'    => 'integer',
											'example' => 123,
										),
										'active_count'  => array(
											'type'    => 'integer',
											'example' => 5,
										),
										'is_being_used' => array(
											'type'    => 'boolean',
											'example' => true,
										),
									),
								),
							),
						),
					),
				),
			),
			404 => array(
				'description' => 'Plan not found',
			),
		);
	}
}
