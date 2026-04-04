<?php
// phpcs:ignoreFile

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Webhooks;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Gateways\Stripe;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Interfaces\WebhookInterface;
use STM_LMS_Helpers;
use Stripe\Webhook;

class StripeWebhooks extends Stripe implements WebhookInterface {

	/**
	 * Process webhook events
	 */
	public function handle_webhook( $payload, $headers ): void {
		try {
			$sig   = $headers['stripe_signature'][0] ?? '';
			$event = Webhook::constructEvent( $payload, $sig, $this->webhook_key );
			// Enhanced logging for invoice events
			if ( in_array( $event->type, array( 'invoice.payment_succeeded', 'invoice.payment_failed' ) ) ) {
				$invoice = $event->data->object;
			}
		} catch ( \Exception $e ) {
			error_log( "Webhook Error: " . $e->getMessage() );
			throw new \Exception( $e->getMessage() );
		}

		switch ( $event->type ) {
			case 'invoice.payment_succeeded':
				$this->payment_succeeded( $event->data->object );
				break;
			case 'invoice.payment_failed':
				$this->payment_failed( $event->data->object );
				break;
			case 'customer.subscription.deleted':
				$this->subscription_deleted( $event->data->object );
				break;
			case 'customer.subscription.updated':
				$this->subscription_updated( $event->data->object );
				break;
			case 'charge.refunded':
				$this->refund_processed( $event->data->object );
				break;
			default:
				break;
		}

	}

	/**
	 * Handle successful invoice payment
	 */
	private function payment_succeeded( $invoice ): void {
		// Enhanced subscription ID retrieval for new API version
		$subscription_id = $this->get_subscription_id_from_invoice( $invoice );

		if ( ! $subscription_id ) {
			return;
		}

		$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $subscription_id );
		if ( ! $subscription ) {
			return;
		}

		$currency = strtolower( $invoice->currency ?? 'usd' );
		if ( isset( $invoice->amount_paid ) ) {
			if ( 'jpy' === $currency ) {
				$amount = floatval( $invoice->amount_paid );
			} else {
				$amount = floatval( $invoice->amount_paid / 100 );
			}
		} else {
			$amount = 0;
		}

		// Update subscription status - only for real payments, not trial payments
		$is_trialing = $this->is_stripe_subscription_trialing( $subscription_id );

		if ( ! $is_trialing ) {
			$this->subscription_repository->update_status( $subscription['id'], 'active' );
		}

		// Update related order status
		$this->subscription_repository->update_related_order_status_by_gateway_invoice_id( $invoice->id, 'completed' );

		// Update next payment date
		$line = $invoice->lines->data[0] ?? null;
		if ( $line && ! empty( $line->period->end ) ) {
			$stripe_next_payment_date = gmdate( 'Y-m-d H:i:s', $line->period->end );

			// Check if this subscription has billing cycles limit and recalculate end date
			$plan = $this->subscription_meta_repository->get( $subscription['id'], 'plan' );
			$plan = is_array( $plan ) ? $plan : maybe_unserialize( $plan );

			if ( ! empty( $plan['billing_cycles'] ) && (int) $plan['billing_cycles'] > 0 ) {
				// Recalculate end date based on trial + billing cycles
				$trial_period    = $plan['trial_period'] ?? 0;
				$billing_cycles  = (int) $plan['billing_cycles'];
				$recurring_value = $plan['recurring_value'] ?? 1;

				// Total duration = trial + (billing_cycles * recurring_value)
				$total_days = $trial_period + ( $billing_cycles * $recurring_value );

				// Calculate end date from subscription start
				$subscription_start = $subscription['created_at'] ?? current_time( 'mysql' );
				$end_date           = gmdate( 'Y-m-d H:i:s', strtotime( $subscription_start . " +{$total_days} days" ) );

				// For limited subscriptions: next_payment_date = next payment, end_date = subscription end
				$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $stripe_next_payment_date );
				$this->subscription_repository->update_end_date( $subscription['id'], $end_date );

			} else {
				// Use Stripe's calculated date for unlimited subscriptions
				$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $stripe_next_payment_date );
				$this->subscription_repository->update_end_date( $subscription['id'], null, true ); // Unlimited subscriptions have no end date
			}
		}

		// Create related order
		$order_id = $this->create_order_from_invoice( $subscription, $invoice, 'completed' );

		do_action( 'masterstudy_lms_subscription_payment_succeeded', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle failed invoice payment
	 */
	private function payment_failed( $invoice ): void {
		// Enhanced subscription ID retrieval for new API version
		$subscription_id = $this->get_subscription_id_from_invoice( $invoice );

		if ( ! $subscription_id ) {
			return;
		}

		$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $subscription_id );
		if ( ! $subscription ) {
			return;
		}

		// Update subscription status - payment failure leads to expiration
		$this->subscription_repository->update_status( $subscription['id'], 'expired' );

		if ( ! empty( $invoice->next_payment_attempt ) ) {
			$retry_at = gmdate( 'Y-m-d H:i:s', $invoice->next_payment_attempt );
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $retry_at );
		}

		// Create related order
		$order_id = $this->create_order_from_invoice( $subscription, $invoice, 'failed' );

		do_action(
			'masterstudy_lms_subscription_payment_failed',
			$subscription['user_id'],
			$subscription['id'],
			$order_id
		);

		do_action( 'masterstudy_lms_subscription_expired', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Handle subscription deletion
	 */
	private function subscription_deleted( $stripe_subscription ): void {
		$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $stripe_subscription->id );
		if ( ! $subscription ) {
			return;
		}

		// Check if subscription is already cancelled or expired
		if ( in_array( $subscription['status'], array( 'cancelled', 'expired' ), true ) ) {
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', null );
			return;
		}

		// Determine if this is natural expiration or user cancellation
		// If subscription has cancel_at and it matches the ended_at, it's natural expiration
		$is_natural_expiration = false;
		if ( ! empty( $stripe_subscription->cancel_at ) && ! empty( $stripe_subscription->ended_at ) ) {
			$cancel_at_timestamp = $stripe_subscription->cancel_at;
			$ended_at_timestamp  = $stripe_subscription->ended_at;

			// If cancel_at and ended_at are close (within 1 minute), it's natural expiration
			if ( abs( $cancel_at_timestamp - $ended_at_timestamp ) <= 60 ) {
				$is_natural_expiration = true;
			}
		}

		// Set appropriate status
		if ( $is_natural_expiration ) {
//			$this->subscription_repository->update_status( $subscription['id'], 'expired' );

			mslms_schedule_subscription_expire_after_period_cron( $subscription['id'], ( new \MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository() )->get_recurring_interval_by_subscription_id( $subscription['id'] ) );

			do_action( 'masterstudy_lms_subscription_expired', $subscription['user_id'], $subscription['id'] );
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', null );
		} else {
			$this->subscription_repository->update_status( $subscription['id'], 'cancelled' );
			do_action( 'masterstudy_lms_subscription_cancelled', $subscription['user_id'], $subscription['id'] );
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', null );
		}

		// Update subscription with concrete end time if Stripe provides it
		if ( ! empty( $stripe_subscription->ended_at ) ) {
			$ended_at = gmdate( 'Y-m-d H:i:s', $stripe_subscription->ended_at );
			$this->subscription_repository->update_end_date( $subscription['id'], $ended_at );
		} elseif ( ! empty( $stripe_subscription->current_period_end ) ) {
			$ended_at = gmdate( 'Y-m-d H:i:s', $stripe_subscription->current_period_end );
			$this->subscription_repository->update_end_date( $subscription['id'], $ended_at );
		} else {
			$this->subscription_repository->update_end_date( $subscription['id'], null, false );
		}
	}

	/**
	 * Handle subscription update
	 */
	private function subscription_updated( $stripe_subscription ): void {
		$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $stripe_subscription->id );
		if ( ! $subscription ) {
			return;
		}

		// Update subscription status based on Stripe status
		$status_mapping = array(
			'active'   => 'active',
			'trialing' => 'trialing',
			'past_due' => 'expired',    // Natural expiration due to payment failure
			'canceled' => 'cancelled',  // User-initiated cancellation
			'unpaid'   => 'expired',    // Natural expiration due to unpaid status
		);

		$new_status = $status_mapping[ $stripe_subscription->status ] ?? 'unknown';
		$this->subscription_repository->update_status( $subscription['id'], $new_status );

		// If terminal state, clear next payment date
		if ( in_array( $new_status, array( 'expired', 'cancelled' ), true ) ) {
			$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', null );
		}

		// Log status changes for debugging
		if ( 'expired' === $new_status ) {
			do_action( 'masterstudy_lms_subscription_expired', $subscription['user_id'], $subscription['id'] );
		} elseif ( 'cancelled' === $new_status ) {
			error_log( "Stripe Webhook: Subscription {$subscription['id']} set to cancelled due to Stripe status: {$stripe_subscription->status}" );
		}

		if ( 'trialing' === $new_status && ! empty( $stripe_subscription->trial_end ) ) {
			$trial_end = gmdate( 'Y-m-d H:i:s', $stripe_subscription->trial_end );
			$this->subscription_repository->update_column( $subscription['id'], 'trial_end_date', $trial_end );
			$this->subscription_repository->update_end_date( $subscription['id'], $trial_end );
			$this->subscription_repository->update_column( $subscription['id'], 'is_trial_used', 1 );
		}

		// Renewals / period boundaries
		if ( ! empty( $stripe_subscription->current_period_end ) ) {
			$next_payment_date = gmdate( 'Y-m-d H:i:s', $stripe_subscription->current_period_end );

			// Check if this subscription has billing cycles limit and recalculate end date
			$plan = $this->subscription_meta_repository->get( $subscription['id'], 'plan' );
			$plan = is_array( $plan ) ? $plan : maybe_unserialize( $plan );

			if ( ! empty( $plan['billing_cycles'] ) && (int) $plan['billing_cycles'] > 0 ) {
				// Recalculate end date based on trial + billing cycles
				$trial_period    = $plan['trial_period'] ?? 0;
				$billing_cycles  = (int) $plan['billing_cycles'];
				$recurring_value = $plan['recurring_value'] ?? 1;

				// Total duration = trial + (billing_cycles * recurring_value)
				$total_days = $trial_period + ( $billing_cycles * $recurring_value );

				// Calculate end date from subscription start
				$subscription_start = $subscription['created_at'] ?? current_time( 'mysql' );
				$end_date           = gmdate( 'Y-m-d H:i:s', strtotime( $subscription_start . " +{$total_days} days" ) );

				// For limited subscriptions: next_payment_date = next payment, end_date = subscription end
				$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $next_payment_date );
				$this->subscription_repository->update_end_date( $subscription['id'], $end_date );

			} else {
				// Use Stripe's calculated date for unlimited subscriptions
				$this->subscription_repository->update_column( $subscription['id'], 'next_payment_date', $next_payment_date );
				$this->subscription_repository->update_end_date( $subscription['id'], null, true ); // Unlimited subscriptions have no end date
			}
		}

		// Capture cancel-at-period-end semantics
		if ( ! empty( $stripe_subscription->cancel_at_period_end ) && ! empty( $stripe_subscription->current_period_end ) ) {
			$end_date = gmdate( 'Y-m-d H:i:s', $stripe_subscription->current_period_end );
			$this->subscription_repository->update_end_date( $subscription['id'], $end_date );
		}

		do_action( 'masterstudy_lms_subscription_updated', $subscription['user_id'], $new_status, $subscription['id'] );
	}

	/**
	 * Process refund
	 */
	public function refund_processed( $stripe_refund ): void {
		try {
			// Find the related subscription via the charge -> invoice -> subscription chain.
			$charge_id = $stripe_refund->id ?? null;
			if ( ! $charge_id ) {
				return;
			}

			$charge          = \Stripe\Charge::retrieve( $charge_id );
			$invoice         = ! empty( $charge->invoice ) ? \Stripe\Invoice::retrieve( $charge->invoice ) : null;
			$subscription_id = $invoice && ! empty( $invoice->subscription ) ? $invoice->subscription : null;

			if ( ! $subscription_id ) {
				return;
			}

			$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $subscription_id );
			if ( ! $subscription ) {
				return;
			}

			// Mark refunded
			$this->subscription_repository->update_status( $subscription['id'], 'refunded' );
			// JPY is a zero-decimal currency, so don't divide by 100.
			$refund_currency = strtolower( $stripe_refund->currency ?? 'usd' );
			if ( 'jpy' === $refund_currency ) {
				$refund_amount = floatval( $stripe_refund->amount ?? 0 );
			} else {
				$refund_amount = floatval( ( $stripe_refund->amount ?? 0 ) / 100 );
			}

			$this->subscription_repository->update_column(
				$subscription['id'],
				'note',
				sprintf(
				// translators: %1$s - Refund ID, %2$s - Refund Amount, %3$s - Refund Currency
					esc_html__( 'Refund processed via Stripe. Refund ID: %1$s, Amount: %2$s %3$s', 'masterstudy-lms-learning-management-system-pro' ),
					$stripe_refund->id,
					$refund_amount,
					strtoupper( $stripe_refund->currency ?? '' )
				)
			);

			// Create related order
			$this->create_order_from_invoice( $subscription, $invoice, 'refunded' );

			do_action( 'masterstudy_lms_subscription_refunded', $subscription['user_id'], $subscription['id'] );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			throw new \Exception( 'Failed to process refund: ' . $e->getMessage() );
		}
	}

	/**
	 * Create an order from an invoice
	 */
	private function create_order_from_invoice( array $subscription, \Stripe\Invoice $invoice, string $status ): int {
		$order = $this->subscription_repository->get_subscription_order_query_by_gateway_invoice_id( $invoice->id );
		if ( $order->have_posts() ) {
			return $order->posts[0] ?? 0;
		}

		// Check if this is a trial invoice ($0 amount) and subscription already has an order
		// JPY is a zero-decimal currency, so don't divide by 100.
		$currency = strtolower( $invoice->currency ?? 'usd' );
		if ( isset( $invoice->amount_paid ) ) {
			if ( 'jpy' === $currency ) {
				$amount = floatval( $invoice->amount_paid );
			} else {
				$amount = floatval( $invoice->amount_paid / 100 );
			}
		} else {
			$amount = 0;
		}

//		$is_trialing = $this->is_stripe_subscription_trialing( (string) ( $invoice->subscription ?? '' ) );
		$gateway_subscription_id = $this->get_subscription_id_from_invoice( $invoice );
		$is_trialing             = $gateway_subscription_id
			? $this->is_stripe_subscription_trialing( $gateway_subscription_id )
			: false;

		if ( 0.0 == $amount && $is_trialing && (int) $subscription['first_order_id'] === (int) $subscription['active_order_id'] ) {
			$existing_order_id = (int) $subscription['first_order_id'];

			update_post_meta( $existing_order_id, 'status', $status );
			update_post_meta( $existing_order_id, 'gateway_invoice_id', $invoice->id );
			update_post_meta( $existing_order_id, 'subscription_id', $subscription['id'] );

			return $existing_order_id;
		}

		$plan = $this->subscription_meta_repository->get( $subscription['id'], 'plan' );
		$plan = is_array( $plan ) ? $plan : maybe_unserialize( $plan );

		$user_id  = (int) $subscription['user_id'];
		$currency = strtoupper(
			! empty( $invoice->currency )
				? (string) $invoice->currency
				: $this->get_default_currency_code()
		);

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
			'user_id'         => $user_id,
			'cart_items'      => array(
				array(
					'item_id'   => $plan['id'] ?? 0,
					'item_name' => $plan['name'] ?? 'Subscription',
					'quantity'  => 1,
					'price'     => $amount,
				),
			),
			'payment_code'    => 'stripe',
			'_order_total'    => $amount + $taxes,
			'_order_subtotal' => $amount,
			'_order_taxes'    => $taxes,
			'_order_currency' => $currency,
			'is_subscription' => 1,
			'plan'            => $plan,
		);

		$order_id = \STM_LMS_Order::create_order( $order_data, true );

		if ( ! $order_id ) {
			return 0;
		}

		update_post_meta( $order_id, 'status', $status );
		update_post_meta( $order_id, 'gateway_invoice_id', $invoice->id );
		update_post_meta( $order_id, 'subscription_id', $subscription['id'] );
		update_post_meta( $order_id, 'subscription_order_number', intval( $order->found_posts + 1 ) );

		$this->subscription_repository->update_column( $subscription['id'], 'active_order_id', $order_id );

		$this->cancel_subscription_if_billing_cycle_ended( $plan, $subscription );

		return $order_id;
	}

	private function is_stripe_subscription_trialing( string $gateway_subscription_id ): bool {
		try {
			$stripe_subscription = \Stripe\Subscription::retrieve( $gateway_subscription_id );
			return ( ! empty( $stripe_subscription->status ) && 'trialing' === $stripe_subscription->status );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Enhanced subscription ID retrieval for new Stripe API versions
	 * Handles various payload structures that may occur with different API versions
	 */
	private function get_subscription_id_from_invoice( $invoice ): ?string {
		// Method 1: Check nested subscription_details structure (from logs)
		if ( ! empty( $invoice->parent ) && ! empty( $invoice->parent->subscription_details ) && ! empty( $invoice->parent->subscription_details->subscription ) ) {
			return $invoice->parent->subscription_details->subscription;
		}

		// Method 2: Check invoice lines for nested subscription_item_details structure
		if ( ! empty( $invoice->lines ) && ! empty( $invoice->lines->data ) ) {
			foreach ( $invoice->lines->data as $index => $line ) {
				if ( ! empty( $line->parent ) && ! empty( $line->parent->subscription_item_details ) && ! empty( $line->parent->subscription_item_details->subscription ) ) {
					return $line->parent->subscription_item_details->subscription;
				}
			}
		}

		// Method 3: Direct access to subscription property (fallback)
		if ( ! empty( $invoice->subscription ) ) {
			return $invoice->subscription;
		}

		// Method 4: Check invoice lines for direct subscription property
		if ( ! empty( $invoice->lines ) && ! empty( $invoice->lines->data ) ) {
			foreach ( $invoice->lines->data as $index => $line ) {
				if ( ! empty( $line->subscription ) ) {
					return $line->subscription;
				}
			}
		}

		// Method 5: Check subscription_item if available
		if ( ! empty( $invoice->subscription_item ) ) {
			// Try to get subscription from subscription item
			try {
				$subscription_item = \Stripe\SubscriptionItem::retrieve( $invoice->subscription_item );
				if ( ! empty( $subscription_item->subscription ) ) {
					return $subscription_item->subscription;
				}
			} catch ( \Exception $e ) {
				error_log( "Failed to retrieve subscription_item: " . $e->getMessage() );
			}
		}

		// Method 6: Check for subscription in invoice metadata or other fields
		if ( ! empty( $invoice->metadata ) && ! empty( $invoice->metadata->subscription_id ) ) {
			return $invoice->metadata->subscription_id;
		}

		// Method 7: Retrieve full invoice from Stripe API
		try {
			$full_invoice = \Stripe\Invoice::retrieve( $invoice->id );

			if ( ! empty( $full_invoice->subscription ) ) {
				return $full_invoice->subscription;
			}

			// Check lines in full invoice
			if ( ! empty( $full_invoice->lines ) && ! empty( $full_invoice->lines->data ) ) {
				foreach ( $full_invoice->lines->data as $index => $line ) {
					if ( ! empty( $line->subscription ) ) {
						return $line->subscription;
					}
				}
			}

		} catch ( \Exception $e ) {
			error_log( "Failed to retrieve full invoice: " . $e->getMessage() );
		}

		// Method 8: Check if this is a subscription invoice by looking at the customer
		if ( ! empty( $invoice->customer ) ) {
			try {
				$customer = \Stripe\Customer::retrieve( $invoice->customer );
				if ( ! empty( $customer->subscriptions ) && ! empty( $customer->subscriptions->data ) ) {
					// Find the most recent active subscription
					foreach ( $customer->subscriptions->data as $sub ) {
						if ( $sub->status === 'active' || $sub->status === 'trialing' ) {
							return $sub->id;
						}
					}
				}
			} catch ( \Exception $e ) {
				error_log( "Failed to retrieve customer subscriptions: " . $e->getMessage() );
			}
		}

		// Method 9: Check if this is a setup invoice or one-time payment
		// For setup invoices, we might need to look at the payment intent
		if ( ! empty( $invoice->payment_intent ) ) {
			try {
				$payment_intent = \Stripe\PaymentIntent::retrieve( $invoice->payment_intent );
				if ( ! empty( $payment_intent->metadata ) && ! empty( $payment_intent->metadata->subscription_id ) ) {
					return $payment_intent->metadata->subscription_id;
				}
			} catch ( \Exception $e ) {
				error_log( "Failed to retrieve payment intent: " . $e->getMessage() );
			}
		}

		// Method 11: Check for subscription ID in invoice description or other text fields
		if ( ! empty( $invoice->description ) ) {
			// Look for subscription ID pattern in description
			if ( preg_match( '/sub_[a-zA-Z0-9]+/', $invoice->description, $matches ) ) {
				return $matches[0];
			}
		}

		// Method 12: Check for subscription ID in invoice footer or other text fields
		if ( ! empty( $invoice->footer ) ) {
			// Look for subscription ID pattern in footer
			if ( preg_match( '/sub_[a-zA-Z0-9]+/', $invoice->footer, $matches ) ) {
				return $matches[0];
			}
		}

		return null;
	}
}
