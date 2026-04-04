<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Routing\Swagger\Frontend;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetAssignments extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'page'     => array(
				'type'        => 'integer',
				'description' => 'Pagination number in the query. Default is 1.',
			),
			'per_page' => array(
				'type'        => 'integer',
				'description' => 'Number of items to be shown per page in the query. Default is 10.',
			),
			'status'   => array(
				'type'        => 'string',
				'enum'        => array( 'pending', 'passed', 'not_passed' ),
				'description' => 'Filter assignments by status (pending, passed, not_passed). Default is empty.',
			),
			'search'   => array(
				'type'        => 'string',
				'description' => 'Filter assignments by assignment title. Default is empty.',
			),
		);
	}

	public function response(): array {
		return array(
			'page'        => array(
				'type' => 'integer',
			),
			'per_page'    => array(
				'type' => 'integer',
			),
			'max_pages'   => array(
				'type' => 'integer',
			),
			'found_posts' => array(
				'type' => 'integer',
			),
			'assignments' => array(
				'type' => 'array',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Assignments';
	}

	public function get_description(): string {
		return 'Returns a list of assignments based on the provided parameters.';
	}
}
