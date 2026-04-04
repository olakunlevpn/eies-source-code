<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Analytics\Bundle;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetBundleCourses extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'course_id'    => array(
				'type'        => 'integer',
				'description' => 'Course Id',
			),
			'course_name'  => array(
				'type'        => 'string',
				'description' => 'Course name',
			),
			'date_created' => array(
				'type'        => 'date',
				'description' => 'Created date of that course',
			),
			'enrollments'  => array(
				'type'        => 'integer',
				'description' => 'Course Enrollments',
			),
		);
	}

	/**
	 * Route Summary
	 */
	public function get_summary(): string {
		return 'Get Bundles Data';
	}

	/**
	 * Route Description
	 */
	public function get_description(): string {
		return 'Get Bundles Courses Data';
	}
}
