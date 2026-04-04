<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Payout;

use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Payout\PayoutOrderSerializer;
use stmLms\Classes\Models\StmStatistics;
use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Validation\Validator;

final class GetPayoutOrdersController {
	private array $allowed_orderby = array(
		'date_created' => 'MAX(_order.post_date)',
	);

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'start'     => 'required|integer',
				'length'    => 'required|integer',
				'date_from' => 'nullable|string',
				'date_to'   => 'nullable|string',
				'status'    => 'nullable|string|contains_list,completed;pending;canceled',
				'order'     => 'array',
				'course_id' => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return new WP_REST_Response(
				array(
					'status' => 'validation_failed',
					'errors' => $validator->get_errors_array(),
				),
				400
			);
		}

		$params              = $validator->get_validated();
		$limit               = $params['length'];
		$offset              = $params['start'];
		$order               = $params['order'];
		$params['author_id'] = get_current_user_id();

		if (
		! empty( $order[0]['name'] )
		&& ! empty( $this->allowed_orderby[ $order[0]['name'] ] )
		&& ! empty( $order[0]['dir'] )
		&& in_array( $order[0]['dir'], array( 'asc', 'desc' ), true )
		) {
			$params['orderby'] = $this->allowed_orderby[ $order[0]['name'] ];
			$params['order']   = strtoupper( $order[0]['dir'] );
		}

		$orders_data = StmStatistics::get_user_order_items( $offset, $limit, $params );

		return new WP_REST_Response(
			array(
				'data'            => ( new PayoutOrderSerializer() )->collectionToArray( $orders_data['items'] ),
				'recordsTotal'    => intval( $orders_data['total'] ),
				'recordsFiltered' => intval( $orders_data['total'] ),
				'formatted_price' => $orders_data['formatted_price'],
			)
		);
	}
}
