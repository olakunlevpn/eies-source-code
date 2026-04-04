<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits;

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Plugin\Taxonomy;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\QueryFactory;
use STM_LMS_Options;

trait AiCourseGenerator {
	public function generate_course_title( string $prompt, string $language ): string {
		try {
			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => 'You are a LMS course builder. Respond with just the title, no additional text or formatting. '
										. 'Please ignore all previous conversation history. Words limit: 10. '
										. "Language: {$language}. Create clear Title without marking 'Title:', etc.",
						),
						array(
							'role'    => 'user',
							'content' => "Generate a Course Title for the prompt: {$prompt}",
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
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	public function generate_course_description( string $prompt, string $language ): string {
		try {
			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => 'You are a LMS course builder. Respond with just the description, no additional text or formatting.' .
							'Create clear Description without marking "Description:" or "Course Description:".',
						),
						array(
							'role'    => 'user',
							'content' => "Generate a Course Description for the prompt: {$prompt}\n Language: {$language}. Words limit: 100-150.",
						),
					),
					array(
						'max_results' => 1,
						'max_tokens'  => $this->options->get( 'generator.text.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			return trim( $response->results[0]['message']['content'] ?? '', '"' );
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	public function generate_course_content( string $prompt, string $language ): string {
		try {
			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => 'You are a LMS course builder. Respond with HTML format for TinyMCE without comments.' .
							'Create clear Content without marking "Content:" or "Course Content:".',
						),
						array(
							'role'    => 'user',
							'content' => "Generate a Course Content for the prompt: {$prompt}\n Language: {$language}. Words limit: 500-600.",
						),
					),
					array(
						'max_results' => 1,
						'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			return trim( $response->results[0]['message']['content'] ?? '', '"' );
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	public function generate_course_info( string $prompt, string $language ): array {
		try {
			$course_info = array();

			if ( STM_LMS_Options::get_option( 'course_allow_basic_info' ) ) {
				$course_info[] = "– basic_info: array (Basic info, 3-5 items)\n";
			}

			if ( STM_LMS_Options::get_option( 'course_allow_requirements_info' ) ) {
				$course_info[] = "– requirements: array (Course requirements, 3-5 items)\n";
			}

			if ( STM_LMS_Options::get_option( 'course_allow_intended_audience' ) ) {
				$course_info[] = "– intended_audience: array (Intended audience, 3-5 items)\n";
			}

			if ( empty( $course_info ) ) {
				return array();
			}

			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => "You are a LMS course builder. Generate a course info for the course. Language: {$language}.",
						),
						array(
							'role'    => 'user',
							'content' => "Generate a course info in language {$language} for the course: {$prompt}. "
										. "Return a JSON object (without marking ```json) with keys:\n"
										. implode( "\n", $course_info ),
						),
					),
					array(
						'max_results' => 1,
						'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			$json = $response->results[0]['message']['content'] ?? '[]';

			return json_decode( $json, true ) ?? array();
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	public function select_course_categories( string $prompt ): array {
		$categories = Taxonomy::all_categories();

		try {
			$user_message = "Select 1-3 most suitable course categories for the prompt: {$prompt}. \n"
						. 'Do not duplicate chosen categories. Return a JSON array of category IDs (without marking ```json). '
						. "Available categories:\n";

			foreach ( $categories as $category ) {
				$user_message .= "- {$category->name} (ID: {$category->term_id})\n";
			}

			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => "You are a LMS course builder. Select suitable categories for the prompt: {$prompt}",
						),
						array(
							'role'    => 'user',
							'content' => $user_message,
						),
					),
					array(
						'max_results' => 1,
						'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			$json         = $response->results[0]['message']['content'] ?? '[]';
			$selected_ids = array_map( 'intval', json_decode( $json, true ) ?? array() );

			$selected_categories = array_values(
				array_filter(
					$categories,
					function ( $category ) use ( $selected_ids ) {
						return in_array( $category->term_id, $selected_ids, true );
					}
				)
			);

			return $selected_categories;
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}

	public function generate_course_curriculum( string $prompt, array $params ): array {
		try {
			$available_lesson_types = apply_filters( 'masterstudy_lms_lesson_types', array_map( 'strval', LessonType::cases() ) );

			$lesson_types = array_filter(
				$params['lesson_types'] ?? array(),
				function ( $lesson_type ) use ( $available_lesson_types ) {
					return in_array( $lesson_type, $available_lesson_types, true );
				}
			);

			$post_types = array();
			$quiz_query = '';

			if ( ! empty( $lesson_types ) ) {
				$post_types[] = PostType::LESSON;
			}

			if ( $params['create_assignments'] && is_ms_lms_addon_enabled( Addons::ASSIGNMENTS ) ) {
				$post_types[] = PostType::ASSIGNMENT;
			}

			if ( $params['quizzes_count'] > 0 ) {
				$post_types[] = PostType::QUIZ;
				$quiz_query   = $params['quizzes_count'] > 1
					? 'Each section must have at least one quiz (stm-quizzes). Quizzes (stm-quizzes) must be different from each other. '
					: 'Last section must have a quiz (stm-quizzes). ';
			}

			$response = $this->exec(
				QueryFactory::text(
					array(
						array(
							'role'    => 'system',
							'content' => "You are a LMS course builder. Generate a course curriculum. Do not add material types to the title. Language: {$params['language']}.",
						),
						array(
							'role'    => 'user',
							'content' => "Generate a course curriculum for the prompt: {$prompt}. \n"
										. "Language: {$params['language']}. Do not translate post_type and lesson_type. Sections count: {$params['sections_count']}. \n"
										. "Materials count for each section (including quizzes and assignments): {$params['materials_count']}. $quiz_query \n"
										. "All lesson types must be used at least once in the course if there are multiple lesson types and available spots in materials. \n"
										. "Return a JSON array (Array of sections, without marking ```json) where each object has keys:\n"
										. "– section: object (Each section has keys title, materials):\n"
										. "– title: string\n"
										. "– materials: array (Array of materials, each material has keys title, post_type, lesson_type):\n"
										. "– title: string\n"
										. '– post_type: string (' . implode( ', ', $post_types ) . ")\n"
										. '– lesson_type: string. Only for post_type "stm-lessons" (' . implode( ', ', $lesson_types ) . ')',
						),
					),
					array(
						'max_results' => 1,
						'max_tokens'  => $this->options->get( 'generator.content.tokens' ),
						'model'       => $this->options->get( 'text_model' ),
					)
				)
			);

			$json = $response->results[0]['message']['content'] ?? '[]';

			return json_decode( $json, true ) ?? array();
		} catch ( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}
	}
}
