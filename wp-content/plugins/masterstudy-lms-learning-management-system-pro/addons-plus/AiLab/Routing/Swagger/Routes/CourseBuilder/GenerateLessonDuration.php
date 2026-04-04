<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateLessonDuration extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'duration' => array(
				'type'        => 'string',
				'description' => 'Duration of the lesson.',
			),
		);
	}

	public function get_summary(): string {
		return 'Generate lesson duration';
	}

	public function get_description(): string {
		return 'Generate lesson duration via OpenAI API';
	}
}
