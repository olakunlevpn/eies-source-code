<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Validation\Validator;

class CreateController {

	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'title'               => 'required|string',
				'coupon_status'       => 'required|string',
				'code'                => 'required|string',
				'discount_type'       => 'required|string',
				'discount'            => 'required|numeric|min,1',
				'product_type'        => 'required|string',
				'usage_limit'         => 'nullable|integer|min,1',
				'user_usage_limit'    => 'nullable|integer|min,1',
				'min_purchase_amount' => 'nullable|numeric|min,1',
				'min_course_quantity' => 'nullable|integer|min,1',
				'start_date'          => 'nullable|integer',
				'start_time'          => 'nullable|string',
				'end_date'            => 'nullable|integer',
				'end_time'            => 'nullable|string',
				'items'               => 'nullable|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		$gate = apply_filters(
			'masterstudy_lms_before_create_coupon',
			true,
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

		$coupon_id = ( new CouponRepository() )->create( $validated_data );

		if ( ! $coupon_id ) {
			return WpResponseFactory::error(
				esc_html__( 'Failed to create coupon', 'masterstudy-lms-learning-management-system-pro' )
			);
		}

		return WpResponseFactory::created(
			array(
				'coupon_id' => $coupon_id,
			)
		);
	}
}
