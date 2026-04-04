<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services;

use Google_Client;
use Google_Service_Calendar;

class GoogleOpenAuth {
	public const TOKEN_NAME  = 'stm_lms_google_meet_token';
	public const CONFIG_NAME = 'stm_lms_google_meet_config';
	private $client;

	public function __construct() {
		$this->set_client();
	}

	public function set_client() {
		$this->client = new Google_Client();
		$auth_config  = get_option( self::CONFIG_NAME, array() );

		if ( ! empty( $auth_config ) ) {
			$this->client->setAuthConfig( $auth_config );
			$this->client->setAccessType( 'offline' );
			$this->client->setApprovalPrompt( 'force' );
			$this->client->setIncludeGrantedScopes( true );
			$this->client->addScope( Google_Service_Calendar::CALENDAR, Google_Service_Calendar::CALENDAR_EVENTS );
		}
	}

	public function process_auth_code( $code ) {
		try {
			$token = $this->client->fetchAccessTokenWithAuthCode( $code );
		} catch ( \Throwable $error ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Google Meet auth token fetch failed: ' . $error->getMessage() );
			$this->redirect_to_google_meet_page();
			return;
		}

		if ( ! is_array( $token ) || isset( $token['error'] ) || empty( $token['access_token'] ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Google Meet auth token response error: ' . wp_json_encode( $token ) );
			$this->redirect_to_google_meet_page();
			return;
		}

		try {
			update_option( self::TOKEN_NAME, $token );
			$this->client->setAccessToken( $token );
			$token = $this->client->getAccessToken();
			update_user_meta( get_current_user_id(), self::TOKEN_NAME, $token );
		} catch ( \Throwable $error ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Google Meet access token set failed: ' . $error->getMessage() );
		}

		$this->redirect_to_google_meet_page();
	}

	public function get_consent_screen_url() {
		return $this->client->createAuthUrl();
	}

	private function redirect_to_google_meet_page() {
		if ( current_user_can( 'administrator' ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=google_meet_settings' ) );
		} else {
			wp_safe_redirect( ms_plugin_user_account_url( 'google-meets' ) );
		}
	}
}
