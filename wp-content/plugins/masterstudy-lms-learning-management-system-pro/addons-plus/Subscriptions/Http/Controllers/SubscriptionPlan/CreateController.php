<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\SubscriptionPlan;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\ReccuringInterval;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanItemRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;
use MasterStudy\Lms\Validation\Validator;

class CreateController {

	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'type'               => 'required|string|contains_list,' . implode( ';', SubscriptionPlanType::cases() ),
				'name'               => 'required|string',
				'description'        => 'nullable|string',
				'recurring_value'    => 'nullable|integer',
				'recurring_interval' => 'nullable|string|contains_list,' . implode( ';', ReccuringInterval::cases() ),
				'billing_cycles'     => 'nullable|integer',
				'price'              => 'required|numeric',
				'sale_price'         => 'nullable|numeric',
				'sale_price_from'    => 'nullable|date',
				'sale_price_to'      => 'nullable|date',
				'plan_features'      => 'nullable|array',
				'enrollment_fee'     => 'nullable|numeric',
				'trial_period'       => 'nullable|integer',
				'is_featured'        => 'nullable|boolean',
				'featured_text'      => 'nullable|string',
				'is_certified'       => 'nullable|boolean',
				'is_enabled'         => 'nullable|boolean',
				'plan_order'         => 'nullable|integer',
				'items'              => 'nullable|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		if ( SubscriptionPlanRepository::is_instructor_not_allowed( $validated_data['type'] ) ) {
			return WpResponseFactory::error(
				esc_html__( 'Forbidden', 'masterstudy-lms-learning-management-system-pro' )
			);
		}

		$gate = apply_filters(
			'masterstudy_lms_before_create_subscription_plan',
			true,
			$validated_data
		);

		if ( true !== $gate ) {
			if ( is_wp_error( $gate ) ) {
				return WpResponseFactory::error(
					esc_html( $gate->get_error_message() )
				);
			}
			return WpResponseFactory::ok_with_data( $gate );
		}

		// Create subscription plan
		$plan_id = ( new SubscriptionPlanRepository() )->create( $validated_data );

		if ( ! $plan_id ) {
			return WpResponseFactory::error(
				esc_html__( 'Failed to create subscription plan', 'masterstudy-lms-learning-management-system-pro' )
			);
		}

		// Create subscription plan items for category and course types
		if ( ! empty( $validated_data['items'] ) && in_array( $validated_data['type'], array( SubscriptionPlanType::CATEGORY, SubscriptionPlanType::COURSE ), true ) ) {
			$items_created = ( new SubscriptionPlanItemRepository() )->create( $plan_id, $validated_data['items'] );

			if ( ! $items_created ) {
				return WpResponseFactory::error(
					esc_html__( 'Failed to create subscription plan items', 'masterstudy-lms-learning-management-system-pro' )
				);
			}
		}

		return WpResponseFactory::created(
			array(
				'plan_id' => $plan_id,
			)
		);
	}
}
