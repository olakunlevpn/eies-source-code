<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Http\Controllers\Frontend;

use WP_REST_Request;
use MasterStudy\Lms\Validation\Validator;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentTeacherRepository;

class GetAssignmentsController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			array(
				's'         => $request->get_param( 'search' ) ?? '',
				'status'    => $request->get_param( 'status' ) ?? '',
				'page'      => intval( $request->get_param( 'page' ) ?? 1 ),
				'per_page'  => intval( $request->get_param( 'per_page' ) ?? 10 ),
				'course_id' => $request->get_param( 'course_id' ),
			),
			array(
				's'         => 'nullable|string',
				'status'    => 'nullable|string',
				'page'      => 'nullable|integer',
				'per_page'  => 'nullable|integer',
				'course_id' => 'nullable|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		return new \WP_REST_Response(
			AssignmentTeacherRepository::get_assignments( $validator->get_validated() )
		);
	}
}
