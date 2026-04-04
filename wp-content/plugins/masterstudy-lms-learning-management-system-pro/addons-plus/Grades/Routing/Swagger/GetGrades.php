<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class GetGrades extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'start'   => array(
				'type'        => 'integer',
				'description' => 'Start',
			),
			'length'  => array(
				'type'        => 'integer',
				'description' => 'Length',
			),
			'search'  => array(
				'type'        => 'array',
				'description' => 'Search',
				'items'       => array(
					'type'        => 'array',
					'description' => 'Search',
					'items'       => array(
						'type'        => 'string',
						'description' => 'Search',
					),
				),
			),
			'columns' => array(
				'type'        => 'array',
				'description' => 'Columns',
				'items'       => array(
					'type'        => 'array',
					'description' => 'Columns',
					'items'       => array(
						'type'        => 'string',
						'description' => 'Columns',
					),
				),
			),
			'order'   => array(
				'type'        => 'array',
				'description' => 'Order',
				'items'       => array(
					'type'        => 'array',
					'description' => 'Order',
					'items'       => array(
						'type'        => 'string',
						'description' => 'Order',
					),
				),
			),
		);
	}

	public function response(): array {
		return array(
			'recordsTotal'    => array(
				'type'        => 'integer',
				'description' => 'Records Total',
			),
			'recordsFiltered' => array(
				'type'        => 'integer',
				'description' => 'Records Filtered',
			),
			'data'            => array(
				'type'        => 'array',
				'description' => 'User Courses Data',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Grades';
	}

	public function get_description(): string {
		return 'Get User Courses for Grades';
	}
}
