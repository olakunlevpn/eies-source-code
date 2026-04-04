<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class DeleteFile extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'success' => 'boolean',
			'data'    => array(
				'id'      => 'string',
				'object'  => 'string',
				'deleted' => 'boolean',
			),
		);
	}

	public function get_summary(): string {
		return 'Delete OpenAI File';
	}

	public function get_description(): string {
		return 'Delete a file from OpenAI.';
	}
}
