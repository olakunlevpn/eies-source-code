<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/coupon',
	),
	function ( Router $router ) {
		$router->get(
			'/list',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\ListController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\ListRoute::class,
		);
		$router->get(
			'/{coupon_id}',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\GetController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\Get::class,
		);
		$router->post(
			'/create',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\CreateController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\Create::class
		);
		$router->put(
			'/{coupon_id}',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\UpdateController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\Update::class,
		);
		$router->put(
			'/{coupon_id}/status',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\UpdateStatusController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\UpdateStatus::class,
		);
		$router->delete(
			'/{coupon_id}',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\DeleteController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\Delete::class
		);
		$router->post(
			'/bulk-update',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\BulkUpdateController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\BulkUpdate::class
		);
	}
);

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Guest::class,
		),
		'prefix'     => '/coupon',
	),
	function ( Router $router ) {
		$router->post(
			'/apply-cart-coupon',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\ApplyCartController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\ApplyCart::class
		);
		$router->post(
			'/remove-cart-coupon',
			\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons\RemoveCartController::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Coupons\RemoveCart::class
		);
	}
);
