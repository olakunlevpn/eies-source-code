<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\Gateways;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Payment\SubscriptionPayment;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

class PayPal extends SubscriptionPayment {
	protected $api_url;

	protected $paypal_mode;

	protected $client_id;

	protected $client_secret;

	protected $currency;

	public static $gateway_name = 'paypal';

	public function setup( array $config ): void {
		$this->client_id     = $config['client_id'] ?? '';
		$this->client_secret = $config['client_secret'] ?? '';
		$this->currency      = $this->get_default_currency_code() ?? 'USD';
		$this->paypal_mode   = $config['paypal_mode'] ?? 'sandbox';
		$this->api_url       = ( 'live' === $this->paypal_mode )
			? 'https://api-m.paypal.com'
			: 'https://api-m.sandbox.paypal.com';
	}

	public function check(): bool {
		return ! empty( $this->client_id ) && ! empty( $this->client_secret ) && ! empty( $this->currency );
	}

	/**
	 * Get PayPal access token
	 */
	protected function get_access_token(): string {
		$response = wp_remote_post(
			$this->api_url . '/v1/oauth2/token',
			array(
				'headers' => array(
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Authorization' => 'Basic ' . base64_encode( $this->client_id . ':' . $this->client_secret ),
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'grant_type' => 'client_credentials',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Failed to get PayPal access token' );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['access_token'] ) ) {
			throw new \Exception( 'Invalid PayPal access token response' );
		}

		return $body['access_token'];
	}

	/**
	 * Get request headers
	 */
	protected function get_request_headers(): array {
		return array(
			'Authorization' => 'Bearer ' . $this->get_access_token(),
			'Content-Type'  => 'application/json',
		);
	}

	/**
	 * Create a PayPal product
	 */
	public function create_product(): string {
		$plan_name = $this->data['plan']['name'] ?? esc_html__( 'Subscription Plan', 'masterstudy-lms-learning-management-system-pro' );
		$response  = wp_remote_post(
			$this->api_url . '/v1/catalogs/products',
			array(
				'headers' => $this->get_request_headers(),
				'body'    => wp_json_encode(
					array(
						'name'        => $plan_name,
						'description' => ! empty( $this->data['plan']['description'] )
							? $this->data['plan']['description']
							: $plan_name,
						'type'        => 'SERVICE',
						'category'    => 'SOFTWARE',
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Failed to create PayPal product' );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['id'] ) ) {
			throw new \Exception( 'Invalid PayPal product response' );
		}

		return $body['id'];
	}

	/**
	 * Create a PayPal billing plan
	 */
	/**
	 * Create a PayPal billing plan.
	 */
	public function create_billing_plan(): ?array {
		$product_id = $this->create_product();

		$unit_map = array(
			'day'    => 'DAY',
			'days'   => 'DAY',
			'week'   => 'WEEK',
			'weeks'  => 'WEEK',
			'month'  => 'MONTH',
			'months' => 'MONTH',
			'year'   => 'YEAR',
			'years'  => 'YEAR',
		);

		$raw_unit      = strtolower( $this->data['plan']['recurring_interval'] ?? 'month' );
		$interval_unit = $unit_map[ $raw_unit ] ?? 'MONTH';

		// Base price (with taxes).
		$price_value = (float) SubscriptionPlanRepository::get_actual_price_with_taxes( $this->data['plan'] );
		$price_str   = number_format( $price_value, 2, '.', '' );

		// Total number of PAID cycles (0 = infinite) from plan settings.
		$billing_cycles_total = ! empty( $this->data['plan']['billing_cycles'] ) && (int) $this->data['plan']['billing_cycles'] > 0
			? (int) $this->data['plan']['billing_cycles']
			: 0;

		// Coupon handling – same semantics as Stripe: only the first paid invoice is discounted.
		$has_coupon             = ! empty( $this->data['coupon_type'] ) && ! empty( $this->data['coupon_value'] );
		$discounted_price_value = $price_value;

		if ( $has_coupon ) {
			$coupon_value = (float) $this->data['coupon_value'];

			if ( 'percent' === $this->data['coupon_type'] ) {
				$discounted_price_value = $price_value * max( 0, ( 100 - $coupon_value ) ) / 100;
			} else {
				// Fixed amount.
				$discounted_price_value = $price_value - $coupon_value;
			}

			if ( $discounted_price_value < 0 ) {
				$discounted_price_value = 0;
			}
		}

		$discounted_price_str = number_format( $discounted_price_value, 2, '.', '' );

		// Base REGULAR cycle (will be adjusted below).
		$regular_cycle = array(
			'frequency'      => array(
				'interval_unit'  => $interval_unit,
				'interval_count' => (int) ( $this->data['plan']['recurring_value'] ?? 1 ),
			),
			'tenure_type'    => 'REGULAR',
			'sequence'       => 1, // will be changed below.
			'total_cycles'   => $billing_cycles_total,
			'pricing_scheme' => array(
				'fixed_price' => array(
					'value'         => $price_str,
					'currency_code' => $this->get_default_currency_code(),
				),
			),
		);

		$billing_cycles = array();
		$sequence       = 1;

		// 1) Free TRIAL days from plan settings (if any).
		$has_trial_days = ! empty( $this->data['plan']['trial_period'] ) && (int) $this->data['plan']['trial_period'] > 0;

		if ( $has_trial_days ) {
			$trial_days  = (int) $this->data['plan']['trial_period'];
			$trial_cycle = array(
				'frequency'      => array(
					'interval_unit'  => 'DAY',
					'interval_count' => $trial_days,
				),
				'tenure_type'    => 'TRIAL',
				'sequence'       => $sequence,
				'total_cycles'   => 1,
				'pricing_scheme' => array(
					'fixed_price' => array(
						'value'         => '0',
						'currency_code' => $this->get_default_currency_code(),
					),
				),
			);

			$billing_cycles[] = $trial_cycle;
			$sequence++;
		}

		// 2) Discounted first paid invoice -> discounted TRIAL cycle.
		if ( $has_coupon ) {
			$discount_cycle = array(
				'frequency'      => array(
					'interval_unit'  => $interval_unit,
					'interval_count' => (int) ( $this->data['plan']['recurring_value'] ?? 1 ),
				),
				'tenure_type'    => 'TRIAL', // discounted trial – PayPal allows 2 TRIAL tenures.
				'sequence'       => $sequence,
				'total_cycles'   => 1,
				'pricing_scheme' => array(
					'fixed_price' => array(
						'value'         => $discounted_price_str,
						'currency_code' => $this->currency,
					),
				),
			);

			$billing_cycles[] = $discount_cycle;
			$sequence++;

			// One of the paid cycles is now consumed by the discounted trial.
			if ( $billing_cycles_total > 0 ) {
				$billing_cycles_total--;
			}
		}

		// 3) Regular recurring cycle(s) (full price).
		$regular_cycle['sequence']     = $sequence;
		$regular_cycle['total_cycles'] = $billing_cycles_total;
		$billing_cycles[]              = $regular_cycle;

		$plan_name         = $this->data['plan']['name'] ?? esc_html__( 'Subscription Plan', 'masterstudy-lms-learning-management-system-pro' );
		$billing_plan_data = array(
			'product_id'          => $product_id,
			'name'                => $plan_name,
			'description'         => ! empty( $this->data['plan']['description'] )
				? $this->data['plan']['description']
				: $plan_name,
			'status'              => 'ACTIVE',
			'billing_cycles'      => $billing_cycles,
			'payment_preferences' => array(
				'auto_bill_outstanding'     => true,
				'setup_fee'                 => array(
					'value'         => '0',
					'currency_code' => $this->get_default_currency_code(),
				),
				'setup_fee_failure_action'  => 'CONTINUE',
				'payment_failure_threshold' => 3,
			),
		);

		$response = wp_remote_post(
			$this->api_url . '/v1/billing/plans',
			array(
				'headers' => $this->get_request_headers(),
				'body'    => wp_json_encode( $billing_plan_data ),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Failed to create PayPal billing plan' );
		}

		$billing_plan = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $billing_plan['id'] ) ) {
			throw new \Exception( 'Invalid PayPal billing plan response' );
		}

		return $billing_plan;
	}

	/**
	 * Subscribe to a PayPal plan
	 */
	public function subscribe() {
		try {
			// Check if this is a free plan
			if ( $this->is_free_plan() ) {
				return $this->handle_free_plan_subscription();
			}

			$this->data['currency'] = $this->get_default_currency_code();

			// Create billing plan
			$billing_plan = $this->create_billing_plan();
			$user         = get_userdata( $this->data['user_id'] );

			// Create subscription
			$response = wp_remote_post(
				$this->api_url . '/v1/billing/subscriptions',
				array(
					'headers' => $this->get_request_headers(),
					'body'    => wp_json_encode(
						array(
							'plan_id'             => $billing_plan['id'],
							'custom_id'           => (string) ( $this->data['user_id'] ?? '' ),
							'subscriber'          => array(
								'name'          => array(
									'given_name' => $user->first_name,
									'surname'    => $user->last_name,
								),
								'email_address' => $user->user_email,
							),
							'application_context' => array(
								'brand_name'          => get_bloginfo( 'name' ),
								'shipping_preference' => 'NO_SHIPPING',
								'user_action'         => 'SUBSCRIBE_NOW',
								'payment_method'      => array(
									'payer_selected'  => 'PAYPAL',
									'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
								),
								'return_url'          => add_query_arg( array( 'payment_verification' => 'true' ), $this->data['thankyou_url'] ),
								'cancel_url'          => ms_plugin_user_account_url( 'subscriptions' ),
							),
						)
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				throw new \Exception( 'Failed to revise PayPal subscription plan' );
			}

			$response = json_decode( wp_remote_retrieve_body( $response ), true );

			$this->data['gateway_subscription_id']     = $response['id'];
			$this->data['gateway_subscription_status'] = $response['status'];
			$this->data['gateway_invoice_id']          = null;

			// Generate PayPal payment URL
			$approve_url = '';
			if ( ! empty( $response['links'] ) && is_array( $response['links'] ) ) {
				foreach ( $response['links'] as $link ) {
					if ( isset( $link['rel'] ) && 'approve' === $link['rel'] ) {
						$approve_url = $link['href'];
						break;
					}
				}
			}

			$this->data['redirect_url'] = $approve_url;

			// Create subscription in the system
			$this->create_subscription();

			return true;
		} catch ( \Exception $e ) {
			$this->data['error'] = $e->getMessage();

			return false;
		}
	}

	/**
	 * Cancel a PayPal subscription
	 */
	public function cancel_gateway_subscription( $subscription_id ) {
		$subscription = $this->subscription_repository->get( $subscription_id );
		if ( ! $subscription ) {
			return;
		}

		// Get gateway subscription ID from meta table
		$gateway_subscription_id = $this->subscription_meta_repository->get( $subscription_id, 'gateway_subscription_id' );
		if ( empty( $gateway_subscription_id ) ) {
			return;
		}

		// Use the cancel endpoint with POST method (no body needed)
		$response = wp_remote_post(
			$this->api_url . '/v1/billing/subscriptions/' . $gateway_subscription_id . '/cancel',
			array(
				'headers' => $this->get_request_headers(),
				'body'    => wp_json_encode( array( 'reason' => 'User requested cancellation' ) ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code >= 200 && $response_code < 300 ) {
			do_action( 'masterstudy_lms_gateway_subscription_cancelled', self::$gateway_name, $subscription['user_id'], $subscription['id'] );
		}
	}

	/**
	 * Switch a PayPal subscription plan
	 */
	public function switch_plan( $subscription_id, $new_plan_id ) {
		$response = wp_remote_post(
			$this->api_url . '/v1/billing/subscriptions/' . $subscription_id . '/revise',
			array(
				'headers' => $this->get_request_headers(),
				'body'    => wp_json_encode( array( 'plan_id' => $new_plan_id ) ),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Failed to switch PayPal subscription plan' );
		}
	}

}
