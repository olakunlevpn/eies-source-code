<?php
namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UpdateQuestionsLock extends Route implements RequestInterface, ResponseInterface {

	public function response(): array {
		return array(
			'message' => array(
				'type'        => 'string',
				'description' => 'Message about successfully update marker lock post meta',
			),
		);
	}
	public function request(): array {
		return array(
			'lesson_id' => array(
				'type'        => 'integer',
				'description' => 'second for the marker video point',
			),
			'is_locked' => array(
				'type'        => 'boolean',
				'description' => 'caption for the video marker point',
			),
		);
	}

	public function get_summary(): string {
		return 'Update marker post meta for a lesson';
	}

	public function get_description(): string {
		return 'Update marker post meta include lesson id and is_locked';
	}
}
