<?php
// phpcs:ignoreFile

namespace Zoom\Contracts;

class S2SOAuth {
	public static $instance = null;

	public static function get_instance() {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function generateAccessToken( $account_id, $client_id, $client_secret ) {
		if ( empty( $account_id ) ) {
			return new \WP_Error( 'Redirect Url', 'Redirect Url is missing' );
		} elseif ( empty( $client_id ) ) {
			return new \WP_Error( 'Client ID', 'Client ID is missing' );
		} elseif ( empty( $client_secret ) ) {
			return new \WP_Error( 'Client Secret', 'Client Secret is missing' );
		}

		$base64Encoded = base64_encode( $client_id . ':' . $client_secret );
		$result        = new \WP_Error( 0, 'Something went wrong' );
		$args          = array(
			'method'  => 'POST',
			'headers' => array(
				'Authorization' => "Basic $base64Encoded",
			),
			'body'    => array(
				'grant_type' => 'account_credentials',
				'account_id' => $account_id,
			),
		);

		$request_url      = 'https://zoom.us/oauth/token';
		$response         = wp_remote_post( $request_url, $args );
		$responseCode     = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		if ( 200 == $responseCode && 'ok' == strtolower( $response_message ) ) {
			$responseBody          = wp_remote_retrieve_body( $response );
			$decoded_response_body = json_decode( $responseBody );
			if ( isset( $decoded_response_body->access_token ) && ! empty( $decoded_response_body->access_token ) ) {
				$result = $decoded_response_body;
			} elseif ( isset( $decoded_response_body->errorCode ) && ! empty( $decoded_response_body->errorCode ) ) {
				$result = new \WP_Error( $decoded_response_body->errorCode, $decoded_response_body->errorMessage );
			}
		} else {
			$result = new \WP_Error( $responseCode, $response_message );
		}

		return $result;
	}


	public function generateAndSaveAccessToken( $account_id, $client_id, $client_secret ) {
		$token = get_transient( 'mslms_global_oauth_data' );
		
		if ( empty( $token ) && ! empty( $account_id ) && ! empty( $client_id ) && ! empty( $client_secret ) ) {
			$token = $this->generateAccessToken( $account_id, $client_id, $client_secret );
			
			if ( ! is_wp_error( $token ) ) {
				set_transient( 'mslms_global_oauth_data', $token, 60 * 60 );
			}
		}

		return $token;
	}

	public function regenerateAccessTokenAndSave() {
		$settings      = get_option( 'ms_zoom_settings', array() );
		$account_id    = $settings['auth_account_id'] ?? '';
		$client_id     = $settings['auth_client_id'] ?? '';
		$client_secret = $settings['auth_client_secret'] ?? '';

		$result = $this->generateAndSaveAccessToken( $account_id, $client_id, $client_secret );

		if ( is_wp_error( $result ) ) {
			new \WP_Error( '', $result );
		}

		return $result;
	}
}
