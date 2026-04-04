<?php
// phpcs:ignoreFile

/**
 * @copyright  https://github.com/UsabilityDynamics/zoom-api-php-client/blob/master/LICENSE
 */

namespace Zoom\Contracts;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;

class Request {

	protected $apiKey;

	protected $apiSecret;

	protected $client;

	protected $accountID;

	protected $clientID;

	protected $clientSecret;

	/**
	 * @var string
	 */
	public $apiPoint = 'https://api.zoom.us/v2/';

	public function __construct() {

		$this->initProperties();

		$this->client = new Client();
	}

	/**
	 * Headers
	 *
	 * @return array
	 */
	protected function headers() {
		return array(
			'Authorization' => $this->getBearerToken(),
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
		);
	}

	/**
	 * Generate access S2S token
	 *
	 * @return string
	 */
	protected function getAccessToken() {
		return get_transient( 'mslms_global_oauth_data' )->access_token ?? '';
	}

	/**
	 * get BearerToken
	 *
	 * @return string
	 */
	private function getBearerToken() {

		$OauthData = get_transient( 'mslms_global_oauth_data' );
		
		if ( empty( $OauthData ) && ! empty( $account_id ) && ! empty( $client_id ) && ! empty( $client_secret ) ) {
			$result = \Zoom\Contracts\S2SOAuth::get_instance()->regenerateAccessTokenAndSave();
			set_transient( 'mslms_global_oauth_data', $result, 60 * 60 );
		}
		
		$token = ( ! is_wp_error( $OauthData ) && ! empty( $OauthData ) ) ? 'Bearer ' . $this->getAccessToken() : '';
		
		return $token;

	}

	/**
	 * init properties
	 *
	 * @return string
	 */
	private function initProperties() {
	$settings           = get_option( 'ms_zoom_settings', array() );
	$this->accountID    = $settings['auth_account_id'] ?? '';
	$this->clientID     = $settings['auth_client_id'] ?? '';
	$this->clientSecret = $settings['auth_client_secret'] ?? '';
	$this->apiSecret    = $settings['api_secret'] ?? '';
	$this->apiKey       = $settings['api_key'] ?? '';

	\Zoom\Contracts\S2SOAuth::get_instance()->generateAndSaveAccessToken( $this->accountID, $this->clientID, $this->clientSecret );
	}

	/**
	 * Get
	 *
	 * @param $method
	 * @param array $fields
	 *
	 * @return array|mixed
	 */
	protected function get( $method, $fields = array() ) {
		try {
			$response = $this->client->request(
				'GET',
				$this->apiPoint . $method,
				array(
					'query'   => $fields,
					'headers' => $this->headers(),
				)
			);

			return $this->result( $response );

		} catch ( ClientException $e ) {
			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		}
	}

	/**
	 * Post
	 *
	 * @param $method
	 * @param $fields
	 *
	 * @return array|mixed
	 */
	protected function post( $method, $fields ) {
		$body = wp_json_encode( $fields, JSON_PRETTY_PRINT );

		try {
			$response = $this->client->request(
				'POST',
				$this->apiPoint . $method,
				array(
					'headers' => $this->headers(),
					'body'    => $body,
				)
			);

			return $this->result( $response );

		} catch ( ClientException $e ) {

			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		}
	}

	/**
	 * Patch
	 *
	 * @param $method
	 * @param $fields
	 *
	 * @return array|mixed
	 */
	protected function patch( $method, $fields ) {
		$body = wp_json_encode( $fields, JSON_PRETTY_PRINT );

		try {
			$response = $this->client->request(
				'PATCH',
				$this->apiPoint . $method,
				array(
					'body'    => $body,
					'headers' => $this->headers(),
				)
			);

			return $this->result( $response );

		} catch ( ClientException $e ) {

			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		}
	}

	/**
	 * Put
	 *
	 * @param $method
	 * @param $fields
	 *
	 * @return array|mixed
	 */
	protected function put( $method, $fields ) {
		$body = wp_json_encode( $fields, JSON_PRETTY_PRINT );

		try {
			$response = $this->client->request(
				'PUT',
				$this->apiPoint . $method,
				array(
					'body'    => $body,
					'headers' => $this->headers(),
				)
			);

			return $this->result( $response );

		} catch ( ClientException $e ) {

			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		}
	}

	/**
	 * Delete
	 *
	 * @param $method
	 * @param $fields
	 *
	 * @return array|mixed
	 */
	protected function delete( $method, $fields = array() ) {
		$body = wp_json_encode( $fields, JSON_PRETTY_PRINT );

		try {
			$response = $this->client->request(
				'DELETE',
				$this->apiPoint . $method,
				array(
					'body'    => $body,
					'headers' => $this->headers(),
				)
			);

			return $this->result( $response );

		} catch ( ClientException $e ) {

			return (array) json_decode( $e->getResponse()->getBody()->getContents() );
		}
	}

	/**
	 * Result
	 *
	 * @param Response $response
	 *
	 * @return mixed
	 */
	protected function result( Response $response ) {
		$result = json_decode( (string) $response->getBody(), true );

		$result['code'] = $response->getStatusCode();

		return $result;
	}
}
