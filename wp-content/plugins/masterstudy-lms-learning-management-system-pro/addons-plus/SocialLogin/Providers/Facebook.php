<?php
namespace MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Providers;

use MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Provider;

class Facebook extends Provider {
	public const CONFIG = array(
		'is_enabled'      => array(
			'setting' => 'social_login_facebook_enabled',
			'default' => false,
		),
		'provider_id'     => array(
			'setting' => 'social_login_facebook_app_id',
			'default' => '',
		),
		'provider_secret' => array(
			'setting' => 'social_login_facebook_app_secret',
			'default' => '',
		),
	);

	public function set_client() {
		$this->set_provider_configs();

		$this->client = new \League\OAuth2\Client\Provider\Facebook(
			array(
				'clientId'        => $this->settings['provider_id'],
				'clientSecret'    => $this->settings['provider_secret'],
				'on_off'          => $this->settings['is_enabled'],
				'redirectUri'     => site_url( '/?addon=social_login&provider=facebook' ),
				'graphApiVersion' => 'v6.0',
			)
		);
	}

	public function set_token_exchange_code( string $code ) {
		try {
			$this->token = $this->client->getAccessToken( 'authorization_code', array( 'code' => $code ) );
			$user_data   = $this->get_user_data( 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $this->token );
			$user        = $this->register_user( $user_data );

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
			return $this->client->getAuthorizationUrl( array( 'scope' => array( 'email' ) ) );
		}

		return null;
	}
}
