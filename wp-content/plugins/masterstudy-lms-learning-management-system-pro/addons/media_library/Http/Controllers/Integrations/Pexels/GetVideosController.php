<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Pexels;

use Exception;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Controller;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Helpers\PexelsHelper;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Pexels;
use WP_REST_Request;

final class GetVideosController extends Controller {
	private const LIST_ENDPOINT   = '/videos/popular';
	private const SEARCH_ENDPOINT = '/videos/search';

	protected $url             = 'https://api.pexels.com';
	protected $key_err_message = 'Pexels\' api key is not set';
	protected $key             = 'pexels_api_key';

	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		try {
			$params   = PexelsHelper::validate_params( $request, $this->per_page );
			$endpoint = self::LIST_ENDPOINT;

			if ( $params instanceof \WP_REST_Response ) {
				return $params;
			}

			if ( ! empty( $params['query'] ) ) {
				$endpoint = self::SEARCH_ENDPOINT;
			}

			$response = $this->client->request( 'GET', $endpoint, $params );
			$data     = $response['data'];

			return new \WP_REST_Response(
				array(
					'success' => true,
					'total'   => (int) $data['total_results'],
					'limit'   => (int) $data['per_page'],
					'data'    => ( new Pexels\VideoSerializer() )->collectionToArray( $data['videos'] ),
				)
			);
		} catch ( Exception $e ) {
			return WpResponseFactory::bad_request( $e->getMessage() );
		}
	}
}
