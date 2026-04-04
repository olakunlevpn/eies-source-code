<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class PaymentHistory extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'data'            => array(
				'type'        => 'array',
				'description' => 'Payment history for the subscription',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'             => array(
							'type'        => 'integer',
							'description' => 'Payment ID (order item id or derived number for trial)',
							'example'     => 12345,
						),
						'total'          => array(
							'type'        => 'string',
							'description' => 'Formatted payment amount',
							'example'     => '$49.99',
						),
						'subtotal'       => array(
							'type'        => 'string',
							'description' => 'Formatted subtotal amount',
							'example'     => '$45.00',
						),
						'taxes'          => array(
							'type'        => 'string',
							'description' => 'Formatted taxes amount',
							'example'     => '$4.99',
						),
						'payment_method' => array(
							'type'        => 'string',
							'description' => 'Payment method (lowercase), e.g. "stripe", "paypal", "free trial"',
							'example'     => 'stripe',
						),
						'date'           => array(
							'type'        => 'string',
							'description' => 'Payment date in Y-m-d H:i:s format',
							'example'     => '2025-01-15 10:30:00',
						),
						'status'         => array(
							'type'        => 'string',
							'description' => 'Payment status (lowercase)',
							'enum'        => array( 'paid', 'trial', 'pending', 'failed', 'refunded' ),
							'example'     => 'paid',
						),
					),
					'required'   => array( 'id', 'total', 'subtotal', 'taxes', 'payment_method', 'date', 'status' ),
				),
			),
			'recordsTotal'    => array(
				'type'        => 'integer',
				'description' => 'Total number of payment records',
				'example'     => 15,
			),
			'recordsFiltered' => array(
				'type'        => 'integer',
				'description' => 'Number of filtered payment records (same as recordsTotal)',
				'example'     => 15,
			),
			'start'           => array(
				'type'        => 'integer',
				'description' => 'Starting index for pagination',
				'example'     => 0,
			),
			'length'          => array(
				'type'        => 'integer',
				'description' => 'Number of records per page',
				'example'     => 10,
			),
		);
	}

	public function get_summary(): string {
		return 'Get subscription payment history';
	}

	public function get_description(): string {
		return 'Retrieve payment history for a specific subscription with pagination support. Supports start, length, and sort parameters.';
	}

	public function get_parameters(): array {
		return array(
			array(
				'name'        => 'start',
				'in'          => 'query',
				'description' => 'Starting index for pagination',
				'required'    => false,
				'schema'      => array(
					'type'    => 'integer',
					'default' => 0,
				),
			),
			array(
				'name'        => 'length',
				'in'          => 'query',
				'description' => 'Number of records per page',
				'required'    => false,
				'schema'      => array(
					'type'    => 'integer',
					'default' => 10,
				),
			),
			array(
				'name'        => 'sort',
				'in'          => 'query',
				'description' => 'Sort field and direction in format "field:direction" (e.g., "date:desc", "total:asc")',
				'required'    => false,
				'schema'      => array(
					'type'    => 'string',
					'default' => 'date:desc',
				),
			),
		);
	}
}
