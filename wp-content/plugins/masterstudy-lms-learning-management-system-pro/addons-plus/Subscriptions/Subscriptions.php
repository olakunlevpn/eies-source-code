<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions;

use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;
use MasterStudy\Lms\Plugin\Addons;

final class Subscriptions implements Addon {
	public function get_name(): string {
		//@TODO Remove condition
		return defined( 'Addons::SUBSCRIPTIONS' )
			? Addons::SUBSCRIPTIONS
			: 'subscriptions';
	}

	public function register( Plugin $plugin ): void {
		$plugin->load_file( __DIR__ . '/helpers.php' );
		$plugin->load_file( __DIR__ . '/actions.php' );
		$plugin->load_file( __DIR__ . '/filters.php' );

		$plugin->load_file( __DIR__ . '/Services/EmailDispatcher.php' );

		// Load Expiration Service
		$plugin->load_file( __DIR__ . '/Services/ExpirationService.php' );

		// Load Database Tables
		$plugin->load_file( __DIR__ . '/Database/subscription_plans.php' );
		$plugin->load_file( __DIR__ . '/Database/subscription_plan_items.php' );
		$plugin->load_file( __DIR__ . '/Database/subscriptions.php' );
		$plugin->load_file( __DIR__ . '/Database/subscription_meta.php' );
		$plugin->load_file( __DIR__ . '/Database/tables.php' );

		// Load Routes
		$plugin->get_router()->load_routes( __DIR__ . '/routes.php' );
	}
}
