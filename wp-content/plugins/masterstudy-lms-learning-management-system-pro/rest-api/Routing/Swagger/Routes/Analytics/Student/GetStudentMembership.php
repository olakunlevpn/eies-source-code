<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Swagger\Routes\Analytics\Student;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GetStudentMembership extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'name'       => array(
				'type'        => 'string',
				'description' => 'Student name.',
			),
			'price'      => array(
				'type'        => 'string',
				'description' => 'Plan price',
			),
			'subscribed' => array(
				'type'        => 'string',
				'description' => 'Date subscribed.',
			),
			'cancelled'  => array(
				'type'        => 'string',
				'description' => 'Date cancelled',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Student Memberships history';
	}

	public function get_description(): string {
		return 'Returns Students Memberships history data.';
	}
}
