<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateContent extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'   => array(
				'type'        => 'string',
				'description' => 'Prompt message for course content generation.',
			),
			'language' => array(
				'type'        => 'string',
				'description' => 'Language of the course content.',
			),
		);
	}

	public function response(): array {
		return array(
			'content' => array(
				'type'        => 'string',
				'description' => 'Course content.',
			),
		);
	}

	public function get_summary(): string {
		return 'Generate a course content.';
	}

	public function get_description(): string {
		return 'Generate Course Content.';
	}
}
