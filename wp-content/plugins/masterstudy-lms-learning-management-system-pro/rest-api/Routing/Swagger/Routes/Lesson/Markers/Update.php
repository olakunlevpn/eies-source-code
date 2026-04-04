<?php
namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Update extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'message' => array(
				'type'        => 'string',
				'description' => 'Message about successfully update markers',
			),
		);
	}
	public function request(): array {
		return array(
			'time'    => array(
				'type'        => 'integer',
				'description' => 'second for the marker video point',
			),
			'caption' => array(
				'type'        => 'string',
				'description' => 'caption for the video marker point',
			),
			'content' => array(
				'type'        => 'string',
				'description' => 'content for the video marker question',
			),
			'type'    => array(
				'type'        => 'string',
				'description' => 'type for the video marker question',
			),
			'answers' => array(
				'type'        => 'array',
				'description' => 'answers for the video marker question',
			),
			'rewatch' => array(
				'type'        => 'string',
				'description' => 'rewatch for the video marker question',
			),
		);
	}

	public function get_summary(): string {
		return 'Update marker for a lesson';
	}

	public function get_description(): string {
		return 'Update marker (e.g., timecodes, captions) to a lesson. Markers can include types like multiple_choice, single_choice, etc.';
	}
}
