<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Exception;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\UploadFile;
use WP_REST_Request;
use WP_REST_Response;

class UploadFileController extends Controller {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		try {
			$file = $this->client->upload_file(
				new UploadFile(
					sanitize_text_field( $request->get_param( 'filename' ) ),
					$request->get_param( 'data' )
				),
				'fine-tune'
			);

			return new WP_REST_Response(
				array(
					'file' => $file,
				)
			);
		} catch ( Exception $e ) {
			return WpResponseFactory::error( $e->getMessage() );
		}
	}
}
