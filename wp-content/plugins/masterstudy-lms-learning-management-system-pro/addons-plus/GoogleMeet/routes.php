<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->post(
	'/google-meets',
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers\CreateController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger\Create::class,
);

$router->get(
	'/google-meets/{meeting_id}',
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers\GetController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger\Get::class,
);

$router->put(
	'/google-meets/{meeting_id}',
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers\UpdateController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger\Update::class,
);

$router->delete(
	'/google-meets/{meeting_id}',
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers\DeleteController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger\Delete::class,
);

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Instructor::class,
		),
	),
	function ( Router $router ) {
		$router->post(
			'/google-meets/list',
			\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers\ListController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Routing\Swagger\ListMeetings::class
		);
	}
);
