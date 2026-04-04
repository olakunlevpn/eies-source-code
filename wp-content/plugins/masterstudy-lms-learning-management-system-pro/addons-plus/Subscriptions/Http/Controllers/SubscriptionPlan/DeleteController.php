<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanItemRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

class DeleteController {
	public function __invoke( int $plan_id ) {
		$subscription_plan_repository = new SubscriptionPlanRepository();

		if ( ! $subscription_plan_repository->find( $plan_id ) ) {
			return WpResponseFactory::not_found();
		}

		$subscription_repository = new SubscriptionRepository();
		$active_count            = $subscription_repository->get_active_subscriptions_count_by_plan( $plan_id );

		$gate = apply_filters(
			'masterstudy_lms_before_delete_subscription_plan',
			true,
			$plan_id,
			$active_count
		);

		if ( true !== $gate ) {
			if ( is_wp_error( $gate ) ) {
				return WpResponseFactory::error(
					esc_html( $gate->get_error_message() )
				);
			}
			return WpResponseFactory::ok_with_data( $gate );
		}

		if ( $subscription_repository->is_plan_being_used( $plan_id ) && $active_count > 0 ) {
			$plan_name = $subscription_plan_repository->get_plan_name( $plan_id );

			$message = sprintf(
				// translators: %1$d - number of active subscriptions, %2$s - plan name
				esc_html__( 'Cannot delete this subscription plan. It is currently being used by %1$d active student(s). Please cancel or wait for all subscriptions to expire before deleting plan %2$s.', 'masterstudy-lms-learning-management-system-pro' ),
				$active_count,
				"\"$plan_name\""
			);

			return new \WP_REST_Response(
				array(
					'error'   => true,
					'message' => $message,
					'code'    => 'plan_in_use',
					'data'    => array(
						'plan_id'       => $plan_id,
						'active_count'  => $active_count,
						'is_being_used' => true,
					),
				),
				409
			);
		}

		try {
			( new SubscriptionPlanItemRepository() )->delete_by_plan_id( $plan_id );

			$subscription_plan_repository->delete( $plan_id );
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}

		return WpResponseFactory::ok();
	}
}
