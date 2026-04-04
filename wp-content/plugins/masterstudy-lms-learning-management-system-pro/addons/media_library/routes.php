<?php

use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers;
use MasterStudy\Lms\Pro\addons\media_library\Routing\Swagger;

use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Unsplash;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Pexels;
use MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Pixabay;

/** @var \MasterStudy\Lms\Routing\Router $router */

$router->get( '/media-file-manager', Controllers\GetAllController::class, Swagger\GetAll::class );
$router->post( '/media-file-manager', Controllers\UploadController::class, Swagger\Upload::class );
$router->get( '/media-file-manager/{id}', Controllers\GetByIdController::class, Swagger\GetById::class );
$router->delete( '/media-file-manager/{id}', Controllers\DeleteController::class, Swagger\Delete::class );

if ( STM_LMS_Helpers::is_pro_plus() ) {
	$router->get( '/media-file-manager/integration/unsplash/photos', Unsplash\GetPhotosController::class, Swagger\GetIntegrationsPhotos::class );
	$router->get( '/media-file-manager/integration/pexels/photos', Pexels\GetPhotosController::class, Swagger\GetIntegrationsPhotos::class );
	$router->get( '/media-file-manager/integration/pexels/videos', Pexels\GetVideosController::class, Swagger\GetIntegrationsVideos::class );
	$router->get( '/media-file-manager/integration/pixabay/photos', Pixabay\GetPhotosController::class, Swagger\GetIntegrationsPhotos::class );
	$router->get( '/media-file-manager/integration/pixabay/videos', Pixabay\GetVideosController::class, Swagger\GetIntegrationsVideos::class );
}
