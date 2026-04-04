<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet;

use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;
use MasterStudy\Lms\Plugin\Addons;

final class GoogleMeet implements Addon {
	public function get_name(): string {
		return Addons::GOOGLE_MEET;
	}

	public function register( Plugin $plugin ): void {
		$plugin->get_router()->load_routes( __DIR__ . '/routes.php' );

		$plugin->load_file( __DIR__ . '/helpers.php' );
		$plugin->load_file( __DIR__ . '/actions.php' );
		$plugin->load_file( __DIR__ . '/ajax-actions.php' );
		$plugin->load_file( __DIR__ . '/filters.php' );
		$plugin->load_file( __DIR__ . '/Settings/main.php' );
	}
}
