<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Analytics\Bundle;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetBundleData extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'revenue' => array(
				'type'        => 'number',
				'format'      => 'float',
				'description' => 'Total revenue',
			),
			'orders'  => array(
				'type'        => 'integer',
				'description' => 'Total Orders',
			),
			'period'  => array(
				'type'        => 'array',
				'description' => 'Periods',
			),
			'values'  => array(
				'type'        => 'array',
				'format'      => 'float',
				'description' => 'Price values for periods',
			),
		);
	}

	/**
	 * Route Summary
	 */
	public function get_summary(): string {
		return 'Get Bundles Data';
	}

	/**
	 * Route Description
	 */
	public function get_description(): string {
		return 'Get Bundles Revenue and Orders and Period Data';
	}
}
