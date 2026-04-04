<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\FaqSerializer;
use MasterStudy\Lms\Validation\Validator;

class GenerateFaqController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'prompt'      => 'required|string',
				'words_limit' => 'required|integer',
				'count'       => 'required|integer',
				'tone'        => 'required|string',
				'language'    => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		try {
			$response = $this->ai->generate_faq( $validated_data['prompt'], $validated_data );

			return new \WP_REST_Response(
				( new FaqSerializer() )->collectionToArray( $response )
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
