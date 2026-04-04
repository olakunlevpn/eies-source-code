<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\QuestionSerializer;
use MasterStudy\Lms\Validation\Validator;

final class GenerateQuestionsController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'prompt'          => 'required|string',
				'questions_count' => 'required|integer',
				'answers_limit'   => 'required|integer',
				'images_style'    => 'required|string',
				'questions_types' => 'required|array',
				'language'        => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$data = $validator->get_validated();

		try {
			$questions = $this->ai->generate_questions(
				$data['prompt'],
				$data
			);

			return new \WP_REST_Response(
				array(
					'questions' => ( new QuestionSerializer() )->collectionToArray( $questions ),
				)
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
