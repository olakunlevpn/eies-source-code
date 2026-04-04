<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Ecommerce\Ecommerce;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\BulkSubscriptionAction;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateSubscriptionsController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action'      => 'required|string|contains_list,' . implode( ';', BulkSubscriptionAction::cases() ),
				'memberships' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params = $validator->get_validated();

		$repository = new SubscriptionRepository();

		if ( BulkSubscriptionAction::DELETE === $params['action'] ) {
			$error = $repository->masterstudy_lms_bulk_delete_subscriptions( $params );
		} else {
			return WpResponseFactory::error( 'Only delete action is supported' );
		}

		if ( is_array( $error ) ) {
			$id = $error['id'];

			return WpResponseFactory::error( "Unable to perform action on subscription $id" );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Bulk action completed successfully',
			)
		);
	}

}
