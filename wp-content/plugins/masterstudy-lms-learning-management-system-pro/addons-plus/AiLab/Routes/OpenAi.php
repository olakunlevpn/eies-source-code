<?php
/** @var Router $router */

use MasterStudy\Lms\Routing\Router;

// OpenAI Routes, commonly for Fine-Tuning
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Pro\RestApi\Routing\Middleware\Administrator::class, // Admins only
		),
		'prefix'     => '/openai',
	),
	function ( Router $router ) {
		$router->get(
			'/files',
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi\GetFilesController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi\GetFiles::class,
		);
		$router->post(
			'/files',
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi\UploadFileController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi\UploadFile::class,
		);
		$router->get(
			'/files/{file_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi\GetFileController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi\GetFile::class,
		);
		$router->delete(
			'/files/{file_id}',
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\OpenAi\DeleteFileController::class,
			\MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\OpenAi\DeleteFile::class,
		);
	}
);
