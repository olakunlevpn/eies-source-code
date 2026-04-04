<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateText extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt' => array(
				'type'        => 'string',
				'description' => 'Prompt message for text generation.',
			),
			'type'   => array(
				'type'        => 'string',
				'description' => 'Type of text to generate.',
				'enum'        => array( 'title', 'text', 'content' ),
			),
			'tone'   => array(
				'type'        => 'string',
				'description' => 'Tone of text to generate.',
				'enum'        => array( 'formal', 'casual', 'engaging', 'professional' ),
			),
			'length' => array(
				'type'        => 'integer',
				'description' => 'Length of text to generate.',
			),
		);
	}

	public function response(): array {
		return array(
			'data' => array(
				'type'        => 'array',
				'description' => 'Array of generated texts.',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'index' => array(
							'type'        => 'integer',
							'description' => 'Index',
						),
						'text'  => array(
							'type'        => 'string',
							'description' => 'Generated text',
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
