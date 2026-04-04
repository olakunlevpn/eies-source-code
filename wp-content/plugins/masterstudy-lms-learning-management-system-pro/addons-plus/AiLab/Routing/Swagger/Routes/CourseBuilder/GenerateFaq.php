<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateFaq extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'      => array(
				'type'        => 'string',
				'description' => 'Prompt message for FAQ generation.',
			),
			'words_limit' => array(
				'type'        => 'integer',
				'description' => 'Words limit for FAQ generation.',
			),
			'count'       => array(
				'type'        => 'integer',
				'description' => 'Number of FAQ items to generate.',
			),
			'tone'        => array(
				'type'        => 'string',
				'description' => 'Tone of the FAQ.',
			),
			'language'    => array(
				'type'        => 'string',
				'description' => 'Language of the FAQ.',
			),
		);
	}

	public function response(): array {
		return array(
			'data' => array(
				'type'        => 'array',
				'description' => 'Array of FAQ items.',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'question' => array(
							'type'        => 'string',
							'description' => 'Question of the FAQ.',
						),
						'answer'   => array(
							'type'        => 'string',
							'description' => 'Answer of the FAQ.',
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Generate a list of FAQ items.';
	}

	public function get_description(): string {
		return 'Returns an array of generated FAQ items.';
	}
}
