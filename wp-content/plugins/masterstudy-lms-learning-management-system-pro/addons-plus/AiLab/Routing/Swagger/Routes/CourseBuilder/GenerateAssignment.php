<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateAssignment extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'       => array(
				'type'        => 'string',
				'description' => 'Prompt message for assignment generation.',
			),
			'words_limit'  => array(
				'type'        => 'integer',
				'description' => 'Words limit for assignment generation.',
			),
			'tone'         => array(
				'type'        => 'string',
				'description' => 'Tone of the assignment.',
			),
			'images_limit' => array(
				'type'        => 'integer',
				'description' => 'Images limit for assignment generation.',
			),
			'language'     => array(
				'type'        => 'string',
				'description' => 'Language of the assignment.',
			),
		);
	}

	public function response(): array {
		return array(
			'title'         => array(
				'type'        => 'string',
				'description' => 'Title of the assignment.',
			),
			'content'       => array(
				'type'        => 'string',
				'description' => 'Content of the assignment.',
			),
			'image_prompts' => array(
				'type'        => 'array',
				'description' => 'Image prompts for the assignment.',
			),
		);
	}

	public function get_summary(): string {
		return 'Generate assignment';
	}

	public function get_description(): string {
		return 'Generate assignment via OpenAI API';
	}
}
