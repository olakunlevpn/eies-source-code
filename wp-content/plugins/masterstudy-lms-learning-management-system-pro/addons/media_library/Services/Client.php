<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Services;

use Exception;

final class Client {
	/** @var string|null */
	private $auth_key;
	private string $request_url;

	public function __construct( $auth_key, $url ) {
		$this->auth_key    = $auth_key;
		$this->request_url = $url;
	}

	/**
	 * @param $method string method to call
	 * @param $endpoint string endpoint to call
	 * @param $params mixed query params
	 *
	 * @return array{
	 *     response: array,
	 *     data: array
	 * }
	 * @throws Exception
	 */
	public function request( $method, $endpoint, $params = null ): array {
		$query_string = add_query_arg( $params, $this->request_url . $endpoint );

		$cache_key  = $this->generate_cache_key( $method, $endpoint, $query_string );
		$cache_data = get_transient( $cache_key );

		if ( false !== $cache_data ) {
			return $cache_data;
		}

		$headers = array(
			'Authorization' => $this->auth_key,
		);

		$options = array(
			'headers'   => $headers,
			'method'    => $method,
			'timeout'   => 300,
			'sslverify' => false,
		);

		try {
			$response = wp_remote_request( $query_string, $options );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$data   = json_decode( wp_remote_retrieve_body( $response ), true );
			$result = array(
				'response' => $response,
				'data'     => $data,
			);

			if ( 200 !== $response['response']['code'] ) {
				throw new Exception( $response['response']['message'], $response['response']['code'] );
			}

			set_transient( $cache_key, $result, DAY_IN_SECONDS );

			return $result;
		} catch ( Exception $e ) {
			throw new Exception( 'Integration Error: ' . $e->getMessage() );
		}
	}

	private function generate_cache_key( $method, $endpoint, $params ): string {
		$hash = md5( $method . $endpoint . $params );

		return 'media_gallery_integration_cache_' . $hash;
	}

	public static function flush_all_cache() {
		global $wpdb;
		$wpdb->query(
			"DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_media_gallery_integration_cache_%' "
		);
	}

	public static function is_cache_exists(): bool {
		global $wpdb;
		$result = $wpdb->get_var(
			"SELECT option_name FROM $wpdb->options WHERE option_name LIKE '_transient_media_gallery_integration_cache_%' LIMIT 1"
		);

		return ! empty( $result );
	}

	public static function get_cache_size(): array {
		global $wpdb;

		$results = $wpdb->get_results(
			"SELECT option_name, CHAR_LENGTH(option_value) as size_bytes 
         FROM $wpdb->options 
         WHERE option_name LIKE '_transient_media_gallery_integration_cache_%'"
		);

		$total_size = 0;

		foreach ( $results as $result ) {
			$total_size += $result->size_bytes;
		}

		return array(
			'total_size_mb' => round( $total_size / ( 1024 * 1024 ), 2 ),
		);
	}
}
