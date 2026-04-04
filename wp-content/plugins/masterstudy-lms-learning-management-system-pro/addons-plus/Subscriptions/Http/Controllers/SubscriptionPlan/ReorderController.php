<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Validation\Validator;

class ReorderController {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'plans' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();
		$reordered      = ( new SubscriptionPlanRepository() )->reorder( $validated_data['plans'] );

		if ( ! $reordered ) {
			return WpResponseFactory::error(
				esc_html__( 'Failed to reorder subscription plans', 'masterstudy-lms-learning-management-system-pro' )
			);
		}

		return WpResponseFactory::ok();
	}
}
