<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Webhooks;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Gateways\PayPal;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Interfaces\WebhookInterface;

class PayPalWebhooks extends PayPal implements WebhookInterface {

	/**
	 * Process webhook events
	 */
	public function handle_webhook( $payload, $headers ): void {
		$payload    = json_decode( $payload, true );
		$event_type = $payload['event_type'] ?? '';
		$resource   = $payload['resource'] ?? array();

		switch ( $event_type ) {
			case 'BILLING.SUBSCRIPTION.CREATED':
				$this->subscription_created( $resource );
				break;
			case 'BILLING.SUBSCRIPTION.ACTIVATED':
				$this->subscription_activated( $resource );
				break;
			case 'BILLING.SUBSCRIPTION.SUSPENDED':
				$this->subscription_suspended( $resource );
				break;
			case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
				$this->subscription_reactivated( $resource );
				break;
			case 'BILLING.SUBSCRIPTION.CANCELLED':
				$this->subscription_cancelled( $resource );
				break;
			case 'BILLING.SUBSCRIPTION.EXPIRED':
				$this->subscription_expired( $resource );
				break;
			case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
				$this->payment_failed( $resource );
				break;
			case 'PAYMENT.SALE.COMPLETED':
			case 'PAYMENT.CAPTURE.COMPLETED':
			case 'BILLING.SUBSCRIPTION.PAYMENT.SUCCEEDED':
				$this->payment_completed( $resource );
				break;
			case 'PAYMENT.SALE.DENIED':
			case 'PAYMENT.CAPTURE.DENIED':
				$this->payment_failed( $resource );
				break;
			case 'PAYMENT.SALE.REFUNDED':
			case 'PAYMENT.CAPTURE.REFUNDED':
				$this->payment_refunded( $resource );
				break;
			default:
				break;
		}
	}

	/**
	 * Extract subscription id from resource
	 */
	private function extract_subscription_id( array $resource ): string {
		if ( ! empty( $resource['billing_agreement_id'] ) ) {
			return $resource['billing_agreement_id'];
		}

		if ( ! empty( $resource['supplementary_data']['related_ids']['subscription_id'] ) ) {
			return $resource['supplementary_data']['related_ids']['subscription_id'];
		}

		if ( ! empty( $resource['id'] ) && 0 === strpos( (string) $resource['id'], 'I-' ) ) {
			return (string) $resource['id'];
		}

		return '';
	}

	/**
	 * Extract transaction amount and currency from resource
	 */
	private function extract_transaction_amount_currency( array $resource ): array {
		if ( ! empty( $resource['amount']['total'] ) && ! empty( $resource['amount']['currency'] ) ) {
			return array( (float) $resource['amount']['total'], strtoupper( $resource['amount']['currency'] ) );
		}

		if ( ! empty( $resource['amount']['value'] ) && ! empty( $resource['amount']['currency_code'] ) ) {
			return array( (float) $resource['amount']['value'], strtoupper( $resource['amount']['currency_code'] ) );
		}

		$default_currency = $this->currency
			? strtoupper( $this->currency )
			: strtoupper( \STM_LMS_Options::get_option( 'transactions_currency', 'USD' ) );

		return array( 0.0, $default_currency );
	}

	/**
	 * Extract transaction id from resource
	 */
	private function extract_transaction_id( array $resource ): string {
		if ( ! empty( $resource['id'] ) ) {
			return $resource['id'];
		}

		if ( ! empty( $resource['capture_id'] ) ) {
			return $resource['capture_id'];
		}

		return '';
	}

	/**
	 * Fetch subscription details from PayPal API
	 */
	private function fetch_subscription_details( string $subscription_id ): ?array {
		try {
			$response = wp_remote_get(
				$this->api_url . '/v1/billing/subscriptions/' . $subscription_id,
				array(
					'headers' => $this->get_request_headers(),
				)
			);

			if ( is_wp_error( $response ) ) {
				return null;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $body['id'] ) ) {
				return null;
			}

			return $body;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Extract next billing time from PayPal subscription data
	 */
	private function extract_next_billing_time_from_subscription( array $subscription_data ): ?string {
		if ( ! empty( $subscription_data['billing_info']['next_billing_time'] ) ) {
			$timestamp = strtotime( $subscription_data['billing_info']['next_billing_time'] );
			if ( $timestamp ) {
				return gmdate( 'Y-m-d H:i:s', $timestamp );
			}
		}

		if ( ! empty( $subscription_data['agreement_details']['next_billing_date'] ) ) {
			$timestamp = strtotime( $subscription_data['agreement_details']['next_billing_date'] );
			if ( $timestamp ) {
				return gmdate( 'Y-m-d H:i:s', $timestamp );
			}
		}

		return null;
	}

	/**
	 * Extract final payment time from PayPal subscription data
	 */
	private function extract_final_payment_time_from_subscription( array $subscription_data ): ?string {
		if ( ! empty( $subscription_data['billing_info']['final_payment_time'] ) ) {
			$timestamp = strtotime( $subscription_data['billing_info']['final_payment_time'] );
			if ( $timestamp ) {
				return gmdate( 'Y-m-d H:i:s', $timestamp );
			}
		}

		return null;
	}

	/**
	 * Get subscription row by PayPal subscription id
	 */
	private function get_subscription_row( string $paypal_subscription_id ): ?array {
		if ( empty( $paypal_subscription_id ) ) {
			return null;
		}

		return $this->subscription_repository->get_by_gateway_subscription_id( $paypal_subscription_id );
	}

	/**
	 * Handle subscription creation
	 */
	public function subscription_created( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}
		// For trial subscriptions, update the order status immediately if subscription is already active
		if ( ! empty( $resource['status'] ) && 'ACTIVE' === $resource['status'] ) {
			if ( $subscription['first_order_id'] === $subscription['active_order_id'] ) {
				update_post_meta( $subscription['first_order_id'], 'status', 'completed' );
				$this->subscription_repository->update_status( $subscription['id'], 'active' );
			}
		}

		$next_payment_date = $this->extract_next_billing_time_from_subscription( $resource );
		if ( empty( $next_payment_date ) ) {
			$subscription_data = $this->fetch_subscription_details( $paypal_subscription_id );
			$next_payment_date = $this->extract_next_billing_time_from_subscription( $subscription_data ?? array() );
		}

		if ( $next_payment_date ) {
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $next_payment_date );
			$this->subscription_repository->update_end_date( $subscription['id'], $next_payment_date );
		}

		do_action( 'masterstudy_lms_subscription_created', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle subscription activation
	 */
	public function subscription_activated( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}
		// Check if this is a trial subscription by looking at PayPal's cycle_executions
		$plan = $this->subscription_meta_repository->get( $subscription['id'], 'plan' );
		$plan = is_array( $plan ) ? $plan : maybe_unserialize( $plan );
		// Check if subscription has trial period and if it's still within that period
		$trial_period   = $plan['trial_period'] ?? 0;
		$trial_end_date = $subscription['trial_end_date'] ?? null;
		$now            = current_time( 'mysql' );
		$is_in_trial    = false;

		if ( $trial_period > 0 && $trial_end_date ) {
			$is_in_trial = ( strtotime( $now ) < strtotime( $trial_end_date ) );
		} else {
			// Fallback to checking PayPal's cycle_executions
			if ( ! empty( $resource['billing_info']['cycle_executions'] ) ) {
				foreach ( $resource['billing_info']['cycle_executions'] as $cycle ) {
					if ( isset( $cycle['tenure_type'] ) && 'TRIAL' === $cycle['tenure_type'] ) {
						// If trial cycle has remaining cycles, we're still in trial
						$cycles_remaining = $cycle['cycles_remaining'] ?? 0;
						$cycles_completed = $cycle['cycles_completed'] ?? 0;
						$is_in_trial = ( $cycles_remaining > 0 );
						break;
					}
				}
			}
		}
		// Set appropriate status based on trial
		if ( $is_in_trial ) {
			$this->subscription_repository->update_status( $subscription['id'], 'trialing' );
			error_log( "Updated subscription status to 'trialing' (in trial period)" );
		} else {
			$this->subscription_repository->update_status( $subscription['id'], 'active' );
			error_log( "Updated subscription status to 'active'" );
		}

		// For trial subscriptions, we need to update the first order, not use resource['id']
		if ( $subscription['first_order_id'] === $subscription['active_order_id'] ) {
			update_post_meta( $subscription['first_order_id'], 'status', 'completed' );
		} else {
			$this->subscription_repository->update_related_order_status_by_gateway_invoice_id( $resource['id'], 'completed' );
		}

		// Update subscription dates with fresh data
		$next_payment_date = $this->extract_next_billing_time_from_subscription( $resource );
		if ( empty( $next_payment_date ) ) {
			$subscription_data = $this->fetch_subscription_details( $paypal_subscription_id );
			$next_payment_date = $this->extract_next_billing_time_from_subscription( $subscription_data ?? array() );
		}

		if ( $next_payment_date ) {
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $next_payment_date );
			$this->subscription_repository->update_end_date( $subscription['id'], $next_payment_date );
		}

		do_action( 'masterstudy_lms_subscription_activated', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle subscription suspended
	 */
	public function subscription_suspended( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		$this->subscription_repository->update_status( $subscription['id'], 'suspended' );

		do_action( 'masterstudy_lms_subscription_suspended', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle subscription reactivation
	 */
	public function subscription_reactivated( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		// Update subscription status
		$this->subscription_repository->update_status( $subscription['id'], 'active' );

		// Update related order status
		$this->subscription_repository->update_related_order_status_by_gateway_invoice_id( $resource['id'], 'completed' );

		// Update subscription dates with fresh data
		$next_payment_date = $this->extract_next_billing_time_from_subscription( $resource );
		if ( empty( $next_payment_date ) ) {
			$subscription_data = $this->fetch_subscription_details( $paypal_subscription_id );
			$next_payment_date = $this->extract_next_billing_time_from_subscription( $subscription_data ?? array() );
		}

		if ( $next_payment_date ) {
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $next_payment_date );
			$this->subscription_repository->update_end_date( $subscription['id'], $next_payment_date );
		}

		do_action( 'masterstudy_lms_subscription_reactivated', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle subscription cancellation
	 */
	public function subscription_cancelled( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		// Check if subscription is already cancelled
		if ( 'cancelled' === $subscription['status'] ) {
			return;
		}

		$this->subscription_repository->update_status( $subscription['id'], 'cancelled' );

		// Update subscription end date
		$final_payment_time = $this->extract_final_payment_time_from_subscription( $resource );
		if ( empty( $final_payment_time ) ) {
			$subscription_data  = $this->fetch_subscription_details( $paypal_subscription_id );
			$final_payment_time = $this->extract_final_payment_time_from_subscription( $subscription_data ?? array() );
		}

		if ( $final_payment_time ) {
			$this->subscription_repository->update_end_date( $subscription['id'], $final_payment_time );
		}

		do_action( 'masterstudy_lms_subscription_cancelled', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle subscription expired
	 */
	public function subscription_expired( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		mslms_schedule_subscription_expire_after_period_cron( $subscription['id'], ( new \MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository() )->get_recurring_interval_by_subscription_id( $subscription['id'] ) );

		$this->subscription_repository->update_column( (int) $subscription['id'], 'next_payment_date', null );

		//$this->subscription_repository->update_status( $subscription['id'], 'expired' );
		$this->subscription_repository->update_end_date( $subscription['id'], null, false );

		do_action( 'masterstudy_lms_subscription_expired', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle payment completion - this is the key method for renewals
	 */
	public function payment_completed( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		list( $amount, $currency ) = $this->extract_transaction_amount_currency( $resource );

		// Check if this is a trial payment
		$plan = $this->subscription_meta_repository->get( $subscription['id'], 'plan' );
		$plan = is_array( $plan ) ? $plan : maybe_unserialize( $plan );

		$trial_period  = $plan['trial_period'] ?? 0;
		$is_trial_used = $subscription['is_trial_used'] ?? 0;
		// Check if this is a trial payment (amount = 0 and trial period exists)
		$is_trial_payment = ( 0 == $amount && 0 < $trial_period && ! $is_trial_used );

		// Update status based on whether it's a trial payment
		if ( $is_trial_payment ) {
			$this->subscription_repository->update_status( $subscription['id'], 'trialing' );
		} else {
			$this->subscription_repository->update_status( $subscription['id'], 'active' );
		}

		// Update subscription dates with fresh data
		$subscription_data = $this->fetch_subscription_details( $paypal_subscription_id );
		if ( $subscription_data ) {
			$next_payment_date = $this->extract_next_billing_time_from_subscription( $subscription_data );
			if ( $next_payment_date ) {
				$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $next_payment_date );
				$this->subscription_repository->update_end_date( $subscription['id'], $next_payment_date );
			}

			// Update subscription end date
			$final_payment_time = $this->extract_final_payment_time_from_subscription( $subscription_data );
			if ( $final_payment_time ) {
				$this->subscription_repository->update_end_date( $subscription['id'], $final_payment_time );
			}
		}

		// Create order from payment
		$order_id = $this->create_order_from_payment( $subscription, $resource, 'completed' );

		do_action( 'masterstudy_lms_subscription_payment_completed', $subscription['user_id'], $subscription['id'], $order_id );
	}

	/**
	 * Handle payment denial
	 */
	public function payment_failed( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		$this->subscription_repository->update_status( $subscription['id'], 'past_due' );

		// Create order from payment
		$order_id = $this->create_order_from_payment( $subscription, $resource, 'failed' );

		do_action( 'masterstudy_lms_subscription_payment_failed', $subscription['user_id'], $subscription['id'], $order_id );
	}

	/*
	 * Handle payment refund
	 */
	public function payment_refunded( array $resource ): void {
		$paypal_subscription_id = $this->extract_subscription_id( $resource );
		if ( ! $paypal_subscription_id ) {
			return;
		}

		$subscription = $this->get_subscription_row( $paypal_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		$this->subscription_repository->update_status( $subscription['id'], 'refunded' );

		$order_id = $this->create_order_from_payment( $subscription, $resource, 'refunded' );

		do_action( 'masterstudy_lms_subscription_payment_refunded', $subscription['user_id'], $subscription['id'], $order_id );
	}

	/**
	 * Maybe create order from payment
	 */
	private function create_order_from_payment( array $subscription, array $resource, string $status ): ?int {
		list( $amount, $currency ) = $this->extract_transaction_amount_currency( $resource );
		$txn_id = $this->extract_transaction_id( $resource );

		// 1) Dedupe by gateway invoice/transaction id.
		$order = $this->subscription_repository->get_subscription_order_query_by_gateway_invoice_id( $txn_id );
		if ( $order->have_posts() ) {
			return (int) ( $order->posts[0] ?? 0 );
		}

		// 2) Update the first order only once (initial/trial/first payment).
		if ( (int) $subscription['first_order_id'] === (int) $subscription['active_order_id'] ) {
			$existing_order_id = (int) $subscription['first_order_id'];
			$existing_invoice  = get_post_meta( $existing_order_id, 'gateway_invoice_id', true );

			if ( empty( $existing_invoice ) ) {
				// First charge/trial → attach this payment to the existing first order.
				update_post_meta( $existing_order_id, 'status', $status );
				update_post_meta( $existing_order_id, 'gateway_invoice_id', $txn_id );
				update_post_meta( $existing_order_id, 'subscription_id', (int) $subscription['id'] );

				return $existing_order_id;
			}
			// else: first order already holds an invoice → fall through to create a NEW order.
		}

		// 3) Create a NEW order for this payment.
		$plan = $this->subscription_meta_repository->get( (int) $subscription['id'], 'plan' );
		$plan = is_array( $plan ) ? $plan : maybe_unserialize( $plan );

		/**
		 * Copy taxes from first order to all orders (if first order had taxes).
		 */
		$first_order_id = (int) ( $subscription['first_order_id'] ?? 0 );
		$taxes          = 0.0;

		if ( $first_order_id > 0 ) {
			$first_sub_total = get_post_meta( $first_order_id, '_order_subtotal', true );
			$taxes = $amount - $first_sub_total;
		}

		$amount = $amount - $taxes;

		$order_data = array(
			'user_id'         => (int) $subscription['user_id'],
			'cart_items'      => array(
				array(
					'item_id'   => isset( $plan['id'] ) ? (int) $plan['id'] : 0,
					'item_name' => isset( $plan['name'] ) ? (string) $plan['name'] : 'Subscription',
					'quantity'  => 1,
					'price'     => (float) $amount,
				),
			),
			'payment_code'    => 'paypal',
			'_order_total'    => (float) $amount + $taxes,
			'_order_subtotal' => (float) $amount,
			'_order_taxes'    => $taxes,
			'_order_currency' => strtoupper( (string) $currency ),
			'is_subscription' => 1,
			'plan'            => $plan,
		);

		$order_id = \STM_LMS_Order::create_order( $order_data, true );
		if ( ! $order_id ) {
			return 0;
		}

		update_post_meta( $order_id, 'status', $status );
		update_post_meta( $order_id, 'gateway_invoice_id', $txn_id );
		update_post_meta( $order_id, 'subscription_id', (int) $subscription['id'] );

		// (Optional) sequential number inside the subscription:
		// $seq = (int) $this->subscription_repository->get_subscription_orders_query( (int) $subscription['id'], 'ids' )->found_posts;
		// update_post_meta( $order_id, 'subscription_order_number', $seq );

		// Move the active order pointer to this new order.
		$this->subscription_repository->update_column( (int) $subscription['id'], 'active_order_id', (int) $order_id );

		// Respect limited billing cycles.
		//$this->cancel_subscription_if_billing_cycle_ended( $plan, $subscription );

		return (int) $order_id;
	}
}
