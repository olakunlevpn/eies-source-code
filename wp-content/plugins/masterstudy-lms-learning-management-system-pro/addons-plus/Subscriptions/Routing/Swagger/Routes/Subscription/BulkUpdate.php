<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class BulkUpdate extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'action'      => array(
				'type'        => 'string',
				'description' => 'Bulk action to perform on subscriptions',
				'enum'        => array( 'delete' ),
				'required'    => true,
			),
			'memberships' => array(
				'type'        => 'array',
				'description' => 'Array of subscription memberships to perform the action on',
				'required'    => true,
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id' => array(
							'type'        => 'integer',
							'description' => 'Subscription ID',
							'required'    => true,
						),
					),
					'required'   => array( 'id' ),
				),
			),
		);
	}

	public function response(): array {
		return array(
			'success' => array(
				'type'        => 'boolean',
				'description' => 'Whether the bulk action was successful',
			),
			'message' => array(
				'type'        => 'string',
				'description' => 'Success message',
			),
		);
	}

	public function get_summary(): string {
		return 'Bulk update subscriptions';
	}

	public function get_description(): string {
		return 'Perform bulk operations on multiple subscriptions. Currently supports deleting multiple subscriptions at once.';
	}
}
