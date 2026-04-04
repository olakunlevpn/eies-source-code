<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi;

use Exception;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use WP_REST_Response;

class DeleteFileController extends Controller {
	public function __invoke( $file_id ): WP_REST_Response {
		try {
			$this->client->delete_file( $file_id );

			return WpResponseFactory::ok();
		} catch ( Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
