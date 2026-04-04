<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;

class GenerateLessonDurationController extends Controller {
	public function __invoke( int $lesson_id ) {
		$lesson = get_post( $lesson_id );

		if ( ! $lesson ) {
			return WpResponseFactory::not_found();
		}

		$words_count  = 0;
		$images_count = 0;

		if ( ! empty( $lesson->post_content ) ) {
			$words_count  = (int) str_word_count( preg_replace( '/<img[^>]*>/i', '', $lesson->post_content ) );
			$images_count = (int) preg_match_all( '/<img[^>]+>/', $lesson->post_content );
		}

		try {
			return new \WP_REST_Response(
				array(
					'duration' => $this->ai->generate_lesson_duration( $words_count, $images_count ),
				)
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
