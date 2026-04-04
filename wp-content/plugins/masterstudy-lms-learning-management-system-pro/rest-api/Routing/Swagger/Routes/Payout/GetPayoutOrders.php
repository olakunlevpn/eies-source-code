<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Payout;

use MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Fields\PayoutOrder;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetPayoutOrders extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'start'     => array(
				'type'        => 'integer',
				'description' => 'start from. Default is 10.',
			),
			'length'    => array(
				'type'        => 'integer',
				'description' => 'length per page. Default is 1.',
			),
			'date_from' => array(
				'type'        => 'string',
				'description' => 'Filter date from.',
			),
			'date_to'   => array(
				'type'        => 'string',
				'description' => 'Filter date to.',
			),
			'status'    => array(
				'type'        => 'string',
				'description' => 'Filter by status.',
				'enum'        => array(
					'completed',
					'pending',
					'canceled',
				),
			),
			'order'     => array(
				'type'        => 'array',
				'description' => 'Order by and order dir.',
			),
			'course_id' => array(
				'type'        => 'string',
				'description' => 'Filter by course_id',
			),
		);
	}

	public function response(): array {
		return array(
			'data'            => PayoutOrder::as_array(),
			'recordsTotal'    => array(
				'type'        => 'integer',
				'description' => 'Total number of orders.',
			),
			'recordsFiltered' => array(
				'type'        => 'integer',
				'description' => 'Total number of pages.',
			),
			'formatted_price' => array(
				'type'        => 'string',
				'description' => 'Total price formatted.',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Payout orders';
	}

	public function get_description(): string {
		return 'Returns a list of payout orders based on the provided parameters.';
	}
}
