<?php
namespace MasterStudy\Lms\Pro\AddonsPlus\SocialLogin;

use MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Providers\Properties;

abstract class Provider {
	protected $provider;
	protected $client;
	public $token    = array();
	public $settings = array();

	public function __construct() {
		$this->set_client();
	}

	abstract public function set_token_exchange_code( string $code );

	abstract public function get_auth_url();

	public function is_provider_setup(): bool {
		return $this->client && ( $this->settings['provider_id'] ?? false ) && ( $this->settings['provider_secret'] ?? false );
	}

	public function is_provider_enabled() {
		return $this->settings['is_enabled'] ?? null;
	}

	public function is_connected(): bool {
		return $this->is_provider_enabled() && $this->is_provider_setup();
	}

	public function register_user( array $user_data ) {
		$user = get_user_by( 'email', $user_data['email'] );

		if ( $user ) {
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );
		} elseif ( ! empty( $user_data['email'] ) && ! empty( $user_data['token'] ) ) {
			$password = wp_generate_password();
			$user_id  = wp_create_user( $user_data['email'], $password, $user_data['email'] );

			if ( ! is_wp_error( $user_id ) ) {
				update_user_meta( $user_id, 'display_name', sanitize_user( $user_data['name'] ) );
				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id );

				$user = get_user_by( 'id', $user_id );
			}
		}

		$redirect_url = esc_url( $_COOKIE['social_redirect_url'] ?? \STM_LMS_User::user_page_url() );
		wp_safe_redirect( $redirect_url );

		return $user ?? null;
	}

	public function get_user_data( string $provider_url ): array {
		$user_data = array(
			'email' => '',
			'name'  => '',
			'token' => array(),
		);
		$response  = wp_remote_get( $provider_url );

		if ( ! is_wp_error( $response ) ) {
			$response_body = wp_remote_retrieve_body( $response );
			$provider_data = json_decode( $response_body );

			if ( ! empty( $provider_data ) ) {
				$user_data = array(
					'email' => $provider_data->email ?? '',
					'name'  => $provider_data->name ?? '',
					'token' => is_array( $this->token ) ? $this->token : ( $this->token ?? '' ),
				);
			}
		}

		return $user_data;
	}

	protected function set_provider_configs() {
		$settings = get_option( 'stm_lms_settings', array() );

		foreach ( static::CONFIG as $key => $config ) {
			$this->settings[ $key ] = $settings[ $config['setting'] ] ?? $config['default'];
		}
	}
}
