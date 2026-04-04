<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Routing\Swagger\Routes\Subscription;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Get extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'subscription' => array(
				'type'       => 'object',
				'properties' => array(
					'id'                => array(
						'type'        => 'string',
						'description' => 'Subscription ID with # prefix',
					),
					'type'              => array(
						'type'        => 'string',
						'description' => 'Subscription type (course/bundle/category)',
						'enum'        => array( 'course', 'bundle', 'category' ),
					),
					'renew'             => array(
						'type'        => 'string',
						'description' => 'Renewal information (e.g., "$29.99/month")',
					),
					'payment'           => array(
						'type'        => 'string',
						'description' => 'Payment type',
						'enum'        => array( 'recurring', 'one-time' ),
					),
					'coupon'            => array(
						'type'        => 'string',
						'description' => 'Applied coupon code',
						'nullable'    => true,
					),
					'trial_end_date'    => array(
						'type'        => 'string',
						'format'      => 'date-time',
						'description' => 'Trial end date',
						'nullable'    => true,
					),
					'next_payment_date' => array(
						'type'        => 'string',
						'format'      => 'date-time',
						'description' => 'Next payment date',
						'nullable'    => true,
					),
					'status'            => array(
						'type'        => 'string',
						'description' => 'Subscription status',
						'enum'        => array( 'active', 'cancelled', 'expired', 'trial', 'pending' ),
					),
					'plan_name'         => array(
						'type'        => 'string',
						'description' => 'Subscription plan name',
					),
				),
			),
			'student'      => array(
				'type'       => 'object',
				'properties' => array(
					'name'         => array(
						'type'        => 'string',
						'description' => 'Student full name',
					),
					'email'        => array(
						'type'        => 'string',
						'format'      => 'email',
						'description' => 'Student email address',
					),
					'country'      => array(
						'type'        => 'string',
						'description' => 'Student country',
					),
					'postcode'     => array(
						'type'        => 'string',
						'description' => 'Student postal code',
					),
					'state'        => array(
						'type'        => 'string',
						'description' => 'Student state/province',
					),
					'city'         => array(
						'type'        => 'string',
						'description' => 'Student city',
					),
					'company'      => array(
						'type'        => 'string',
						'description' => 'Student company name',
					),
					'phone_number' => array(
						'type'        => 'string',
						'description' => 'Student phone number',
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Get subscription details';
	}

	public function get_description(): string {
		return 'Get detailed information about a specific subscription including payment history';
	}
}
