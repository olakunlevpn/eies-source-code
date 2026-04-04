<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades;

use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;
use MasterStudy\Lms\Plugin\Addons;

final class Grades implements Addon {
	public function get_name(): string {
		//@TODO Remove condition
		return defined( 'Addons::GRADES' )
			? Addons::GRADES
			: 'grades';
	}

	public function register( Plugin $plugin ): void {
		$plugin->load_file( __DIR__ . '/helpers.php' );
		$plugin->load_file( __DIR__ . '/actions.php' );
		$plugin->load_file( __DIR__ . '/filters.php' );

		$plugin->get_router()->load_routes( __DIR__ . '/routes.php' );
	}
}
