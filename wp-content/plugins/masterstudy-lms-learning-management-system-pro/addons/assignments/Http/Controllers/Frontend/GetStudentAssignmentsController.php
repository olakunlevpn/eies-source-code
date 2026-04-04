<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Http\Controllers\Frontend;

use WP_REST_Request;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;

class GetStudentAssignmentsController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				's'             => 'nullable|string',
				'status'        => 'nullable|string',
				'page'          => 'nullable|integer',
				'per_page'      => 'nullable|integer',
				'sortby'        => 'nullable|string',
				'sort_order'    => 'nullable|string',
				'assignment_id' => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		return new \WP_REST_Response(
			( new AssignmentStudentRepository() )->get_assignments( $validator->get_validated() )
		);
	}
}
