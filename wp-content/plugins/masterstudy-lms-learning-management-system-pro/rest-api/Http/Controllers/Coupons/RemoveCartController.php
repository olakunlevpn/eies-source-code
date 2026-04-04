<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class RemoveCartController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( ( new CouponRepository() )->remove_cart() );
	}
}
