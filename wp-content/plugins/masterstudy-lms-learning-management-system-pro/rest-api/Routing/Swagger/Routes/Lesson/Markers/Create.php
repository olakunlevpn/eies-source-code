<?php
namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Create extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'message' => array(
				'type'        => 'string',
				'description' => 'Message about successfully added markers',
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
		);
	}

	public function get_summary(): string {
		return 'Create new markers for a lesson';
	}

	public function get_description(): string {
		return 'Adds new markers (e.g., timecodes, captions) to a lesson. Markers can include types like multiple_choice, single_choice, etc.';
	}
}
