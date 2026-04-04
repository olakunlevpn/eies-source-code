<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/gradebook',
	),
	function ( Router $router ) {
		$router->post(
			'/students/list',
			\MasterStudy\Lms\Pro\addons\gradebook\Http\Controllers\GetCourseStudentsController::class,
			\MasterStudy\Lms\Pro\addons\gradebook\Routing\Swagger\GetCourseStudents::class
		);
	}
);
