<?php

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\ReccuringInterval;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Gateways\PayPal;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Gateways\Stripe;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Webhooks\PayPalWebhooks;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Webhooks\StripeWebhooks;

/**
 * Add is_subscription field to cart items
 */
function masterstudy_lms_subscriptions_cart_items_fields( $fields ) {
	$fields[] = 'is_subscription';

	return $fields;
}
add_filter( 'stm_lms_cart_items_fields', 'masterstudy_lms_subscriptions_cart_items_fields' );

/**
 * Add payment gateways
 */
function masterstudy_lms_subscriptions_payment_gateways( $gateways ) {
	$gateways['paypal'] = PayPal::class;
	$gateways['stripe'] = Stripe::class;

	return $gateways;
}
add_filter( 'masterstudy_lms_payment_gateways', 'masterstudy_lms_subscriptions_payment_gateways' );

function masterstudy_lms_subscriptions_add_option( $options ) {
	$options['recurring_intervals'] = array_map( 'strval', ReccuringInterval::cases() );

	return $options;
}

add_filter( 'masterstudy_lms_course_options', 'masterstudy_lms_subscriptions_add_option' );

/**
 * Add payment webhook classes
 */
function masterstudy_lms_subscriptions_payment_webhook_classes( $webhook_classes ) {
	$webhook_classes['paypal'] = PayPalWebhooks::class;
	$webhook_classes['stripe'] = StripeWebhooks::class;

	return $webhook_classes;
}
add_filter( 'masterstudy_lms_payment_webhook_classes', 'masterstudy_lms_subscriptions_payment_webhook_classes' );

function masterstudy_lms_subscriptions_menu_item( $menus ) {
	$current_slug = masterstudy_get_current_account_slug();

	$menus[] = array(
		'order'        => 190,
		'id'           => 'my-subscriptions',
		'slug'         => 'my-subscriptions',
		'lms_template' => 'my-subscriptions',
		'menu_title'   => esc_html__( 'My Subscriptions', 'masterstudy-lms-learning-management-system-pro' ),
		'menu_icon'    => 'stmlms-subs-menu',
		'menu_url'     => ms_plugin_user_account_url( 'my-subscriptions' ),
		'menu_place'   => 'learning',
		'is_active'    => 'my-subscriptions' === $current_slug,
		'section'      => 'account',
	);

	return $menus;
}
add_filter( 'stm_lms_menu_items', 'masterstudy_lms_subscriptions_menu_item' );
