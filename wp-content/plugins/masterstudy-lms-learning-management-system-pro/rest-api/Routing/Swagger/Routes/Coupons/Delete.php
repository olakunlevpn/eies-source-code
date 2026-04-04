<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

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
		return 'Delete a coupon';
	}

	public function get_description(): string {
		return 'Delete a coupon by ID. Returns a simple status response.';
	}
}
