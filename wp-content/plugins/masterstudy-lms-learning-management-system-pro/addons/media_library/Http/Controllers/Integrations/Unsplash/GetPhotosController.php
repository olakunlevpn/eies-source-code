<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Unsplash;

use Exception;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Controller;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Helpers\UnsplashHelper;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Unsplash;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

final class GetPhotosController extends Controller {
	private const LIST_ENDPOINT   = '/photos';
	private const SEARCH_ENDPOINT = '/search/photos';

	protected $url             = 'https://api.unsplash.com';
	protected $key_err_message = 'Unsplash\'s access key is not set';
	protected $key             = 'unsplash_access_key';
	protected $key_prefix      = 'Client-ID ';

	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		try {
			$endpoint = self::LIST_ENDPOINT;
			$params   = UnsplashHelper::validate_params( $request, $this->per_page );

			if ( $params instanceof \WP_REST_Response ) {
				return $params;
			}

			if ( ! empty( $params['query'] ) ) {
				$endpoint = self::SEARCH_ENDPOINT;
			}

			$response = $this->client->request( 'GET', $endpoint, $params );
			$headers  = $response['response']['headers'];

			// If search then images are inside of results, if list then inside of data
			$data = empty( $response['data']['results'] ) ? $response['data'] : $response['data']['results'];

			return new \WP_REST_Response(
				array(
					'success' => true,
					'total'   => (int) $headers['X-Total'],
					'limit'   => (int) $headers['X-Per-Page'],
					'data'    => ( new Unsplash\PhotoSerializer() )->collectionToArray( $data ),
				)
			);
		} catch ( Exception $e ) {
			return WpResponseFactory::bad_request( $e->getMessage() );
		}
	}
}
