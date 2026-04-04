<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

$router->middleware(
	apply_filters(
		'masterstudy_lms_routes_middleware',
		array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		)
	)
);

$router->get(
	'/lesson/markers/get/{lesson_id}',
	\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\Markers\GetController::class,
	\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers\Get::class
);
$router->post(
	'/lesson/markers/create/{lesson_id}',
	\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\VideoQuestions\CreateController::class,
	\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers\CreateQuestions::class
);
$router->put(
	'/lesson/markers/update/{marker_id}',
	\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\Markers\UpdateController::class,
	\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers\Update::class
);
$router->delete(
	'/lesson/markers/delete/{lesson_id}/{marker_id}',
	\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\Markers\DeleteController::class,
	\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers\Delete::class
);

$router->put(
	'/lesson/markers/lock/{lesson_id}',
	\MasterStudy\Lms\Pro\RestApi\Http\Controllers\Lessons\VideoQuestions\UpdateQuestionsLockController::class,
	\MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Lesson\Markers\UpdateQuestionsLock::class
);
