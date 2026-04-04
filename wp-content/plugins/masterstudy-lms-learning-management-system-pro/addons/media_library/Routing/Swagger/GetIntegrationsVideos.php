<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Routing\Swagger;

use MasterStudy\Lms\Pro\addons\media_library\Routing\Swagger\Fields\Video;
use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class GetIntegrationsVideos extends Route implements RequestInterface, ResponseInterface {

	/**
	 * Response Schema Properties
	 * @return array
	 */
	public function request(): array {
		return array(
			'page'     => array(
				'type'        => 'integer',
				'description' => 'Request page',
			),
			'query'    => array(
				'type'        => 'string',
				'description' => 'Search query',
			),
			'per_page' => array(
				'type'        => 'integer',
				'description' => 'Photos per page',
			),
		);
	}

	/**
	 * Response Schema Properties
	 * @return array
	 */
	public function response(): array {
		return array(
			'success' => array(
				'type'        => 'boolean',
				'description' => 'Request status',
			),
			'total'   => array(
				'type'        => 'integer',
				'description' => 'Total rows',
			),
			'limit'   => array(
				'type'        => 'integer',
				'description' => 'Rows per page',
			),
			'data'    => Video::as_array(),
		);
	}

	/**
	 * Route Summary
	 * @return string
	 */
	public function get_summary(): string {
		return 'Get integration videos';
	}

	/**
	 * Route Description
	 * @return string
	 */
	public function get_description(): string {
		return 'Get or search for integration videos';
	}
}
