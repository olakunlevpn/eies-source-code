<?php
namespace MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Providers;

use MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Provider;

class Google extends Provider {
	public const CONFIG = array(
		'is_enabled'      => array(
			'setting' => 'social_login_google_enabled',
			'default' => false,
		),
		'provider_id'     => array(
			'setting' => 'social_login_google_client_id',
			'default' => '',
		),
		'provider_secret' => array(
			'setting' => 'social_login_google_client_secret',
			'default' => '',
		),
	);

	public function set_client() {
		$this->set_provider_configs();

		$this->client = new \Google_Client();

		if ( $this->is_provider_enabled() && $this->is_provider_setup() ) {
			$this->client->setClientId( $this->settings['provider_id'] );
			$this->client->setClientSecret( $this->settings['provider_secret'] );
			$this->client->setRedirectUri( site_url( '/?addon=social_login&provider=google' ) );
			$this->client->addScope( 'email' );
			$this->client->addScope( 'profile' );
			$this->client->setAccessType( 'offline' );
			$this->client->setApprovalPrompt( 'force' );
			$this->client->setIncludeGrantedScopes( true );
		}
	}

	public function set_token_exchange_code( string $code ) {
		try {
			if ( $this->client->isAccessTokenExpired() ) {
				$this->token = $this->client->fetchAccessTokenWithAuthCode( $code );
			} else {
				$this->token = $this->client->getAccessToken();
				$this->client->setAccessToken( $this->token );
			}

			$user_data = $this->get_user_data( 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $this->token['access_token'] );
			$user      = $this->register_user( $user_data );

			if ( $user instanceof \WP_User ) {
				$is_new_user = ( strtotime( $user->user_registered ) > time() - 10 );

				if ( $is_new_user && ! empty( $user_data['name'] ) ) {
					wp_update_user(
						array(
							'ID'           => $user->ID,
							'display_name' => $user_data['name'],
						)
					);
				}

				$current_first_name = get_user_meta( $user->ID, 'first_name', true );

				if ( empty( $current_first_name ) && ! empty( $user_data['name'] ) ) {
					update_user_meta( $user->ID, 'first_name', $user_data['name'] );
				}
			}

			return $this->token;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	public function get_auth_url() {
		if ( $this->is_provider_enabled() && $this->is_provider_setup() ) {
			return $this->client->createAuthUrl();
		}

		return null;
	}
}
