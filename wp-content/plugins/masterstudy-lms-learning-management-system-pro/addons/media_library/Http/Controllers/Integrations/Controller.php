<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\media_library\Services\Client;
use MasterStudy\Lms\Pro\addons\media_library\Utility\Options;
use WP_REST_Request;

abstract class Controller {
	protected $url;
	protected $per_page = 30;
	protected $key_err_message;
	protected $client;
	protected $key;
	protected $key_prefix       = '';
	protected $omit_client_auth = false;
	protected $api_key;

	public function __construct() {
		$settings = Options::get_settings();

		if ( ! empty( $settings[ $this->key ] ) ) {
			$this->api_key = $settings[ $this->key ];
		} else {
			$this->api_key = null;
		}

		$this->client = new Client( ! $this->omit_client_auth ? $this->key_prefix . $settings[ $this->key ] : null, $this->url );
	}

	abstract public function __invoke( WP_REST_Request $request ): \WP_REST_Response;
}
