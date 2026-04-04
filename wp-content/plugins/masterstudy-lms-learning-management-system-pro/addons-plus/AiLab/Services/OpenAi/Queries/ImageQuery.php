<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Model;

final class ImageQuery extends Query {
	public string $size;

	private const ALLOWED_SIZES = array(
		'256x256',
		'512x512',
		'1024x1024',
		'1536x1024',
		'1024x1536',
		'1792x1024',
		'1024x1792',
		'auto',
	);

	public function __construct( $prompt, $model = Model::DALL_E_3, $size = '1792x1024' ) {
		$this->set_prompt( $prompt );
		$this->set_model( $model );
		$this->set_size( $size );
	}

	public function set_size( $size ) {
		if ( ! in_array( $size, self::ALLOWED_SIZES, true ) ) {
			throw new \InvalidArgumentException( 'Invalid size.' );
		}
		$this->size = $size;
	}

	public function to_request_body(): array {
		$request_body = array(
			'prompt' => $this->prompt,
			'n'      => $this->max_results,
			'size'   => $this->size,
			'model'  => $this->model,
		);

		if ( Model::DALL_E_3 === $request_body['model'] ) {
			$request_body['quality'] = 'hd'; // High quality for DALL-E 3
		}

		return $request_body;
	}
}
