<?php
namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\Markers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\LessonMarkersRepository;
use MasterStudy\Lms\Repositories\LessonRepository;

class GetController {

	/**
	 * Get markers for a specific lesson.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function __invoke( $lesson_id, \WP_REST_Request $request ): \WP_REST_Response {
		if ( ! ( new LessonRepository() )->get( $lesson_id ) ) {
			return WpResponseFactory::validation_failed( array( esc_html__( 'Lesson not found.', 'masterstudy-lms-learning-management-system-pro' ) ) );
		}

		$markers_repo = new LessonMarkersRepository();

		return new \WP_REST_Response(
			array_merge(
				array(
					'markers' => $markers_repo->get_markers( $lesson_id ) ?? array(),
				),
				$markers_repo->get_video_lesson_utils( $lesson_id ) ?? array()
			)
		);
	}
}
