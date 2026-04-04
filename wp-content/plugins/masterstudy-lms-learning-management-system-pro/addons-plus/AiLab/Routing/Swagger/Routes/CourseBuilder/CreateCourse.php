<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class CreateCourse extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'title'             => array(
				'type'        => 'string',
				'description' => 'Course title.',
			),
			'excerpt'           => array(
				'type'        => 'string',
				'description' => 'Course excerpt.',
			),
			'content'           => array(
				'type'        => 'string',
				'description' => 'Course content.',
			),
			'image'             => array(
				'type'        => 'string',
				'description' => 'Course image URL.',
			),
			'categories'        => array(
				'type'        => 'array',
				'description' => 'Course categories.',
				'required'    => false,
			),
			'curriculum'        => array(
				'type'        => 'array',
				'description' => 'Course curriculum.',
			),
			'faq'               => array(
				'type'        => 'array',
				'description' => 'Course FAQ.',
				'properties'  => array(
					'question' => array(
						'type'        => 'string',
						'description' => 'Question.',
					),
					'answer'   => array(
						'type'        => 'string',
						'description' => 'Answer.',
					),
				),
			),
			'basic_info'        => array(
				'type'        => 'string',
				'description' => 'Course basic info. Optional.',
				'required'    => false,
			),
			'requirements'      => array(
				'type'        => 'string',
				'description' => 'Course requirements. Optional.',
				'required'    => false,
			),
			'intended_audience' => array(
				'type'        => 'string',
				'description' => 'Course intended audience. Optional.',
				'required'    => false,
			),
		);
	}

	public function response(): array {
		return array(
			'id' => array(
				'type'        => 'integer',
				'description' => 'Course ID.',
			),
		);
	}

	public function get_summary(): string {
		return 'Create a course';
	}

	public function get_description(): string {
		return 'Create a course from AI generated data';
	}
}
