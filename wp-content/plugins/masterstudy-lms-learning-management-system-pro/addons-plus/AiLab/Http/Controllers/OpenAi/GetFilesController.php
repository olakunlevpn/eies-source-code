<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use WP_REST_Request;
use WP_REST_Response;

class GetFilesController extends Controller {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		try {
			$files = $this->client->get_files();

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $files,
				)
			);
		} catch ( \Exception $e ) {
			return WpResponseFactory::bad_request( $e->getMessage() );
		}
	}
}
