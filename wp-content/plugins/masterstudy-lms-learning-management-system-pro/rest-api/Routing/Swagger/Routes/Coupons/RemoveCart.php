<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class RemoveCart extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		// Endpoint does not expect any body parameters.
		return array();
	}

	public function response(): array {
		return array(
			'status'  => array(
				'type'        => 'string',
				'description' => 'Result status of removing coupon from cart',
				'example'     => 'success',
			),
			'message' => array(
				'type'        => 'string',
				'description' => 'Human-readable message describing the result',
				'example'     => 'Coupon removed',
			),
		);
	}

	public function get_summary(): string {
		return 'Remove coupon from cart';
	}

	public function get_description(): string {
		return 'Remove the currently applied coupon from the user cart and clear the related cookie.';
	}
}
