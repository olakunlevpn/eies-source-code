<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Gateways;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\SubscriptionPayment;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use Stripe\Util\ApiVersion;

class Stripe extends SubscriptionPayment {
	protected $secret_key;

	protected $webhook_url;

	protected $webhook_key;

	public static $gateway_name = 'stripe';

	public function setup( array $config ): void {
		$this->secret_key  = $config['secret_key'] ?? '';
		$this->webhook_url = $config['webhook_url'] ?? '';
		$this->webhook_key = $config['webhook_key'] ?? '';

		\Stripe\Stripe::setApiKey( $this->secret_key );
		\Stripe\Stripe::setApiVersion( '2025-09-30.clover' );
		\Stripe\Stripe::setAppInfo(
			'MasterStudy',
			STM_LMS_PRO_VERSION,
			'https://stylemixthemes.com'
		);
	}

	public function check(): bool {
		return ! empty( $this->secret_key ) && ! empty( $this->webhook_url );
	}

	/**
	 * True free plan = recurring plan price is 0.
	 * Trial or coupons must NOT make it "free".
	 */
	private function is_truly_free_plan(): bool {
		if ( empty( $this->data['plan'] ) || ! is_array( $this->data['plan'] ) ) {
			return false;
		}

		$plan_price = (float) SubscriptionPlanRepository::get_actual_price_with_taxes( $this->data['plan'] );

		return $plan_price <= 0;
	}

	/**
	 * Create a Stripe customer
	 */
	private function create_customer( int $user_id ): \Stripe\Customer {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			throw new \Exception( 'User not found' );
		}

		// Check if customer already exists
		$existing_customer_id = get_user_meta( $user_id, 'stripe_customer_id', true );
		if ( $existing_customer_id ) {
			try {
				return \Stripe\Customer::retrieve( $existing_customer_id );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				throw new \Exception( 'Failed to retrieve Stripe customer: ' . $e->getMessage() );
			}
		}

		$customer = \Stripe\Customer::create(
			array(
				'email'    => $user->user_email,
				'name'     => $user->display_name,
				'metadata' => array(
					'user_id' => $user_id,
				),
			)
		);

		update_user_meta( $user_id, 'stripe_customer_id', $customer->id );

		return $customer;
	}

	/**
	 * Create a Stripe price for subscription
	 */
	private function create_price(): \Stripe\Price {
		$price    = SubscriptionPlanRepository::get_actual_price_with_taxes( $this->data['plan'] );
		$currency = strtolower( (string) $this->data['currency'] );

		if ( 'jpy' === $currency ) {
			$unit_amount = intval( round( floatval( $price ) ) );
		} else {
			$unit_amount = intval( round( floatval( $price ) * 100 ) );
		}

		$price_params = array(
			'unit_amount'  => $unit_amount,
			'currency'     => $currency,
			'recurring'    => array(
				'interval'       => $this->data['plan']['recurring_interval'],
				'interval_count' => $this->data['plan']['recurring_value'],
			),
			'product_data' => array(
				'name' => $this->data['plan']['name'],
			),
		);

		return \Stripe\Price::create( $price_params );
	}

	/**
	 * Get payment method
	 */
	private function get_payment_method() {
		if ( ! empty( $this->data['payment_method_id'] ) ) {
			try {
				return \Stripe\PaymentMethod::retrieve( $this->data['payment_method_id'] );
			} catch ( \Stripe\Exception\ApiErrorException $e ) {
				$this->data['error'] = $e->getMessage();
				return false;
			}
		}

		return false;
	}

	/**
	 * Process charges
	 */
	private function process_charges() {
		$payment_intent = $this->get_payment_intent();
		if ( empty( $payment_intent ) ) {
			return false;
		}

		$this->data['stripe_payment_intent'] = $payment_intent;

		$this->confirm_payment_intent( $payment_intent );

		if ( ! empty( $this->data['error'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Confirm payment intent
	 */
	private function get_payment_intent() {
		if ( ! empty( $this->data['stripe_payment_intent'] ) ) {
			try {
				$payment_intent = \Stripe\PaymentIntent::retrieve( $this->data['stripe_payment_intent'] );
			} catch ( \Exception $e ) {
				$this->data['error'] = $e->getMessage();
				return false;
			}
		}

		if ( empty( $payment_intent ) ) {
			$payment_intent = $this->create_payment_intent();
		}

		return $payment_intent ?? false;
	}

	/**
	 * Create a Stripe payment intent
	 */
	private function create_payment_intent() {
		$params = array(
			'customer'             => $this->data['stripe_customer'],
			'payment_method'       => $this->data['payment_method_id'],
			'payment_method_types' => array( 'card' ),
			'amount'               => $this->data['amount'],
			'currency'             => strtolower( (string) $this->data['currency'] ),
			'confirmation_method'  => 'manual',
			'setup_future_usage'   => 'off_session',
		);

		try {
			$payment_intent = \Stripe\PaymentIntent::create( $params );
		} catch ( \Exception $e ) {
			$this->data['error'] = $e->getMessage();
			return false;
		}

		return $payment_intent;
	}

	/**
	 * Confirm payment intent
	 */
	private function confirm_payment_intent( $payment_intent ) {
		try {
			$params = array(
				'expand' => array(
					'payment_method',
				),
			);
			$payment_intent->confirm( $params );
		} catch ( \Exception $e ) {
			$this->data['error'] = $e->getMessage();
			return false;
		}

		if ( 'requires_action' === $payment_intent->status ) {
			$this->data['error'] = esc_html__( 'To finish this transaction, you need to verify your identity with your payment provider. Please follow the authentication steps required.', 'masterstudy-lms-learning-management-system-pro' );

			return false;
		}

		return true;
	}

	/**
	 * Subscribe to a Stripe subscription
	 */
	public function subscribe() {
		// Check if this is a free plan
		if ( $this->is_truly_free_plan() ) {
			return $this->handle_free_plan_subscription();
		}

		$this->data['currency'] = $this->get_default_currency_code();

		$customer       = $this->create_customer( $this->data['user_id'] );
		$price          = $this->create_price();
		$payment_method = $this->get_payment_method();

		$this->data['stripe_customer'] = $customer;
		$this->data['amount']          = $price->unit_amount;

		// Process charges
		// IMPORTANT For All Developers: Do not pre-charge via standalone PaymentIntent to avoid double charges.
		if ( $payment_method ) {
			try {
				if ( empty( $payment_method->customer ) ) {
					$payment_method->attach(
						array(
							'customer' => $customer->id,
						)
					);
				}
			} catch ( \Exception $e ) {
				$this->data['error'] = $e->getMessage();
				return false;
			}
		}

		// Create Subscription
		$stripe_subscription = $this->create_stripe_subscription( $price );
		if ( ! empty( $stripe_subscription ) ) {
			$this->data['gateway_subscription_id']     = $stripe_subscription->id;
			$this->data['gateway_subscription_status'] = $stripe_subscription->status;
			if ( ! empty( $stripe_subscription->latest_invoice ) ) {
				$invoice = $stripe_subscription->latest_invoice;
				if ( ! is_object( $invoice ) || empty( $invoice->payment_intent ) ) {
					try {
						$invoice_id = is_object( $invoice ) ? $invoice->id : (string) $invoice;
						$invoice    = \Stripe\Invoice::retrieve(
							array(
								'id'     => $invoice_id,
								'expand' => array( 'payment_intent' ),
							)
						);
					} catch ( \Exception $e ) {
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
							error_log(
								sprintf(
									'Stripe Invoice retrieve failed for %s: %s',
									$invoice_id,
									$e->getMessage()
								)
							);
						}
					}
				}

				$this->data['gateway_invoice_id'] = is_object( $invoice ) ? ( $invoice->id ?? '' ) : '';

				$pi = is_object( $invoice ) ? ( $invoice->payment_intent ?? null ) : null;
				if ( is_object( $pi ) && ! empty( $pi->client_secret ) ) {
					$requires_action_statuses = array( 'requires_action', 'requires_payment_method', 'requires_confirmation' );
					if ( empty( $pi->status ) || in_array( $pi->status, $requires_action_statuses, true ) ) {
						$this->data['client_secret'] = $pi->client_secret;
					}
				}

				if ( 'incomplete' === $stripe_subscription->status && is_object( $invoice ) && ! empty( $invoice->hosted_invoice_url ) ) {
					$this->data['redirect_url'] = $invoice->hosted_invoice_url;
				} elseif ( 'active' === $stripe_subscription->status || 'trialing' === $stripe_subscription->status ) {
					$this->data['redirect_url'] = $this->data['thankyou_url'] ?? '';
				}
			}
		}

		try {
			$this->create_subscription();
		} catch ( \Exception $e ) {
			$this->data['error'] = $e->getMessage();
			return false;
		}

		return true;
	}

	/**
	 * Create (or reuse) a Stripe Coupon for the first invoice only.
	 */
	private function create_stripe_first_invoice_coupon() {
		if ( empty( $this->data['coupon_type'] ) || empty( $this->data['coupon_value'] ) ) {
			return null;
		}

		$params = array(
			'duration' => 'once',
		);

		$coupon_type = (string) $this->data['coupon_type'];
		$currency    = strtolower( (string) $this->data['currency'] );

		if ( 'percent' === $coupon_type ) {
			$params['percent_off'] = (float) $this->data['coupon_value'];
		} else {
			$amount_off = (float) $this->data['coupon_value'];

			if ( 'jpy' === $currency ) {
				$params['amount_off'] = (int) round( $amount_off );
			} else {
				$params['amount_off'] = (int) round( $amount_off * 100 );
			}

			$params['currency'] = $currency;
		}

		$coupon = \Stripe\Coupon::create( $params );

		return $coupon->id;
	}

	private function create_stripe_subscription( $price ) {
		$params = array(
			'customer'               => $this->data['stripe_customer'],
			'default_payment_method' => $this->data['payment_method_id'],
			'items'                  => array( array( 'price' => $price->id ) ),
			'trial_period_days'      => $this->data['plan']['trial_period'] ?? 0,
			'payment_behavior'       => 'allow_incomplete',
			'payment_settings'       => array( 'save_default_payment_method' => 'on_subscription' ),
			'expand'                 => array( 'pending_setup_intent.payment_method', 'latest_invoice.payment_intent' ),
		);

		$coupon_id = $this->create_stripe_first_invoice_coupon();
		if ( $coupon_id ) {
			$params['discounts'] = array(
				array( 'coupon' => $coupon_id ),
			);
		}

		$stripe_subscription = \Stripe\Subscription::create( $params );

		$billing_cycles = masterstudy_lms_subscription_plan_billing_cycles_limit( $this->data['plan'] );
		if ( $billing_cycles > 0 ) {
			$interval        = $this->data['plan']['recurring_interval'] ?? 'day';
			$count_per_cycle = (int) ( $this->data['plan']['recurring_value'] ?? 1 );
			$total_periods   = $billing_cycles * max( 1, $count_per_cycle );

			$anchor    = (int) ( $stripe_subscription->current_period_start ?: $stripe_subscription->start_date ); // phpcs:ignore
			$cancel_at = $this->add_interval( $anchor, $interval, $total_periods );

			// Add trial if any (trial is before first paid period)
			$trial_days = (int) ( $this->data['plan']['trial_period'] ?? 0 );
			if ( $trial_days > 0 ) {
				$cancel_at += $trial_days * DAY_IN_SECONDS;
			}

			\Stripe\Subscription::update( $stripe_subscription->id, array( 'cancel_at' => $cancel_at ) );
		}

		return $stripe_subscription;
	}

	private function add_interval( int $anchor, string $interval, int $periods ): int {
		$map = array(
			'day'   => DAY_IN_SECONDS,
			'week'  => WEEK_IN_SECONDS,
			'month' => 30 * DAY_IN_SECONDS,   // use DateTime add(P1M) if you need true calendar months
			'year'  => 365 * DAY_IN_SECONDS,  // or DateTime add(P1Y)
		);
		$sec = $map[ $interval ] ?? DAY_IN_SECONDS;
		return $anchor + ( $periods * $sec );
	}


	/**
	 * Cancel a Stripe subscription
	 */
	public function cancel_gateway_subscription_now( $subscription_id ) {
		$subscription = $this->subscription_repository->get( $subscription_id );
		if ( ! $subscription || empty( $subscription['meta']['gateway_subscription_id'] ) ) {
			error_log( "Stripe: Cannot cancel subscription {$subscription_id} - missing gateway subscription ID" );
			return;
		}

		try {
			$stripe_subscription = \Stripe\Subscription::retrieve( $subscription['meta']['gateway_subscription_id'] );
			$stripe_subscription->cancel();

			do_action( 'masterstudy_lms_gateway_subscription_cancelled', self::$gateway_name, $subscription['user_id'], $subscription['id'] );
		} catch ( \Exception $e ) {
			error_log( "Stripe: Failed to cancel subscription {$subscription_id} - " . $e->getMessage() );
		}
	}

	public function cancel_gateway_subscription( $subscription_id ) {
		$subscription = $this->subscription_repository->get( $subscription_id );
		if ( ! $subscription || empty( $subscription['meta']['gateway_subscription_id'] ) ) {
			error_log( "Stripe: Cannot cancel subscription {$subscription_id} - missing gateway subscription ID" );
			return;
		}

		try {
			// Default: cancel at period end (non-destructive)
			\Stripe\Subscription::update(
				$subscription['meta']['gateway_subscription_id'],
				array( 'cancel_at_period_end' => true )
			);

			do_action( 'masterstudy_lms_gateway_subscription_cancelled', self::$gateway_name, $subscription['user_id'], $subscription['id'] );
		} catch ( \Exception $e ) {
			error_log( "Stripe: Failed to cancel subscription {$subscription_id} - " . $e->getMessage() );
		}
	}


	/**
	 * Update payment method for subscription
	 */
	public function update_payment_method( $gateway_subscription_id, $payment_method_id ): void {
		$stripe_subscription                         = \Stripe\Subscription::retrieve( $gateway_subscription_id );
		$stripe_subscription->default_payment_method = $payment_method_id;

		$stripe_subscription->save();

		$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $gateway_subscription_id );
		if ( ! $subscription ) {
			return;
		}

		$this->subscription_meta_repository->update(
			$subscription['id'],
			'payment_method_id',
			$payment_method_id
		);

		do_action( 'masterstudy_lms_subscription_payment_method_updated', $subscription['user_id'], $subscription['id'] );
	}

	/**
	 * Switch plan
	 */
	public function switch_plan( $gateway_subscription_id, $new_price_id ) {
		try {
			$stripe_subscription = \Stripe\Subscription::retrieve( $gateway_subscription_id );

			\Stripe\Subscription::update(
				$gateway_subscription_id,
				array(
					'items'              => array(
						array(
							'id'    => $stripe_subscription->items->data[0]->id,
							'price' => $new_price_id,
						),
					),
					'proration_behavior' => 'create_prorations',
				)
			);

			$subscription = $this->subscription_repository->get_by_gateway_subscription_id( $gateway_subscription_id );
			if ( ! $subscription ) {
				return;
			}

			$this->subscription_repository->update_column( $subscription['id'], 'plan_id', $new_price_id );
		} catch ( \Stripe\Exception\ApiErrorException $e ) {
			throw new \Exception( 'Failed to switch plan: ' . $e->getMessage() );
		}
	}
}
