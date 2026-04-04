<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment;

use MasterStudy\Lms\Ecommerce\AbstractPayment;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionMetaRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

abstract class SubscriptionPayment extends AbstractPayment {
	protected $subscription_repository;

	protected $subscription_meta_repository;

	protected static $gateway_name;

	abstract public function subscribe();

	abstract public function cancel_gateway_subscription( $subscription_id );

	public function __construct() {
		$this->subscription_repository      = new SubscriptionRepository();
		$this->subscription_meta_repository = new SubscriptionMetaRepository();

		$payment = \STM_LMS_Options::get_option( 'payment_methods' );

		if ( empty( $payment[ static::$gateway_name ] ) || empty( $payment[ static::$gateway_name ]['enabled'] ) ) {
			throw new \Exception( 'Payment gateway is not configured' );
		}

		$this->setup( $payment[ static::$gateway_name ]['fields'] );
	}

	// Inside class PayPal extends SubscriptionPayment
	protected function log_debug( $message, $context = array() ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$prefix  = '[MS Subscriptions PayPal] ';
			$payload = ! empty( $context ) ? wp_json_encode( $context ) : '';
			error_log( $prefix . $message . ' ' . $payload ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Get default LMS currency code (from plugin settings).
	 */
	protected function get_default_currency_code(): string {
		// Try dedicated currency_code option first (if it exists in some installs).
		$code = (string) \STM_LMS_Options::get_option( 'currency_code', '' );

		// Fallback to transactions_currency (present in your settings).
		if ( '' === $code ) {
			$code = (string) \STM_LMS_Options::get_option( 'transactions_currency', 'USD' );
		}

		if ( '' === $code ) {
			$code = 'USD';
		}

		return strtoupper( $code );
	}

	/**
	 * Check if the current plan is free
	 */
	public function is_free_plan(): bool {
		return 0.00 === (float) $this->data['_order_total'];
	}

	/**
	 * Create a Stripe subscription
	 */
	public function create_subscription() {
		$is_trial               = SubscriptionPlanRepository::is_plan_trial( $this->data['plan'] );
		$subscription_datetimes = ( new SubscriptionPlanRepository() )->calculate_plan_datetimes( $this->data['plan'], false );
		$subscription_data      = array(
			'user_id'         => $this->data['user_id'],
			'plan_id'         => $this->data['plan']['id'],
			'first_order_id'  => $this->data['order_id'],
			'active_order_id' => $this->data['order_id'],
			'status'          => strtolower( $this->data['gateway_subscription_status'] ?? 'active' ),
			'is_trial_used'   => $is_trial ? 1 : 0,
			'note'            => null,
			'created_at'      => current_time( 'mysql' ),
			'updated_at'      => current_time( 'mysql' ),
		);

		$subscription_id = $this->subscription_repository->create(
			array_merge( $subscription_datetimes, $subscription_data )
		);

		$this->data['subscription_id']     = $subscription_id;
		$this->data['subscription_status'] = $subscription_data['status'];

		$this->subscription_meta_repository->create(
			$subscription_id,
			'plan',
			maybe_serialize( $this->data['plan'] )
		);

		$this->subscription_meta_repository->create(
			$subscription_id,
			'gateway_subscription_id',
			$this->data['gateway_subscription_id'] ?? null
		);

		$this->subscription_meta_repository->create(
			$subscription_id,
			'payment_method_id',
			$this->data['payment_method_id']
		);

		$this->subscription_meta_repository->create(
			$subscription_id,
			'gateway',
			static::$gateway_name
		);

		do_action( 'masterstudy_lms_subscription_created', $this->data['user_id'], $subscription_id );

		return $subscription_id;
	}

	/**
	 * Cancel a subscription
	 *
	 * @param int $subscription_id Subscription ID.
	 * @param bool $immediate Whether to cancel immediately (true) or at period end (false). Default false.
	 */
	public function cancel_subscription( $subscription_id, bool $immediate = false ) {
		$subscription = $this->subscription_repository->get( $subscription_id );
		if ( ! $subscription ) {
			return;
		}

		$response = apply_filters( 'masterstudy_lms_subscription_before_cancel', true, (int) $subscription_id, (int) $subscription['user_id'] );

		if ( true !== $response ) {
			return $response;
		}

		// Cancel the gateway subscription
		if ( $immediate && method_exists( $this, 'cancel_gateway_subscription_now' ) ) {
			// For Stripe: use immediate cancellation when requested
			$this->cancel_gateway_subscription_now( $subscription_id );
		} else {
			// Default: cancel at period end (or immediate for PayPal which doesn't support period-end)
			$this->cancel_gateway_subscription( $subscription_id );
		}

		$this->subscription_repository->update_status( $subscription_id, 'cancelled' );

		do_action( 'masterstudy_lms_subscription_cancelled', $subscription['user_id'], $subscription_id );

		return true;
	}

	/**
	 * Handle free plan subscription creation
	 */
	protected function handle_free_plan_subscription(): bool {
		try {
			// Set free plan specific data
			$this->data['gateway_subscription_id']     = null;
			$this->data['gateway_subscription_status'] = 'active';
			$this->data['payment_method_id']           = null;

			// Create subscription directly in the system
			$this->create_subscription();

			// Force activate subscription and disable next payments
			$this->force_activate_subscription( $this->data['subscription_id'] );

			return true;
		} catch ( \Exception $e ) {
			$this->data['error'] = $e->getMessage();

			return false;
		}
	}

	/**
	 * Cancel subscription if billing cycle ended
	 */
	public function cancel_subscription_if_billing_cycle_ended( array $plan, array $subscription ): void {
		$billing_cycles = masterstudy_lms_subscription_plan_billing_cycles_limit( $plan );
		if ( $billing_cycles <= 0 ) {
			return;
		}

		// If cancel_at is already set on Stripe, don't do anything here.
		$gateway_id = $subscription['meta']['gateway_subscription_id'] ?? '';
		if ( $gateway_id ) {
			try {
				$sub = \Stripe\Subscription::retrieve( $gateway_id );
				if ( ! empty( $sub->cancel_at ) || ! empty( $sub->cancel_at_period_end ) ) {
					return; // already scheduled, do nothing
				}
			} catch ( \Exception $e ) {
				error_log( $e->getMessage() );
			}
		}

		// Optionally: if you prefer to decide by local count, only schedule period-end (not delete).
		$orders           = $this->subscription_repository->get_subscription_orders_query( $subscription['id'], 'ids', $billing_cycles, 'completed' );
		$completed_orders = (int) $orders->found_posts;

		if ( $completed_orders >= $billing_cycles ) {
			// We’ve collected all planned payments; finish at period end.
			$this->cancel_gateway_subscription( $subscription['id'] ); // now period-end, not delete
		}
	}

	/**
	 * Force activate subscription and disable next payments
	 */
	public function force_activate_subscription( $subscription_id ) {
		// Get current subscription status before forcing activation
		$subscription = $this->subscription_repository->get( $subscription_id );
		if ( ! $subscription ) {
			return;
		}

		// Don't force activate if subscription was cancelled by webhook
		// This prevents overriding webhook cancellations
		if ( 'cancelled' === $subscription['status'] ) {
			return;
		}

		$this->subscription_repository->update_status( $subscription_id, 'active' );
		$this->subscription_repository->update_column( $subscription_id, 'end_date', null );
		$this->subscription_repository->update_column( $subscription_id, 'next_payment_date', null );
	}
}
