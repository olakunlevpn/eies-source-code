<?php

use MasterStudy\Lms\Plugin\Taxonomy;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\ReccuringInterval;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;

function masterstudy_lms_is_subscription_plan_recurring( $plan ) {
	$is_recurring = true;

	if ( ! empty( $plan ) && 0 < intval( $plan['billing_cycles'] ) ) {
		$is_recurring = false;
	}

	return apply_filters( 'masterstudy_lms_is_subscription_plan_recurring', $is_recurring, $plan );
}

function masterstudy_lms_subscription_plan_billing_cycles_limit( $plan ) {
	if ( empty( $plan['billing_cycles'] ) ) {
		return 0;
	}

	$billing_cycles = intval( $plan['billing_cycles'] );

	return apply_filters( 'masterstudy_lms_subscription_plan_billing_cycles_limit', $billing_cycles, $plan );
}

function masterstudy_lms_get_membership_plans_template_vars() {
	return array(
		'recurring_intervals' => array_map( 'strval', ReccuringInterval::cases() ),
		'categories'          => array_values( get_terms( array( 'taxonomy' => Taxonomy::COURSE_CATEGORY ) ) ),
	);
}

function masterstudy_lms_get_membership_url() {
	$settings = get_option( 'stm_lms_settings', array() );

	if ( empty( $settings['memberships_url'] ) ) {
		return home_url( '/' );
	}

	return get_the_permalink( $settings['memberships_url'] );
}

function masterstudy_lms_get_subscription_types_labels() {
	return array(
		'full_site' => __( 'Sitewide', 'masterstudy-lms-learning-management-system-pro' ),
		'category'  => __( 'Category-Based', 'masterstudy-lms-learning-management-system-pro' ),
		'course'    => __( 'Course', 'masterstudy-lms-learning-management-system-pro' ),
	);
}

/**
 * Schedule a one-time cron job for a specific ID and period.
 *
 * @param int    $id     The unique ID (user, subscription, etc.).
 * @param string $period One of: day, week, month, year.
 */
function mslms_schedule_subscription_expire_after_period_cron( $id, $period ) {
	if ( empty( $id ) || empty( $period ) ) {
		return;
	}

	// Map period to timestamp offset.
	switch ( $period ) {
		case 'day':
			$timestamp = strtotime( '+1 day' );
			break;
		case 'week':
			$timestamp = strtotime( '+1 week' );
			break;
		case 'month':
			$timestamp = strtotime( '+1 month' );
			break;
		case 'year':
			$timestamp = strtotime( '+1 year' );
			break;
		default:
			error_log( sprintf( 'Invalid period "%s" for ID %d', $period, $id ) );
			return;
	}

	// Check if already scheduled.
	if ( ! wp_next_scheduled( 'mslms_schedule_subscription_expire_after_period_cron_action', array( $id, $period ) ) ) {
		wp_schedule_single_event( $timestamp, 'mslms_schedule_subscription_expire_after_period_cron_action', array( $id, $period ) );
		error_log( sprintf( 'Scheduled %s cron for ID %d to run at %s', $period, $id, gmdate( 'Y-m-d H:i:s', $timestamp ) ) );
	}
}

/**
 * Hook that runs when the cron triggers.
 *
 * @param int    $id     The unique ID for the task.
 * @param string $period The period string (day/week/month/year).
 */
add_action( 'mslms_schedule_subscription_expire_after_period_cron_action', 'mslms_schedule_subscription_expire_after_period_cron_handle', 10, 2 );

function mslms_schedule_subscription_expire_after_period_cron_handle( $id, $period ) {
	// Your business logic here.
	error_log( sprintf( '%s cron ended with ID %d', ucfirst( $period ), $id ) );

	//			$this->subscription_repository->update_status( $subscription['id'], 'expired' );

	( ( new \MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository() )->update_status( $id, 'expired' ) );
	// Example: if you wanted to perform something specific
	// mslms_process_subscription_expiration( $id, $period );

	// Clean up to ensure it's never repeated.
	$timestamp = wp_next_scheduled( 'mslms_schedule_subscription_expire_after_period_cron_action', array( $id, $period ) );
	if ( false !== $timestamp ) {
		wp_unschedule_event( $timestamp, 'mslms_schedule_subscription_expire_after_period_cron_action', array( $id, $period ) );
	}
}

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

function ms_lms_should_send_renewed_email( int $subscription_id ): bool {
	$repo = new SubscriptionRepository();
	$sub  = $repo->get( $subscription_id );
	if ( empty( $sub ) ) {
		return false;
	}

	// Don’t send “renewed” on the very first cycle (activation / first invoice).
	$first  = (int) ( $sub['first_order_id'] ?? 0 );
	$active = (int) ( $sub['active_order_id'] ?? 0 );

	// If they are equal, we are still in the initial cycle.
	if ( $first > 0 && $first === $active ) {
		return false;
	}

	// Optional: also suppress if the subscription is currently in trial.
	if ( ( $sub['status'] ?? '' ) === 'trialing' ) {
		return false;
	}

	return true;
}
