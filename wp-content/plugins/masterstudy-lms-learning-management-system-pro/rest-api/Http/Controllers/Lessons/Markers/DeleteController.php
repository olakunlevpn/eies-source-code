<?php
namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\Markers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\LessonMarkersRepository;
use MasterStudy\Lms\Repositories\LessonRepository;

class DeleteController {

	/**
	 * Remove a marker for a video lesson using lesson_id and marker_id.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function __invoke( $lesson_id, $marker_id ): \WP_REST_Response {
		if ( ! ( new LessonRepository() )->get( $lesson_id ) ) {
			return WpResponseFactory::validation_failed( array( esc_html__( 'Lesson not found.', 'masterstudy-lms-learning-management-system-pro' ) ) );
		}

		$marker_question_id = ( new LessonMarkersRepository() )->delete( $lesson_id, $marker_id );

		if ( ! empty( $marker_question_id ) ) {
			return new \WP_REST_Response(
				array(
					'message' => esc_html__( 'Marker deleted successfully', 'masterstudy-lms-learning-management-system-pro' ),
				)
			);
		}

		return WpResponseFactory::error(
			esc_html__( 'Failed to delete marker', 'masterstudy-lms-learning-management-system-pro' )
		);
	}
}
