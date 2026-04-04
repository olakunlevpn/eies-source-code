<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class UploadImage extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'image' => array(
				'type' => 'string',
			),
		);
	}

	public function response(): array {
		return array(
			'id'       => array(
				'type' => 'integer',
			),
			'title'    => array(
				'type' => 'string',
			),
			'url'      => array(
				'type' => 'string',
			),
			'type'     => array(
				'type' => 'string',
			),
			'date'     => array(
				'type' => 'string',
			),
			'modified' => array(
				'type' => 'string',
			),
			'size'     => array(
				'type' => 'string',
			),
		);
	}

	public function get_summary(): string {
		return 'Upload an image to the Media Library';
	}

	public function get_description(): string {
		return 'Upload an image to the Media Library';
	}
}
