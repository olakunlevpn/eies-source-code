<?php

use MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\SocialLoginManager;

function masterstudy_lms_social_login_init() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['addon'], $_GET['provider'], $_GET['code'] ) && 'social_login' === $_GET['addon'] ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$provider = SocialLoginManager::get_provider( sanitize_text_field( $_GET['provider'] ) );

		if ( $provider ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$provider->set_token_exchange_code( sanitize_text_field( $_GET['code'] ) );
		}
	}
}
add_action( 'init', 'masterstudy_lms_social_login_init' );
