<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Enums\CustomQuestionType;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\QueryFactory;
use MasterStudy\Lms\Enums\QuestionType;

trait AiQuestionsGeneratorTrait {
	private string $prompt;
	private int $questions_count;
	private int $answers_limit;
	private string $images_style;
	private array $questions_types;
	private string $language;

	public function generate_questions( string $prompt, array $params = array() ): array {
		$this->prompt          = $prompt;
		$this->questions_count = $params['questions_count'];
		$this->answers_limit   = $params['answers_limit'];
		$this->images_style    = $params['images_style'];
		$this->language        = $params['language'];
		$allowed_types         = array_merge(
			array_map( fn( $type ) => QuestionType::from( $type )->value, QuestionType::cases() ),
			array_map( fn( $type ) => CustomQuestionType::from( $type )->value, CustomQuestionType::cases() )
		);

		$this->questions_types = array_values(
			array_filter(
				(array) $params['questions_types'],
				function ( $type ) use ( $allowed_types ) {
					return in_array( $type, $allowed_types, true );
				}
			)
		);
		shuffle( $this->questions_types );

		$num_types = count( $this->questions_types );
		if ( 0 === $num_types ) {
			return array();
		}

		$base      = intdiv( $this->questions_count, $num_types );
		$remainder = $this->questions_count % $num_types;
		$counts    = array();

		foreach ( $this->questions_types as $i => $type ) {
			$counts[ $type ] = $base + ( $i < $remainder ? 1 : 0 );
		}

		$all_questions = array();
		foreach ( $this->questions_types as $type ) {
			$count = $counts[ $type ] ?? 0;
			if ( $count < 1 ) {
				continue;
			}

			$questions = array();

			try {
				$system_message = "Please ignore all previous conversation history. You are a quiz generator. Generate {$count} {$type} questions about: {$this->prompt}\n"
								. "Use up to {$this->answers_limit} answers per question. true_false questions should have only 2 answers. "
								. 'multi_choice questions may have multiple correct answers.';
				$user_message   = "Generate {$count} questions of type {$type} about: {$this->prompt}.\n"
								. "Language: {$this->language}. Use up to {$this->answers_limit} answers per question.\n"
								. "Return a JSON array (without marking ```json) where each object has keys:\n"
								. "– question: string\n"
								. "– type: string\n";

				if ( QuestionType::FILL_THE_GAP === $type ) {
					$system_message .= 'fill_the_gap questions does not have separate answers, it is a phrase with piped words.';
					$user_message   .= "\n (fill_the_gap question example: Early in the morning, Lily |dipped| her toes in the lake and |laughed| softly)";
				}

				if ( in_array( $type, array( QuestionType::IMAGE_MATCH, QuestionType::ITEM_MATCH ), true ) ) {
					$user_message .= 'answers: array of { question: string, text: string, isTrue: boolean }. "question" and "text" fields should match each other.';
				} else {
					$user_message .= '– answers: array of { text: string, isTrue: boolean }';
				}

				if ( QuestionType::KEYWORDS === $type ) {
					$user_message .= ' (answer text should be short, 1-2 words)';
				}

				if ( CustomQuestionType::IMAGE_CHOICE === $type ) {
					$user_message .= ' (answer text should be descriptive enough)';
				}

				$response = $this->exec(
					QueryFactory::text(
						array(
							array(
								'role'    => 'system',
								'content' => $system_message,
							),
							array(
								'role'    => 'user',
								'content' => $user_message,
							),
						),
						array(
							'max_results' => 1,
							'max_tokens'  => $this->options->get( 'generator.quiz.tokens' ),
							'model'       => $this->options->get( 'text_model' ),
						)
					)
				);

				$json      = $response->results[0]['message']['content'] ?? '[]';
				$questions = json_decode( $json, true ) ?? array();

				// Fill the gap questions should have only 1 answer which is a phrase with piped words.
				if ( QuestionType::FILL_THE_GAP === $type ) {
					$questions = array_map(
						function ( $question ) {
							$question['answers'][0] = array(
								'text'   => $question['question'],
								'isTrue' => true,
							);
							$question['question']   = esc_html__( 'Fill the gap', 'masterstudy-lms-learning-management-system-pro' );

							return $question;
						},
						$questions
					);
				}
			} catch ( \Exception $e ) {
				throw new \Exception( $e->getMessage() );
			}

			if ( ! empty( $questions ) ) {
				$all_questions = array_merge( $all_questions, $questions );
			}
		}

		return $all_questions;
	}
}
