<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\SocialLogin;

class SocialLoginManager {
	public static function get_provider( string $provider ) {
		$providers = apply_filters( 'masterstudy_lms_social_login_providers', array() );

		if ( ! empty( $providers[ $provider ] ) ) {
			return $providers[ $provider ];
		}

		return null;
	}
}
