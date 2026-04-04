<?php

namespace MasterStudy\Lms\Routing\Swagger\Routes\Certificates;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCertificates extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'       => array(
				'type'        => 'integer',
				'description' => 'Pagination number in the query. Default is 1.',
			),
			'per_page'   => array(
				'type'        => 'integer',
				'description' => 'Number of items to be shown per page in the query. Default is 10.',
			),
			'instructor' => array(
				'type'        => 'string',
				'description' => 'Filter certificates by instructor. Default is empty.',
			),
			'category'   => array(
				'type'        => 'string',
				'description' => 'Filter certificates by category. Default is empty.',
			),
			'search'     => array(
				'type'        => 'string',
				'description' => 'Filter assignments by assignment title. Default is empty.',
			),
		);
	}

	public function response(): array {
		return array(
			'page'         => array(
				'type' => 'integer',
			),
			'per_page'     => array(
				'type' => 'integer',
			),
			'max_pages'    => array(
				'type' => 'integer',
			),
			'found_posts'  => array(
				'type' => 'integer',
			),
			'certificates' => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Certificates';
	}

	public function get_description(): string {
		return 'Returns a list of certificates based on the provided parameters.';
	}
}
