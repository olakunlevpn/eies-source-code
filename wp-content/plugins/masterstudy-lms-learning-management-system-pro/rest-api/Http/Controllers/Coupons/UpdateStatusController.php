<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Coupons\CouponSerializer;
use MasterStudy\Lms\Validation\Validator;

class UpdateStatusController {
	public function __invoke( int $coupon_id, \WP_REST_Request $request ) {
		$coupon_repository = new CouponRepository();

		if ( ! $coupon_repository->get( $coupon_id ) ) {
			return WpResponseFactory::not_found();
		}

		$validator = new Validator(
			$request->get_params(),
			array(
				'coupon_status' => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		$gate = apply_filters(
			'masterstudy_lms_before_update_status_coupon',
			true,
			$coupon_id,
			$validated_data
		);

		if ( true !== $gate ) {
			if ( is_wp_error( $gate ) ) {
				return WpResponseFactory::error(
					esc_html( $gate->get_error_message() )
				);
			}
			return WpResponseFactory::ok_with_data( $gate );
		}

		$coupon_repository->update( $coupon_id, $validated_data );

		return WpResponseFactory::ok();
	}
}
