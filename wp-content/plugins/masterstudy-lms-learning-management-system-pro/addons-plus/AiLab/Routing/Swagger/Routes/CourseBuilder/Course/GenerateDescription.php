<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateDescription extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'   => array(
				'type'        => 'string',
				'description' => 'Prompt message for course description generation.',
			),
			'language' => array(
				'type'        => 'string',
				'description' => 'Language of the course description.',
			),
		);
	}

	public function response(): array {
		return array(
			'description' => array(
				'type'        => 'string',
				'description' => 'Course description.',
			),
		);
	}

	public function get_summary(): string {
		return 'Generate a course description.';
	}

	public function get_description(): string {
		return 'Generate Course Description.';
	}
}
