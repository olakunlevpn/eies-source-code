<?php

use MasterStudy\Lms\Routing\Router;

/**
 * Public routes for Pro version
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Guest::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/instructor-co-owned-courses',
			\MasterStudy\Lms\Pro\addons\MultiInstructors\Http\Controllers\GetInstructorCoOwnedCourses::class,
			\MasterStudy\Lms\Pro\addons\MultiInstructors\Routing\Swagger\GetInstructorCoOwnedCourses::class
		);
	}
);

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/instructor-co-courses',
			\MasterStudy\Lms\Pro\addons\MultiInstructors\Http\Controllers\GetInstructorCoCoursesController::class,
			\MasterStudy\Lms\Pro\addons\MultiInstructors\Routing\Swagger\GetInstructorCoCourses::class
		);
	}
);
