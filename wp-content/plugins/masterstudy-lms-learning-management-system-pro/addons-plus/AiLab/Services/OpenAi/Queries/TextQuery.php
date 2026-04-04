<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Model;

final class TextQuery extends Query {
	public int $max_tokens    = 16;
	public $stop              = null;
	public float $temperature = 0.8;

	public function __construct( array $messages = array(), int $max_tokens = 16, string $model = Model::GPT_35_TURBO ) {
		$this->set_max_tokens( $max_tokens );
		$this->set_model( $model );
		$this->set_messages( $messages );
	}

	/**
	 * The maximum number of tokens to generate in the completion.
	 * The token count of your prompt plus max_tokens cannot exceed the model's context length.
	 * Most models have a context length of 2048 tokens (except for the newest models, which support 4096).
	 */
	public function set_max_tokens( int $max_tokens ) {
		$this->max_tokens = $max_tokens;
	}

	/**
	 * Set the sampling temperature to use. Higher values means the model will take more risks.
	 * Try 0.9 for more creative applications, and 0 for ones with a well-defined answer.
	 */
	public function set_temperature( float $temperature ) {
		if ( $temperature > 1 ) {
			$temperature = 1.0;
		}
		if ( $temperature < 0 ) {
			$temperature = 0.0;
		}
		$this->temperature = $temperature;
	}

	/**
	 * Up to 4 sequences where the API will stop generating further tokens.
	 * The returned text will not contain the stop sequence.
	 */
	public function set_stop( $stop ): void {
		if ( ! empty( $stop ) ) {
			$this->stop = $stop;
		}
	}

	public function to_request_body(): array {
		$body = array(
			'model'       => $this->model,
			'messages'    => $this->messages,
			'stop'        => $this->stop,
			'n'           => $this->max_results,
			'max_tokens'  => $this->max_tokens,
			'temperature' => $this->temperature,
		);

		return $this->prepare_body_by_model( $body );
	}

	private function prepare_body_by_model( array $body ): array {
		switch ( $this->model ) {
			case Model::GPT_5:
			case Model::GPT_5_MINI:
			case Model::GPT_5_NANO:
				// GPT-5 changed name for max_tokens to max_completion_tokens and the amount of maximum tokens is doubled by 8
				$body['max_completion_tokens'] = $body['max_tokens'] * 8;
				unset( $body['max_tokens'] );

				// GPT-5 doesn't support temperature value except 1
				$body['temperature'] = 1;
				return $body;
			default:
				return $body;
		}
	}
}
