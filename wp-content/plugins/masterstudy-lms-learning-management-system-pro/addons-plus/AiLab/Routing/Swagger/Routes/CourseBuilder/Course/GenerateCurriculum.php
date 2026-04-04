<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Swagger\Routes\CourseBuilder\Course;

use MasterStudy\Lms\Routing\Swagger\RequestInterface;
use MasterStudy\Lms\Routing\Swagger\ResponseInterface;
use MasterStudy\Lms\Routing\Swagger\Route;

class GenerateCurriculum extends Route implements RequestInterface, ResponseInterface {
	public function request(): array {
		return array(
			'prompt'             => array(
				'type'        => 'string',
				'description' => 'Prompt message for course generation.',
			),
			'language'           => array(
				'type'        => 'string',
				'description' => 'Language of the course.',
			),
			'sections_count'     => array(
				'type'        => 'integer',
				'description' => 'Sections count.',
			),
			'materials_count'    => array(
				'type'        => 'integer',
				'description' => 'Materials count for each section.',
			),
			'quizzes_count'      => array(
				'type'        => 'integer',
				'description' => 'Quizzes count. NULL if no quizzes are required. 1 if last section must have a final quiz. 2 if all sections must have a quiz.',
			),
			'create_assignments' => array(
				'type'        => 'boolean',
				'description' => 'Create assignments.',
			),
			'lesson_types'       => array(
				'type'        => 'array',
				'description' => 'Lesson types.',
			),
		);
	}

	public function response(): array {
		return array(
			'data' => array(
				'type'        => 'array',
				'description' => 'Course curriculum.',
				'properties'  => array(
					'title'     => array(
						'type'        => 'string',
						'description' => 'Section title.',
					),
					'materials' => array(
						'type'        => 'array',
						'description' => 'Materials.',
						'properties'  => array(
							'title'       => array(
								'type'        => 'string',
								'description' => 'Material title.',
							),
							'post_type'   => array(
								'type'        => 'string',
								'description' => 'Material post type.',
							),
							'lesson_type' => array(
								'type'        => 'string',
								'description' => 'Material lesson type.',
							),
						),
					),
				),
			),
		);
	}

	public function get_summary(): string {
		return 'Generate a course curriculum.';
	}

	public function get_description(): string {
		return 'Generate Course Curriculum.';
	}
}
