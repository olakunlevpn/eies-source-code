<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;

class DeleteController {
	public function __invoke( int $coupon_id ) {
		$coupon_repository = new CouponRepository();

		if ( ! $coupon_repository->get( $coupon_id ) ) {
			return WpResponseFactory::not_found();
		}

		$gate = apply_filters(
			'masterstudy_lms_before_delete_coupon',
			true,
			$coupon_id,
		);

		if ( true !== $gate ) {
			if ( is_wp_error( $gate ) ) {
				return WpResponseFactory::error(
					esc_html( $gate->get_error_message() )
				);
			}
			return WpResponseFactory::ok_with_data( $gate );
		}

		try {
			$coupon_repository->delete( $coupon_id );
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}

		return WpResponseFactory::ok();
	}
}
