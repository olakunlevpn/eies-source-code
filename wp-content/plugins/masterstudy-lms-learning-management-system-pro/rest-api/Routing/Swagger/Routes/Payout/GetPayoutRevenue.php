<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Payout;

use MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Fields\PayoutOrder;
use MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Fields\PayoutRevenue;
use MasterStudy\Lms\Routing\Swagger\Fields\Post;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetPayoutRevenue extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'date_from' => array(
				'type'        => 'string',
				'description' => 'Filter date from.',
			),
			'date_to'   => array(
				'type'        => 'string',
				'description' => 'Filter date to.',
			),
			'course_id' => array(
				'type'        => 'string',
				'description' => 'Filter by course_id',
			),
		);
	}

	public function response(): array {
		return PayoutRevenue::as_response();
	}

	public function get_summary(): string {
		return 'Get Payout revenue';
	}

	public function get_description(): string {
		return 'Returns an object of payout revenue statistics based on the provided parameters.';
	}
}
