<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder\Course;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\Course\CurriculumSerializer;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Response;

class GenerateCurriculumController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'prompt'             => 'required|string',
				'language'           => 'required|string',
				'sections_count'     => 'required|integer',
				'materials_count'    => 'required|integer',
				'quizzes_count'      => 'nullable|integer',
				'create_assignments' => 'nullable|boolean',
				'lesson_types'       => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		try {
			$curriculum = $this->ai->generate_course_curriculum( $validated_data['prompt'], $validated_data );

			return new WP_REST_Response(
				( new CurriculumSerializer() )->collectionToArray( $curriculum )
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
