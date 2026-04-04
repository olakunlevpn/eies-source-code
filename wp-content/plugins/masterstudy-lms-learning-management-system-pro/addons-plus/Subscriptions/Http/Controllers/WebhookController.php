<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers;

use MasterStudy\Lms\Ecommerce\Ecommerce;
use MasterStudy\Lms\Http\WpResponseFactory;

class WebhookController {
	public function __invoke( string $payment_method, \WP_REST_Request $request ) {
		$webhook_class = Ecommerce::get_payment_webhook_class( $payment_method );

		if ( ! $webhook_class ) {
			return WpResponseFactory::error( 'Invalid payment method' );
		}

		// Handle webhook
		( new $webhook_class() )->handle_webhook(
			$request->get_body(),
			$request->get_headers()
		);

		return WpResponseFactory::ok();
	}
}
