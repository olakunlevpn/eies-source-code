<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics;

use MasterStudy\Lms\Pro\RestApi\Services\CheckoutService;
use MasterStudy\Lms\Pro\RestApi\Providers\MasterstudyProvider;
use MasterStudy\Lms\Pro\RestApi\Providers\WoocommerceProvider;
use MasterStudy\Lms\Pro\RestApi\Traits\AnalyticsValidator;
use STM_LMS_Cart;

class Controller {
	use AnalyticsValidator;

	/**
	 * Returns active Checkout provider.
	 */
	public function get_checkout_provider(): CheckoutService {
		$checkout = STM_LMS_Cart::woocommerce_checkout_enabled()
			? new WoocommerceProvider()
			: new MasterstudyProvider();

		return new CheckoutService( $checkout );
	}
}
