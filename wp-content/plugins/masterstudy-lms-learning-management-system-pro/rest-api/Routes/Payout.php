<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

// Administrator and Instructor routes
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/payout',
	),
	function ( Router $router ) {
		$router->post(
			'/orders',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Payout\GetPayoutOrdersController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Payout\GetPayoutOrders::class,
		);
		$router->get(
			'/revenue',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Payout\GetPayoutRevenueController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Payout\GetPayoutRevenue::class,
		);
	}
);
