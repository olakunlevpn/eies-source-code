<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers\SubscriptionPlansSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Validation\Validator;

class ListController {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page'      => 'nullable|integer',
				'per_page'  => 'nullable|integer',
				'sort'      => 'nullable|string',
				'course_id' => 'nullable|integer',
				'search'    => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();
		if ( ! empty( $validated_data['course_id'] ) ) {
			$subscription_plans = ( new SubscriptionPlanRepository() )->get_course_plans(
				$validated_data['course_id'],
				$validated_data['page'] ?? 1,
				$validated_data['per_page'] ?? 10
			);
		} else {
			$subscription_plans = ( new SubscriptionPlanRepository() )->list(
				$validated_data['page'] ?? 1,
				$validated_data['per_page'] ?? 10,
				$validated_data['sort'] ?? '',
				$validated_data['search'] ?? ''
			);
		}

		return new \WP_REST_Response(
			array(
				'subscription_plans' => ( new SubscriptionPlansSerializer() )->collectionToArray( $subscription_plans['plans'] ),
				'total'              => $subscription_plans['total'],
			)
		);
	}
}
