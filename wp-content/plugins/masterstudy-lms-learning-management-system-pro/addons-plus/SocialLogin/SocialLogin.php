<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\SocialLogin;

use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;
use MasterStudy\Lms\Plugin\Addons;

final class SocialLogin implements Addon {
	public function get_name(): string {
		//@TODO Remove condition
		return defined( 'Addons::SOCIAL_LOGIN' )
			? Addons::SOCIAL_LOGIN
			: 'social_login';
	}

	public function register( Plugin $plugin ): void {
		$plugin->load_file( __DIR__ . '/actions.php' );
		$plugin->load_file( __DIR__ . '/filters.php' );
	}
}
