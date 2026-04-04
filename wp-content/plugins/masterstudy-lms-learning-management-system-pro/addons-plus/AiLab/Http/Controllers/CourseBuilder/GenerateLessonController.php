<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\AiLessonSerializer;
use MasterStudy\Lms\Validation\Validator;

class GenerateLessonController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'prompt'       => 'required|string',
				'words_limit'  => 'required|integer',
				'tone'         => 'required|string',
				'images_limit' => 'required|integer',
				'language'     => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		try {
			$response = $this->ai->generate_lesson(
				$validated_data['prompt'],
				$validated_data
			);

			return new \WP_REST_Response(
				( new AiLessonSerializer() )->toArray( $response )
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
