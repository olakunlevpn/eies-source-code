<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetFiles extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'success' => 'boolean',
			'data'    => array(
				'object' => 'string',
				'data'   => array(
					array(
						'id'         => 'string',
						'object'     => 'string',
						'bytes'      => 'integer',
						'created_at' => 'integer',
						'filename'   => 'string',
						'purpose'    => 'string',
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Get OpenAI Files List';
	}

	public function get_description(): string {
		return 'Returns a list of files that belong to the user\'s organization.';
	}
}
