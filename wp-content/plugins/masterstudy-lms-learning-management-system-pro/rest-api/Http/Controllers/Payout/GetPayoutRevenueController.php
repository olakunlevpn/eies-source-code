<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Payout;

use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Payout\PayoutOrderSerializer;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Payout\PayoutRevenueSerializer;
use MasterStudy\Lms\Pro\RestApi\Services\PayoutService;
use stmLms\Classes\Models\StmStatistics;
use WP_REST_Request;
use WP_REST_Response;
use MasterStudy\Lms\Validation\Validator;

final class GetPayoutRevenueController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'date_from' => 'nullable|string',
				'date_to'   => 'nullable|string',
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

		$data = ( new PayoutService() )->get_payout_revenue( $validator->get_validated() );

		return new WP_REST_Response(
			( new PayoutRevenueSerializer() )->toArray( $data )
		);
	}
}
