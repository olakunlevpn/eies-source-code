<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Routing\Swagger;

use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

final class GetCourseGrade extends Route implements ResponseInterface {
	public function response(): array {
		return array(
			'course'               => array(
				'type'        => 'string',
				'description' => 'Course Title',
			),
			'student'              => array(
				'type'        => 'string',
				'description' => 'Student Name',
			),
			'enroll_date'          => array(
				'type'        => 'string',
				'description' => 'Enroll Date',
			),
			'course_complete_date' => array(
				'type'        => 'string',
				'description' => 'Course Complete Date',
			),
			'grade'                => array(
				'type'        => 'object',
				'properties'  => array(
					'badge'     => array(
						'type'        => 'string',
						'description' => 'Grade Badge',
					),
					'current'   => array(
						'type'        => 'integer',
						'description' => 'Current Grade',
					),
					'max_point' => array(
						'type'        => 'integer',
						'description' => 'Max Point',
					),
					'range'     => array(
						'type'        => 'integer',
						'description' => 'Grade Range',
					),
					'color'     => array(
						'type'        => 'string',
						'description' => 'Grade Color',
					),
				),
				'description' => 'Grade Details',
			),
			'exams'                => array(
				'type'        => 'array',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'type'     => array(
							'type'        => 'string',
							'description' => 'Exam Type',
						),
						'title'    => array(
							'type'        => 'string',
							'description' => 'Exam Title',
						),
						'attempts' => array(
							'type'        => 'integer',
							'description' => 'Exam Attempts',
						),
						'grade'    => array(
							'type'        => 'object',
							'properties'  => array(
								'badge'     => array(
									'type'        => 'string',
									'description' => 'Grade Badge',
								),
								'current'   => array(
									'type'        => 'integer',
									'description' => 'Current Grade',
								),
								'max_point' => array(
									'type'        => 'integer',
									'description' => 'Max Point',
								),
								'range'     => array(
									'type'        => 'integer',
									'description' => 'Grade Range',
								),
								'color'     => array(
									'type'        => 'string',
									'description' => 'Grade Color',
								),
							),
							'description' => 'Grade Details',
						),
					),
				),
				'description' => 'Exam Details',
			),
		);
	}

	public function get_summary(): string {
		return 'Get Course Grade';
	}

	public function get_description(): string {
		return 'Get User Course details & exam grades';
	}
}
