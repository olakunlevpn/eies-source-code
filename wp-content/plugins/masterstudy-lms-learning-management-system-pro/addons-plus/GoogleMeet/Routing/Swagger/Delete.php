<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class Delete extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Delete Google Meeting';
	}

	public function get_description(): string {
		return 'Deletes Google Meeting';
	}
}
