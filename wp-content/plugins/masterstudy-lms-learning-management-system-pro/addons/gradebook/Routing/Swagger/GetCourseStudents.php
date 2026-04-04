<?php

namespace MasterStudy\Lms\Pro\addons\gradebook\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetCourseStudents extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'start'     => array(
				'type'        => 'integer',
				'description' => 'Students offset start',
			),
			'length'    => array(
				'type'        => 'integer',
				'description' => 'Students per page',
			),
			'order'     => array(
				'type'        => 'array',
				'description' => 'Students ordered by',
			),
			'columns'   => array(
				'type'        => 'array',
				'description' => 'Students table columns',
			),
			'course_id' => array(
				'type'        => 'integer',
				'description' => 'Filter students by course id',
			),
		);
	}

	public function response(): array {
		return array(
			'data'            => array(
				'type'  => 'array',
				'items' => array(),
			),
			'recordsTotal'    => array( 'type' => 'integer' ),
			'recordsFiltered' => array( 'type' => 'integer' ),
		);
	}

	public function get_summary(): string {
		return 'Get Students for the course';
	}

	public function get_description(): string {
		return 'Returns a list of students based on the course id.';
	}
}
