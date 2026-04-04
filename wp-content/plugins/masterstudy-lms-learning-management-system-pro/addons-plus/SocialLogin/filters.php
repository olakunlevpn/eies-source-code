<?php

use MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Providers\Facebook;
use MasterStudy\Lms\Pro\AddonsPlus\SocialLogin\Providers\Google;

function masterstudy_lms_add_social_login_providers( $providers ) {
	$providers['google']   = new Google();
	$providers['facebook'] = new Facebook();

	return $providers;
}
add_filter( 'masterstudy_lms_social_login_providers', 'masterstudy_lms_add_social_login_providers' );
