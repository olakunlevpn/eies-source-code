<?php

namespace MasterStudy\Lms\Pro\addons\gradebook\Http\Controllers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\gradebook\Gradebook;
use MasterStudy\Lms\Pro\addons\gradebook\Http\Serializers\CourseStudentsSerializer;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

final class GetCourseStudentsController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'start'     => 'required|integer',
				'length'    => 'required|integer',
				'order'     => 'array',
				'columns'   => 'array',
				'course_id' => 'required|integer',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();
		$per_page       = $validated_data['length'] ?? 10;
		$page           = ( $validated_data['start'] ?? 0 ) / $per_page + 1;

		$data            = Gradebook::get_course_students( $validated_data['course_id'], $page, $per_page, true );
		$course_students = array_map( fn( $_data ) => array_merge( $_data, array( 'curriculum' => $data['course_curriculum'] ) ), $data['course_students'] );

		return new \WP_REST_Response(
			array(
				'data'            => ( new CourseStudentsSerializer() )->collectionToArray( $course_students ),
				'recordsTotal'    => $data['total'],
				'recordsFiltered' => $data['total'],
			)
		);
	}
}
