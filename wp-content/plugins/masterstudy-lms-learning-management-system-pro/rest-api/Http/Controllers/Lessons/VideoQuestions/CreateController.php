<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\VideoQuestions;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\LessonMarkersRepository;
use MasterStudy\Lms\Repositories\LessonRepository;
use MasterStudy\Lms\Validation\Validator;

class CreateController {

	/**
	 * Add markers to a video lesson
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function __invoke( $lesson_id, \WP_REST_Request $request ): \WP_REST_Response {
		if ( ! ( new LessonRepository() )->get( $lesson_id ) ) {
			return WpResponseFactory::validation_failed( array( esc_html__( 'Lesson not found.', 'masterstudy-lms-learning-management-system-pro' ) ) );
		}

		$rules = array(
			'time'    => 'required|integer',
			'rewatch' => 'integer',
			'caption' => 'string',
			'content' => 'string',
			'type'    => 'string',
			'answers' => 'array',
		);

		$validator = new Validator( $request->get_json_params(), $rules );
		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$new_marker = $validator->get_validated();
		$repo       = new LessonMarkersRepository();

		if ( $repo->marker_time_exists( $lesson_id, (int) $new_marker['time'] ) ) {
			return WpResponseFactory::validation_failed(
				array(
					esc_html__( 'The marker already exists at this point.', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);
		}

		if ( empty( $new_marker['type'] ) ) {
			$marker_question_id = $repo->create( $lesson_id, $new_marker['time'], $new_marker['caption'] );
		} else {
			$marker_question_id = $repo->create_question( $lesson_id, $new_marker );
		}

		if ( ! empty( $marker_question_id ) ) {
			return new \WP_REST_Response(
				array(
					'message'   => esc_html__( 'Markers added successfully', 'masterstudy-lms-learning-management-system-pro' ),
					'marker_id' => $marker_question_id,
				)
			);
		}

		return WpResponseFactory::error(
			esc_html__( 'Failed to add markers', 'masterstudy-lms-learning-management-system-pro' )
		);
	}
}
