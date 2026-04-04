<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Certificates;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class DeleteCertificate extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'status' => array(
				'type'    => 'string',
				'example' => 'ok',
			),
		);
	}

	public function get_summary(): string {
		return 'Delete certificate';
	}

	public function get_description(): string {
		return 'Delete certificate';
	}
}
