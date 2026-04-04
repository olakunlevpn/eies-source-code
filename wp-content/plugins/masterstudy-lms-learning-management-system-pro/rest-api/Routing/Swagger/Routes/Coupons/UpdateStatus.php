<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateStatus extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'coupon_status' => array(
				'type'        => 'string',
				'description' => 'Coupon status',
				'enum'        => array( 'active', 'inactive', 'trash' ),
				'required'    => true,
			),
		);
	}

	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Update coupon status';
	}

	public function get_description(): string {
		return 'Update coupon status';
	}
}
