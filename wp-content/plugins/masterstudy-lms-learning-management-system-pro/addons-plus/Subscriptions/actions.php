<?php

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanItemRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

// Add to cart subscription
function masterstudy_lms_add_to_cart_subscription() {
	check_ajax_referer( 'stm_lms_add_to_cart_subscription', 'nonce' );

	if ( ! is_user_logged_in() || empty( $_POST['plan_id'] ) ) {
		wp_send_json_error(
			esc_html__( 'User not logged in or Subscription ID is not set.', 'masterstudy-lms-learning-management-system-pro' ),
			400
		);
		wp_die();
	}

	$item_id = intval( $_POST['plan_id'] );
	$plan    = ( new SubscriptionPlanRepository() )->get( $item_id );

	if ( ! $plan ) {
		wp_send_json_error(
			esc_html__( 'Subscription not found.', 'masterstudy-lms-learning-management-system-pro' ),
			404
		);
		wp_die();
	}

	// Add to cart subscription
	$user_id         = get_current_user_id();
	$price           = SubscriptionPlanRepository::get_actual_price( $plan );
	$redirect        = STM_LMS_Options::get_option( 'redirect_after_purchase', false );
	$quantity        = 1;
	$is_subscription = 1;

	// Empty cart
	stm_lms_get_delete_cart_items( $user_id );

	// Add to cart subscription
	stm_lms_add_user_cart( compact( 'user_id', 'item_id', 'quantity', 'price', 'is_subscription' ) );

	wp_send_json_success(
		array(
			'text'     => esc_html__( 'Go to Cart', 'masterstudy-lms-learning-management-system-pro' ),
			'redirect' => $redirect,
			'cart_url' => esc_url( STM_LMS_Cart::checkout_url() ),
		)
	);
}
add_action( 'wp_ajax_stm_lms_add_to_cart_subscription', 'masterstudy_lms_add_to_cart_subscription' );
add_action( 'wp_ajax_nopriv_stm_lms_add_to_cart_subscription', 'masterstudy_lms_add_to_cart_subscription' );

// Remove subscription cart item if any item is adding to cart
function masterstudy_lms_remove_subscription_cart_item( $item_id, $user_id ) {
	$cart_items = stm_lms_get_cart_items( $user_id );

	foreach ( $cart_items as $cart_item ) {
		if ( $cart_item['is_subscription'] ) {
			stm_lms_delete_course_from_cart( $cart_item['item_id'] );
		}
	}
}
add_action( 'masterstudy_lms_before_add_to_cart', 'masterstudy_lms_remove_subscription_cart_item', 10, 2 );

// Add Admin Membership pages
function masterstudy_add_memberships_pages() {
	$offset = is_ms_lms_coupons_enabled() ? 1 : 0;
	add_submenu_page(
		'stm-lms-settings',
		esc_html__( 'Subscriptions & Memberships', 'masterstudy-lms-learning-management-system-pro' ),
		'<span class="stm-lms-students-menu-title stm-lms-top-delimiter"><span class="stm-lms-menu-text">' . esc_html__( 'Subscriptions & Memberships', 'masterstudy-lms-learning-management-system-pro' ) . '</span></span>',
		'manage_options',
		'manage_memberships',
		'masterstudy_lms_render_memberships_page',
		stm_lms_addons_menu_position() + ( 1 + $offset )
	);

	add_submenu_page(
		'stm-lms-settings',
		esc_html__( 'Membership Plans', 'masterstudy-lms-learning-management-system-pro' ),
		'<span class="stm-lms-students-menu-title"><span class="stm-lms-menu-text">' . esc_html__( 'Membership Plans', 'masterstudy-lms-learning-management-system-pro' ) . '</span>',
		'manage_options',
		'manage_membership_plans',
		'masterstudy_lms_render_membership_plans_page',
		stm_lms_addons_menu_position() + ( 2 + $offset )
	);
}
add_action( 'admin_menu', 'masterstudy_add_memberships_pages', 100001 );

// Render Admin Membership pages
function masterstudy_lms_render_memberships_page() {
	STM_LMS_Templates::show_lms_template( 'memberships' );
}

// Render Admin Membership Plans pages
function masterstudy_lms_render_membership_plans_page() {
	STM_LMS_Templates::show_lms_template( 'membership-plans' );
}

// Create Membership page if not exists
function masterstudy_lms_add_memberships_page( $id, $settings ) {
	$memberships_page = $settings['memberships_url'] ?? '';
	if ( ! empty( $memberships_page ) ) {
		$page_id = $settings['memberships_url'];
		$page    = get_post( $page_id );

		$pos = strpos( $page->post_content, 'masterstudy_membership_pricing' );
		if ( false === $pos ) {
			$updated_page = array(
				'ID'           => $page_id,
				'post_content' => $page->post_content . '[masterstudy_membership_pricing]',
			);

			wp_update_post( $updated_page );
		}
	}
}
add_action( 'wpcfto_after_settings_saved', 'masterstudy_lms_add_memberships_page', 10, 2 );
add_action( 'masterstudy_add_shortcode_memberships_page', 'masterstudy_lms_add_memberships_page', 10, 2 );

function masterstudy_lms_save_bundle_subscriptions( $bundle_id ) {
	check_ajax_referer( 'stm_lms_save_bundle', 'nonce' );

	$subscription_enabled = $_POST['subscription_enabled'] ?? false;

	if ( ! empty( $_POST['subscriptions'] ) && 'true' === $subscription_enabled ) {
		$plans_repository      = new SubscriptionPlanRepository();
		$plan_items_repository = new SubscriptionPlanItemRepository();

		$items = array(
			array(
				'object_id'   => $bundle_id,
				'object_type' => SubscriptionPlanType::BUNDLE,
			),
		);

		foreach ( $_POST['subscriptions'] as $subscription ) {
			$subscription['type']        = SubscriptionPlanType::BUNDLE;
			$subscription['is_featured'] = (int) $subscription['is_featured'];
			$plan_id                     = ! empty( $subscription['id'] ) ? $plans_repository->update( $subscription['id'], $subscription ) : $plans_repository->create( $subscription );

			if ( ! $plan_id && empty( $subscription['id'] ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Unable to save subscription plan', 'masterstudy-lms-learning-management-system-pro' ) . ". Plan name: {$subscription['name']}",
					)
				);
				return;
			}

			$plan_items_saved = ! empty( $subscription['id'] ) ? $plan_items_repository->update_plan_items( $subscription['id'], $items ) : $plan_items_repository->create( $plan_id, $items );

			if ( ! $plan_items_saved ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Failed to save plan items', 'masterstudy-lms-learning-management-system-pro' ),
					)
				);
				return;
			}
		}
	}
}

add_action( 'stm_lms_saved_bundle', 'masterstudy_lms_save_bundle_subscriptions' );


function masterstudy_lms_subscriptions_order_accepted( $user_id, $cart_items ): void {
	if ( ! empty( $cart_items ) ) {
		foreach ( $cart_items as $cart_item ) {
			if ( 1 === (int) $cart_item['is_subscription'] ) {
				$data = ( new SubscriptionPlanItemRepository() )->get_by_plan_id( $cart_item['item_id'] );

				foreach ( $data as $item ) {
					if ( isset( $item['object_type'] ) && 'course' === $item['object_type'] ) {
						\STM_LMS_Course::add_user_course( (int) $item['object_id'], $user_id, 0, 0, false, '', '', '', '', $item['plan_id'] );
					}
				}
			}
		}
	}

	// Clear the cart after processing
	stm_lms_get_delete_cart_items( $user_id );
}
add_action( 'stm_lms_order_accepted', 'masterstudy_lms_subscriptions_order_accepted', 10, 2 );
