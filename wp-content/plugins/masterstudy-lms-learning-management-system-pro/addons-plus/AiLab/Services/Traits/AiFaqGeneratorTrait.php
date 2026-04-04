<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\QueryFactory;

trait AiFaqGeneratorTrait {
	private string $prompt;
	private int $words_limit;
	private int $count;
	private string $tone;
	private string $language;

	public function generate_faq( string $prompt, array $params = array() ): array {
		$this->prompt      = $prompt;
		$this->words_limit = $params['words_limit'];
		$this->count       = $params['count'];
		$this->tone        = $params['tone'];
		$this->language    = $params['language'];

		try {
			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => "Generate {$this->count} frequently asked questions and answers. Make answers clear and concise. Language: {$this->language}.",
						),
						array(
							'role'    => 'user',
							'content' => "Generate {$this->count} frequently asked question and answer about: {$this->prompt}. \n"
							. "Words limit for each answer: {$this->words_limit}. Language: {$this->language}.\n"
							. "Return a JSON array (without marking ```json) where each item has keys 'question' and 'answer':\n"
							. "- question: string\n"
							. '- answer: string',
						),
					),
					array(
						'max_results' => 1,
						'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			return json_decode( $response->results[0]['message']['content'] ?? '[]', true ) ?? array();
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}
}
