<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\TextSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\QueryFactory;
use MasterStudy\Lms\Validation\Validator;

class GenerateTextController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'prompt'   => 'required|string',
				'type'     => 'required|string|contains_list,' . implode( ';', array_keys( $this->options->get( 'modules' ) ) ),
				'tone'     => 'required|string|contains_list,' . implode( ';', array_keys( $this->options->get( 'generator.content.tones' ) ) ),
				'length'   => 'required|integer',
				'language' => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();
		$prompt         = sprintf( $this->options->get( "generator.{$validated_data['type']}.prompt", '%s' ), $validated_data['prompt'] );
		$prompt        .= sprintf(
			'Tone: %s, Words Limit: %d, Language: %s',
			$validated_data['tone'],
			$validated_data['length'],
			$validated_data['language']
		);

		try {
			$response = $this->ai->exec(
				QueryFactory::text(
					QueryFactory::format_prompt_to_message( $prompt ),
					array(
						'max_results' => $this->options->get( 'results.text' ),
						'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			return new \WP_REST_Response(
				( new TextSerializer() )->collectionToArray( $response->results )
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
