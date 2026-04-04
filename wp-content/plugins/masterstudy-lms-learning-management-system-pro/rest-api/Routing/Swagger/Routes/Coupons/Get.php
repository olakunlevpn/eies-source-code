<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;
use MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\CouponSchema;

class Get extends Route implements ResponseInterface {
	public function response(): array {
		return CouponSchema::coupon_properties();
	}

	public function get_summary(): string {
		return 'Get a coupon';
	}

	public function get_description(): string {
		return 'Retrieve a single coupon by ID.';
	}
}
