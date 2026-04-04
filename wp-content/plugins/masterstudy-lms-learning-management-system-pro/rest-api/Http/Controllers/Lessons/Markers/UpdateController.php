<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\Markers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\LessonMarkersRepository;
use MasterStudy\Lms\Validation\Validator;

class UpdateController {

	/**
	 * Add markers to a video lesson
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function __invoke( $marker_id, \WP_REST_Request $request ): \WP_REST_Response {
		if ( empty( $marker_id ) ) {
			return WpResponseFactory::validation_failed( array( esc_html__( 'Marker not found.', 'masterstudy-lms-learning-management-system-pro' ) ) );
		}

		$rules = array(
			'time'      => 'required|integer',
			'caption'   => 'required|string',
			'lesson_id' => 'required|integer',
			'content'   => 'string',
			'type'      => 'string',
			'answers'   => 'array',
			'rewatch'   => 'string',
		);

		$validator = new Validator( $request->get_json_params(), $rules );
		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$new_marker = $validator->get_validated();
		$repo       = new LessonMarkersRepository();

		if ( $repo->marker_id_exists( $marker_id, $new_marker['time'], $new_marker['lesson_id'] ) ) {
			return WpResponseFactory::validation_failed(
				array(
					esc_html__( 'This marker time code already exists', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);
		}

		$marker_question_id = $repo->update( $marker_id, $new_marker );

		if ( ! empty( $marker_question_id ) ) {
			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Marker updated successfully', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);
		}

		return WpResponseFactory::error(
			esc_html__( 'Failed to update marker', 'masterstudy-lms-learning-management-system-pro' )
		);
	}
}
