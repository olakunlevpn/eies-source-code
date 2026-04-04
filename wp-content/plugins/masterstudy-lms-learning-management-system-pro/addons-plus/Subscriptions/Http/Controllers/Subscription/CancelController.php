<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Ecommerce\Ecommerce;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

class CancelController {
	public function __invoke( int $subscription_id ) {
		$repository   = new SubscriptionRepository();
		$subscription = $repository->get( $subscription_id );

		if ( ! $subscription || ! $subscription['meta']['gateway'] ) {
			return WpResponseFactory::not_found();
		}

		$payment_gateway_class = Ecommerce::get_payment_gateway_class( $subscription['meta']['gateway'] );
		if ( ! $payment_gateway_class ) {
			return WpResponseFactory::error( 'Invalid payment method' );
		}

		$gate = apply_filters(
			'masterstudy_lms_before_cancel_subscription',
			true,
			$subscription_id,
			$subscription,
		);

		if ( true !== $gate ) {
			if ( is_wp_error( $gate ) ) {
				return WpResponseFactory::error(
					esc_html( $gate->get_error_message() )
				);
			}
			return WpResponseFactory::ok_with_data( $gate );
		}

		// Cancel the subscription immediately (user-initiated cancellation)
		( new $payment_gateway_class() )->cancel_subscription( $subscription_id, true );

		return WpResponseFactory::ok();
	}
}
