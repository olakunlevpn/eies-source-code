<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers\SubscriptionPlanSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

class GetController {
	public function __invoke( int $plan_id ) {
		$subscription_plan_repository = new SubscriptionPlanRepository();
		if ( ! $subscription_plan_repository->find( $plan_id ) ) {
			return WpResponseFactory::not_found();
		}

		$plan = $subscription_plan_repository->get( $plan_id );

		return new \WP_REST_Response(
			array(
				'plan' => ( new SubscriptionPlanSerializer() )->toArray( $plan ),
			)
		);
	}
}
