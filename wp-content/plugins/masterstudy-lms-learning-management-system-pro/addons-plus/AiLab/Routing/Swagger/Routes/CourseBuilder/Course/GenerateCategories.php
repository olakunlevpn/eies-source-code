<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder\Course;

use MasterStudy\Lms\Routing\Swagger\Fields\Category;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateCategories extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt' => array(
				'type'        => 'string',
				'description' => 'Prompt message for course categories generation.',
			),
		);
	}

	public function response(): array {
		return array(
			'categories' => Category::as_array(),
		);
	}

	public function get_summary(): string {
		return 'Generate a course categories.';
	}

	public function get_description(): string {
		return 'Generate Course Categories.';
	}
}
