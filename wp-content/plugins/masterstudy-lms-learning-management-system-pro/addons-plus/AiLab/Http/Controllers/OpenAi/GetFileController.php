<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi;

use Exception;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use WP_REST_Response;

class GetFileController extends Controller {
	public function __invoke( $file_id ): WP_REST_Response {
		try {
			$data = $this->client->get_file_content( $file_id );

			return new WP_REST_Response(
				array(
					'file' => $data,
				)
			);
		} catch ( Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
