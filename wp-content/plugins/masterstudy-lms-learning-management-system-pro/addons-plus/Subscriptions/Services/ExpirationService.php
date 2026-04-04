<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Services;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

if (
	! class_exists( 'STM_LMS_Helpers' )
	|| ! method_exists( 'STM_LMS_Helpers', 'is_pro_plus' )
	|| ! \STM_LMS_Helpers::is_pro_plus()
	|| ! function_exists( 'is_ms_lms_addon_enabled' )
	|| ! is_ms_lms_addon_enabled( 'subscriptions' )
) {
	return;
}

/**
 * Handle subscription expiration checks and notifications
 */
class ExpirationService {

	private $subscription_repository;

	public function __construct() {
		$this->subscription_repository = new SubscriptionRepository();
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks(): void {
		// Schedule cron job on activation
		add_action( 'init', array( $this, 'schedule_expiration_check' ) );

		// Handle the cron job
		add_action( 'ms_lms_check_expired_subscriptions', array( $this, 'check_expired_subscriptions' ) );

		// Schedule expiration notifications
		add_action( 'ms_lms_check_expiring_subscriptions', array( $this, 'check_expiring_subscriptions' ) );
	}

	/**
	 * Schedule the expiration check cron job
	 */
	public function schedule_expiration_check(): void {
		if ( ! wp_next_scheduled( 'ms_lms_check_expired_subscriptions' ) ) {
			wp_schedule_event( time(), 'hourly', 'ms_lms_check_expired_subscriptions' );
		}

		if ( ! wp_next_scheduled( 'ms_lms_check_expiring_subscriptions' ) ) {
			wp_schedule_event( time(), 'daily', 'ms_lms_check_expiring_subscriptions' );
		}
	}

	/**
	 * Check for subscriptions that should be expired
	 */
	public function check_expired_subscriptions(): void {
		$expired_subscriptions = $this->subscription_repository->get_subscriptions_to_expire();

		foreach ( $expired_subscriptions as $subscription ) {
			// Update status to expired
			$this->subscription_repository->update_status( $subscription['id'], 'expired' );
			// Trigger expiration action for email notifications
			do_action( 'masterstudy_lms_subscription_expired', $subscription['user_id'], $subscription['id'] );
		}

	}

	/**
	 * Check for subscriptions that expire soon and send notifications
	 */
	public function check_expiring_subscriptions(): void {
		$expiring_subscriptions = $this->subscription_repository->get_subscriptions_expiring_soon();

		foreach ( $expiring_subscriptions as $subscription ) {
			// Trigger expires soon action for email notifications
			do_action( 'masterstudy_lms_subscription_expires_soon', $subscription['user_id'], $subscription['id'] );
		}

	}

	/**
	 * Clean up cron jobs on deactivation
	 */
	public static function cleanup_cron_jobs(): void {
		wp_clear_scheduled_hook( 'ms_lms_check_expired_subscriptions' );
		wp_clear_scheduled_hook( 'ms_lms_check_expiring_subscriptions' );
	}
}

// Initialize the service
new ExpirationService();

// Clean up on deactivation
register_deactivation_hook(
	__FILE__,
	array(
		'MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Services\ExpirationService',
		'cleanup_cron_jobs',
	)
);
