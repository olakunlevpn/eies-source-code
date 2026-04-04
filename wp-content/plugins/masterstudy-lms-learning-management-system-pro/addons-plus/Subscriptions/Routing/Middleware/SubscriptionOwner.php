<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Middleware;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Pro\RestApi\Context\StudentContext;
use MasterStudy\Lms\Routing\MiddlewareInterface;

/**
 * Checks if user can access to the post
 */
final class SubscriptionOwner implements MiddlewareInterface {
	public function process( $request, callable $next ) {
		$subscription_id = $this->get_subscription_id( $request );
		$current_user_id = get_current_user_id();

		// Set current student ID in the context, as all my-subscription routes are protected by this middleware
		StudentContext::get_instance()->set_student_id( $current_user_id );

		if ( current_user_can( 'administrator' ) || null === $subscription_id ) {
			return $next( $request );
		}

		$subscription = ( new SubscriptionRepository() )->get( $subscription_id );

		if ( empty( $subscription ) || $current_user_id !== (int) $subscription['user_id'] ) {
			return WpResponseFactory::forbidden();
		}

		return $next( $request );
	}

	/**
	 * @param \WP_REST_Request $request
	 */
	private function get_subscription_id( $request ): ?int {
		$url_params = $request->get_url_params();

		return isset( $url_params['subscription_id'] ) ? (int) $url_params['subscription_id'] : null;
	}
}
