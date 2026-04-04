<?php
namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class Delete extends Route implements ResponseInterface {

	public function response(): array {
		return array(
			'message' => array(
				'type'        => 'string',
				'description' => 'Marker deleted successfully',
			),
		);
	}

	public function get_summary(): string {
		return 'Update existing markers for a lesson';
	}

	public function get_description(): string {
		return 'Updates existing markers for a specific lesson. You can modify the caption, timecode, type, and more.';
	}
}
