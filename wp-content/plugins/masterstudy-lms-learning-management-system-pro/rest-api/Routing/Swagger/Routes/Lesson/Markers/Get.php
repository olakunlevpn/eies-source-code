<?php
namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Get extends Route implements ResponseInterface {

	public function response(): array {
		return array(
			'markers'         => array(
				'type'        => 'array',
				'description' => 'Array of markers for the lesson',
			),
			'lesson_metas'    => array(
				'type'        => 'array',
				'description' => 'Array of video lesson metas',
			),
			'shouldRenderTip' => array(
				'type'        => 'boolean',
				'description' => 'settings which will tell us should I render pro tip banner or not ',
			),
		);
	}

	public function get_summary(): string {
		return 'Retrieve markers for a lesson';
	}

	public function get_description(): string {
		return 'Retrieves the list of markers (timecodes, captions, and their types) associated with a specific lesson.';
	}
}
