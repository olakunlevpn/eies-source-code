<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateCourseInfo extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'   => array(
				'type'        => 'string',
				'description' => 'Prompt message for course generation.',
			),
			'language' => array(
				'type'        => 'string',
				'description' => 'Language of the course.',
			),
		);
	}

	public function response(): array {
		return array(
			'data' => array(
				'type'        => 'object',
				'description' => 'Course info object.',
				'properties'  => array(
					'basic_info'        => array(
						'type'        => 'string',
						'description' => 'Basic info. Optional.',
					),
					'requirements'      => array(
						'type'        => 'string',
						'description' => 'Requirements. Optional.',
					),
					'intended_audience' => array(
						'type'        => 'string',
						'description' => 'Intended audience. Optional.',
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Generate a course info.';
	}

	public function get_description(): string {
		return 'Generate Course Info.';
	}
}
