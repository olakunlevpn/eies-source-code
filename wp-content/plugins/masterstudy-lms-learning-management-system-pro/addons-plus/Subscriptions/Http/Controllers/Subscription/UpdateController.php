<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Validation\Validator;

class UpdateController {
	public function __invoke( int $subscription_id, \WP_REST_Request $request ) {
		$repository = new SubscriptionRepository();

		if ( ! $repository->find( $subscription_id ) ) {
			return WpResponseFactory::not_found();
		}

		$validator = new Validator(
			$request->get_params(),
			array(
				'note' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		$result = $repository->update_column(
			$subscription_id,
			'note',
			$validated_data['note'],
		);

		return new \WP_REST_Response(
			array(
				'result' => $result,
			)
		);
	}
}
