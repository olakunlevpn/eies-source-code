<?php
/** @var Router $router */

use MasterStudy\Lms\Routing\Router;

/**
 * Admin & Instructor routes
 */
$router->post(
	'/grades',
	\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\GetGradesController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetGrades::class
);

$router->get(
	'/grades/{user_course_id}',
	\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\GetCourseGradeController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetCourseGrade::class
);

$router->get(
	'/grades/{user_course_id}/regenerate',
	\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\RegenerateCourseGradeController::class,
	\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetCourseGrade::class
);

/**
 * Student routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Student::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/student-grade/{course_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\Student\GetCourseGradeController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetCourseGrade::class
		);
		$router->get(
			'/student-grade/{course_id}/regenerate',
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\Student\RegenerateCourseGradeController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetCourseGrade::class
		);
		$router->post(
			'/student-grades',
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\Student\GetGradesController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetGrades::class
		);
		$router->get(
			'/student-grades/{user_course_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\GetCourseGradeController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetCourseGrade::class
		);
		$router->get(
			'/student-grades/{user_course_id}/regenerate',
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\RegenerateCourseGradeController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger\GetCourseGrade::class
		);
	}
);
