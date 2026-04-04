<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers\SubscriptionSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use WP_REST_Response;

final class ListController extends Controller {
	/**
	 * GET /subscriptions
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
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
		$is_admin          = $validated_data['is_admin'] ?? null;
		$order             = array_key_exists( 'order', $validated_data ) ? $validated_data['order'] : null;
		$columns           = $validated_data['columns'] ?? array();
		$filters           = array();

		if ( $search ) {
			$filters['search'] = $search;
		}

		if ( $subscription_type ) {
			$filters['subscription_type'] = $subscription_type;
		}

		if ( $is_admin ) {
			$filters['is_admin'] = $is_admin;
		}

		if ( ! empty( $columns ) && ! empty( $order ) ) {
			$order  = reset( $order );
			$column = $order['column'] ?? 0;
			$dir    = $order['dir'] ?? 'asc';

			if ( ! empty( $columns[ $column ]['data'] ) && 'number' !== $columns[ $column ]['data'] ) {
				$filters['sort'] = $columns[ $column ]['data'] . ' ' . $dir;
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
				'data'            => ( new SubscriptionSerializer() )->collectionToArray( $subscriptions_result['data'] ),
				'recordsTotal'    => intval( $subscriptions_result['total'] ),
				'recordsFiltered' => intval( $subscriptions_result['total'] ),
			)
		);
	}
}
