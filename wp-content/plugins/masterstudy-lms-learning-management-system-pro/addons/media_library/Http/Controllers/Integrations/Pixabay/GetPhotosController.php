<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Pixabay;

use Exception;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Controller;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Helpers\PixabayHelper;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Pixabay;
use WP_REST_Request;

final class GetPhotosController extends Controller {
	protected $url              = 'https://pixabay.com/api';
	protected $key_err_message  = 'Pixabay\'s api key is not set';
	protected $key              = 'pixabay_api_key';
	protected $omit_client_auth = true;

	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		try {
			$params = PixabayHelper::validate_params( $request, $this->api_key, $this->per_page );

			if ( $params instanceof \WP_REST_Response ) {
				return $params;
			}

			$response = $this->client->request( 'GET', '/', $params );
			$data     = $response['data'];

			return new \WP_REST_Response(
				array(
					'success' => true,
					'total'   => (int) $data['totalHits'],
					'limit'   => (int) $this->per_page,
					'data'    => ( new Pixabay\PhotoSerializer() )->collectionToArray( $data['hits'] ),
				)
			);
		} catch ( Exception $e ) {
			return WpResponseFactory::bad_request( $e->getMessage() );
		}
	}
}
