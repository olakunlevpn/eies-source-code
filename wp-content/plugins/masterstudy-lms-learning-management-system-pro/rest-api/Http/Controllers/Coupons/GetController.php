<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Coupons\CouponSerializer;

class GetController {
	public function __invoke( int $coupon_id ) {
		$coupon_repository = new CouponRepository();
		if ( ! $coupon_repository->get( $coupon_id ) ) {
			return WpResponseFactory::not_found();
		}

		$coupon = $coupon_repository->get( $coupon_id );

		return new \WP_REST_Response( ( new CouponSerializer() )->toArray( $coupon ) );
	}
}
