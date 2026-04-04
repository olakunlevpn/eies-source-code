<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\QueryFactory;

trait AiLessonGeneratorTrait {
	private string $prompt;
	private int $words_limit;
	private string $tone;
	private int $images_limit;
	private string $title;
	private string $language;

	public function generate_lesson( string $prompt, array $params = array() ): array {
		$this->prompt       = $prompt;
		$this->words_limit  = $params['words_limit'];
		$this->tone         = $params['tone'];
		$this->images_limit = $params['images_limit'];
		$this->language     = $params['language'];

		try {
			$this->title = $this->generate_lesson_title();
			$content     = $this->generate_lesson_content();
			$words_count = str_word_count( $content['content'] );

			return array(
				'title'         => $this->title,
				'description'   => $this->generate_lesson_description(),
				'content'       => $this->replace_image_placeholders( $content['content'] ),
				'image_prompts' => $content['image_prompts'] ?? array(),
				'duration'      => $this->generate_lesson_duration( $words_count, $this->images_limit ),
			);
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	private function generate_lesson_title(): string {
		$response = $this->exec(
			QueryFactory::text(
				array(
					array(
						'role'    => 'system',
						'content' => "You are an educational content creator. Create a clear, engaging, and concise title for a lesson. Please ignore all previous conversation history.\n"
									. 'Respond with just the title, no additional text or formatting.',
					),
					array(
						'role'    => 'user',
						'content' => "Create a title for a lesson about: {$this->prompt}\n"
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

	private function generate_lesson_description(): string {
		$response = $this->exec(
			QueryFactory::text(
				array(
					array(
						'role'    => 'system',
						'content' => 'Create a concise 2-3 sentence description that outlines the main learning objectives of this lesson. The description should be engaging and informative.',
					),
					array(
						'role'    => 'user',
						'content' => "Create a description for a lesson with the following details:\n" .
									"Title: {$this->title}\n" .
									"Topic: {$this->prompt}\n" .
									"Language: {$this->language}. Note: The description should explain what students will learn.",
					),
				),
				array(
					'max_results' => 1,
					'max_tokens'  => $this->options->get( 'generator.text.tokens' ),
					'model'       => $this->options->get( 'text_model' ),
				)
			)
		);

		return trim( $response->results[0]['message']['content'] ?? '' );
	}

	private function generate_lesson_content(): array {
		$response = $this->exec(
			QueryFactory::text(
				array(
					array(
						'role'    => 'system',
						'content' => "You are an educational content creator. Create the main content for a lesson.\n" .
									"Respond with HTML format for TinyMCE without comments. Use [image][/image] wrapping detailed description of image to insert images.\n" .
									"Image example: [image]A diagram showing the REST API structure[/image]\n" .
									"Create engaging, educational content that fulfills the description's promises.",
					),
					array(
						'role'    => 'user',
						'content' => "Create the main content for a lesson with these details:\n" .
									"Title: {$this->title}\n" .
									"Prompt: {$this->prompt}\n\n" .
									"Approximate words count: {$this->words_limit}\n" .
									"Images count: {$this->images_limit}\n" .
									"Tone: {$this->tone}\n" .
									"Language: {$this->language}. Note: Do not add Title at the beginning of the content." .
									"Create engaging, educational content that fulfills the description's promises.",
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

	public function generate_lesson_duration( int $words_count, int $images_count ): string {
		$estimated_minutes = round( $words_count / 150 );
		$image_time        = $images_count * 2.5;
		$total_minutes     = $estimated_minutes + $image_time;

		return $this->format_duration( $total_minutes );
	}

	public function format_duration( float $minutes ): string {
		$hours             = floor( $minutes / 60 );
		$remaining_minutes = round( $minutes % 60 );

		if ( $hours > 0 ) {
			if ( $remaining_minutes > 0 ) {
				return sprintf(
					// translators: %1$d - hours, %2$d - minutes
					esc_html__( '%1$dh %2$dm', 'masterstudy-lms-learning-management-system-pro' ),
					$hours,
					$remaining_minutes
				);
			} else {
				return sprintf(
					// translators: %d - hours
					esc_html__( '%dh', 'masterstudy-lms-learning-management-system-pro' ),
					$hours
				);
			}
		} else {
			return sprintf(
				// translators: %d - minutes
				esc_html__( '%dm', 'masterstudy-lms-learning-management-system-pro' ),
				$remaining_minutes
			);
		}
	}
}
