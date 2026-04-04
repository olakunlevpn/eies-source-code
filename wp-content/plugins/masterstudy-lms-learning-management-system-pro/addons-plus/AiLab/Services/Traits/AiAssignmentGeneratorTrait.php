<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\QueryFactory;

trait AiAssignmentGeneratorTrait {
	private string $prompt;
	private int $words_limit;
	private string $tone;
	private int $images_limit;
	private string $title;
	private string $language;

	public function generate_assignment( string $prompt, array $params = array() ): array {
		$this->prompt       = $prompt;
		$this->words_limit  = $params['words_limit'];
		$this->tone         = $params['tone'];
		$this->images_limit = $params['images_limit'];
		$this->language     = $params['language'];

		try {
			$this->title = $this->generate_assignment_title();
			$content     = $this->generate_assignment_content();

			return array(
				'title'         => $this->title,
				'content'       => $this->replace_image_placeholders( $content['content'] ),
				'image_prompts' => $content['image_prompts'] ?? array(),
			);
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	private function generate_assignment_title(): string {
		$response = $this->exec(
			QueryFactory::text(
				array(
					array(
						'role'    => 'system',
						'content' => "You are an educational content creator. Create a clear, engaging, and concise title for an assignment.\n"
									. 'Respond with just the title, no additional text or formatting.',
					),
					array(
						'role'    => 'user',
						'content' => "Create a title for an assignment about: {$this->prompt}\n"
									. "Language: {$this->language}.",
					),
				),
				array(
					'max_results' => 1,
					'max_tokens'  => $this->options->get( 'generator.title.tokens' ),
					'model'       => $this->options->get( 'text_model' ),
				)
			)
		);

		return trim( $response->results[0]['message']['content'] ?? '', '"' );
	}

	private function generate_assignment_content(): array {
		$response = $this->exec(
			QueryFactory::text(
				array(
					array(
						'role'    => 'system',
						'content' => "You are an educational content creator. Create a clear, engaging, and concise content for an assignment.\n" .
						"Respond with HTML format for TinyMCE without comments. Use [image][/image] wrapping detailed description of image to insert images.\n" .
						"Image example: [image]A diagram showing the REST API structure[/image]\n" .
						"Create engaging, educational content that fulfills the description's promises.",
					),
					array(
						'role'    => 'user',
						'content' => "Create the main Assignment with these details:\n" .
									"Title: {$this->title}\n" .
									"Topic: {$this->prompt}\n\n" .
									"Approximate words count: {$this->words_limit}\n" .
									"Images count: {$this->images_limit}\n" .
									"Tone: {$this->tone}\n" .
									"Do not add Title at the beginning of the content. Language: {$this->language}.\n" .
									'Create engaging, educational Assignment that fulfills above details.',
					),
				),
				array(
					'max_results' => 1,
					'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
					'model'       => $this->options->get( 'text_model' ),
				)
			)
		);

		return $this->parse_content_response( $response->results[0]['message']['content'] ?? '' );
	}
}
