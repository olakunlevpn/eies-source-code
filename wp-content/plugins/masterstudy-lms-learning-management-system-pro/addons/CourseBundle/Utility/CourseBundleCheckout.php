<?php

namespace MasterStudy\Lms\Pro\addons\CourseBundle\Utility;

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use STM_LMS_Woocommerce;
use STM_LMS_Cart;
use STM_LMS_Options;

class CourseBundleCheckout {
	public static function add_to_cart( $item_id, $user_id ): array {
		// Convert item_id to an integer and validate input
		$bundle = intval( $item_id );
		if ( empty( $user_id ) || empty( $bundle ) ) {
			return array( 'error' => 'Invalid user or bundle ID' );
		}

		// Retrieve bundle price and check if WooCommerce is enabled
		$quantity = 1;
		$price    = CourseBundleRepository::get_bundle_price( $item_id );

		// Add the item to the cart if not already added
		if ( ! count( stm_lms_get_item_in_cart( $user_id, $item_id, array( 'user_cart_id' ) ) ) > 0 ) {
			do_action( 'masterstudy_lms_before_add_to_cart', $item_id, $user_id );

			stm_lms_add_user_cart( compact( 'user_id', 'item_id', 'quantity', 'price', 'bundle' ) );

			do_action( 'masterstudy_lms_after_add_to_cart', $item_id, $user_id );
		}

		// Generate and return the response
		$response = array(
			'text'     => esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' ),
			'redirect' => STM_LMS_Options::get_option( 'redirect_after_purchase', false ),
		);

		if ( ! STM_LMS_Cart::woocommerce_checkout_enabled() ) {
			$response['cart_url'] = esc_url( STM_LMS_Cart::checkout_url() );
		} else {
			$response['added']    = STM_LMS_Woocommerce::add_to_cart( $item_id );
			$response['cart_url'] = esc_url( wc_get_cart_url() );
		}

		return apply_filters( 'masterstudy_lms_add_to_cart_response', $response, $item_id );
	}
}
