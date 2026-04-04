<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateLesson extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'       => array(
				'type'        => 'string',
				'description' => 'Prompt message for lesson generation.',
			),
			'words_limit'  => array(
				'type'        => 'integer',
				'description' => 'Words limit for lesson generation.',
			),
			'tone'         => array(
				'type'        => 'string',
				'description' => 'Tone of the lesson.',
			),
			'images_limit' => array(
				'type'        => 'integer',
				'description' => 'Images limit for lesson generation.',
			),
			'language'     => array(
				'type'        => 'string',
				'description' => 'Language of the lesson.',
			),
		);
	}

	public function response(): array {
		return array(
			'title'         => array(
				'type'        => 'string',
				'description' => 'Title of the lesson.',
			),
			'description'   => array(
				'type'        => 'string',
				'description' => 'Description of the lesson.',
			),
			'content'       => array(
				'type'        => 'string',
				'description' => 'Content of the lesson.',
			),
			'image_prompts' => array(
				'type'        => 'array',
				'description' => 'Image prompts for the lesson.',
			),
			'duration'      => array(
				'type'        => 'string',
				'description' => 'Duration of the lesson.',
			),
		);
	}

	public function get_summary(): string {
		return 'Generate lesson';
	}

	public function get_description(): string {
		return 'Generate lesson via OpenAI API';
	}
}
