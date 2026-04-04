<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UploadFile extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'file' => 'string',
		);
	}

	public function get_summary(): string {
		return 'Upload a file to OpenAI';
	}

	public function get_description(): string {
		return 'Upload a file to OpenAI';
	}
}
