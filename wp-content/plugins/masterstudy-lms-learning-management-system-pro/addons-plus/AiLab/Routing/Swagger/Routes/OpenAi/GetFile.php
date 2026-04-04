<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetFile extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'success' => 'boolean',
			'data'    => array(
				'id'         => 'string',
				'object'     => 'string',
				'bytes'      => 'integer',
				'created_at' => 'integer',
				'filename'   => 'string',
				'purpose'    => 'string',
			),
		);
	}

	public function get_summary(): string {
		return 'Get OpenAI File Details';
	}

	public function get_description(): string {
		return 'Returns the details of a specific file.';
	}
}
