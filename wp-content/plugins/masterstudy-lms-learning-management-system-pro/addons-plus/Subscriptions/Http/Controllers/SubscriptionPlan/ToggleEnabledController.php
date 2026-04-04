<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Validation\Validator;

class ToggleEnabledController {
	public function __invoke( int $plan_id, \WP_REST_Request $request ) {
		$subscription_plan_repository = new SubscriptionPlanRepository();
		if ( ! $subscription_plan_repository->find( $plan_id ) ) {
			return WpResponseFactory::not_found();
		}

		$validator = new Validator(
			$request->get_params(),
			array(
				'is_enabled' => 'required|boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		try {
			$subscription_plan_repository->toggle_enabled( $plan_id, $validated_data['is_enabled'] );
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}

		return new \WP_REST_Response(
			array(
				'status'     => 'ok',
				'plan_id'    => $plan_id,
				'is_enabled' => $validated_data['is_enabled'],
			)
		);
	}
}
