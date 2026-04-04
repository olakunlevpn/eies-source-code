<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers\SubscriptionDetailsSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use WP_REST_Response;

class GetController {
	/**
	 * GET /memberships/{subscription_id}
	 *
	 * @param int $subscription_id Subscription ID.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function __invoke( int $subscription_id ) {
		$repository = new SubscriptionRepository();

		if ( ! $repository->find( $subscription_id ) ) {
			return WpResponseFactory::not_found();
		}

		return new WP_REST_Response(
			( new SubscriptionDetailsSerializer() )->toArray( $repository->get_subscription_details( $subscription_id ) )
		);
	}
}
