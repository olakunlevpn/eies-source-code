<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers\InstructorSubscriptionsSerializer;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use WP_REST_Response;

final class GetInstructorSubscriptionsController extends Controller {

	public function __invoke( \WP_REST_Request $request ) {
		$validation = $this->validate_datatable( $request );

		if ( $validation instanceof WP_REST_Response ) {
			return $validation;
		}

		$validated_data    = $this->get_validated_data();
		$date_from         = $this->get_date_from();
		$date_to           = $this->get_date_to();
		$search            = $validated_data['search']['value'] ?? null;
		$per_page          = $validated_data['length'] ?? 10;
		$page              = ( $validated_data['start'] ?? 0 ) / $per_page + 1;
		$subscription_type = $validated_data['subscription_type'] ?? null;
		$order             = $validated_data['order'][0] ?? null;
		$filters           = array();

		if ( $search ) {
			$filters['search'] = $search;
		}

		if ( $subscription_type ) {
			$filters['subscription_type'] = $subscription_type;
		}

		if ( $order ) {
			$column_index = $order['column'] ?? null;
			$dir          = $order['dir'] ?? 'asc';
			$column_name  = $validated_data['columns'][ $column_index ]['data'] ?? null;

			if ( $column_name ) {
				$filters['sort'] = ( 'desc' === strtolower( $dir ) ? '-' : '' ) . $column_name;
			}
		}

		$subscriptions_result = ( new SubscriptionRepository() )->get_all(
			$filters,
			$date_from,
			$date_to,
			$page,
			$per_page
		);

		return new WP_REST_Response(
			array(
				'recordsTotal'    => intval( $subscriptions_result['total'] ),
				'recordsFiltered' => intval( $subscriptions_result['total'] ),
				'data'            => ( new InstructorSubscriptionsSerializer() )->collectionToArray( $subscriptions_result['data'] ),
			)
		);
	}
}
