<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\Fields\QuestionType;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateQuestions extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'          => array(
				'type'        => 'string',
				'description' => 'Prompt message for text generation.',
			),
			'questions_count' => array(
				'type'        => 'integer',
				'description' => 'Number of questions to generate.',
			),
			'answers_limit'   => array(
				'type'        => 'integer',
				'description' => 'Number of answers per question.',
			),
			'images_style'    => array(
				'type'        => 'string',
				'description' => 'Style of images to generate.',
			),
			'questions_types' => array(
				'type'        => 'array',
				'description' => 'Types of questions to generate.',
				'enum'        => QuestionType::as_array(),
			),
			'language'        => array(
				'type'        => 'string',
				'description' => 'Language of the questions.',
			),
		);
	}

	public function response(): array {
		return array(
			'questions' => array(
				'type'        => 'array',
				'description' => 'Array of generated questions.',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'question' => array(
							'type'        => 'string',
							'description' => 'Question text',
						),
						'type'     => array(
							'type'        => 'string',
							'description' => 'Type of question',
						),
						'answers'  => array(
							'type'        => 'array',
							'description' => 'Array of answers',
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'text'     => array(
										'type'        => 'string',
										'description' => 'Answer text',
									),
									'isTrue'   => array(
										'type'        => 'boolean',
										'description' => 'Is answer correct',
									),
									'question' => array(
										'type'        => 'string',
										'description' => 'Question text for Item Match question',
									),
								),
							),
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Generate Text';
	}

	public function get_description(): string {
		return 'Returns array of generated texts via OpenAI API';
	}
}
