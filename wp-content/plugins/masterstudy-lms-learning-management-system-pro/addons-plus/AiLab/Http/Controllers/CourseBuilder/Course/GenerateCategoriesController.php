<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder\Course;

use MasterStudy\Lms\Http\Serializers\CourseCategorySerializer;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Response;

class GenerateCategoriesController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'prompt' => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		try {
			return new WP_REST_Response(
				array(
					'categories' => ( new CourseCategorySerializer() )->collectionToArray(
						$this->ai->select_course_categories( $validated_data['prompt'] )
					),
				)
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
