<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\VideoQuestions;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\LessonRepository;
use MasterStudy\Lms\Validation\Validator;

class UpdateQuestionsLockController {

	/**
	 * Update video questions lock status
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
			'is_locked' => 'required|boolean',
		);

		$validator = new Validator( $request->get_json_params(), $rules );
		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated = $validator->get_validated();
		$is_locked = $validated['is_locked'];

		update_post_meta( $lesson_id, 'video_marker_questions_locked', $is_locked );

		return new \WP_REST_Response(
			array(
				'message' => $is_locked
					? esc_html__( 'Video questions locked successfully', 'masterstudy-lms-learning-management-system-pro' )
					: esc_html__( 'Video questions unlocked successfully', 'masterstudy-lms-learning-management-system-pro' ),
			)
		);
	}
}
