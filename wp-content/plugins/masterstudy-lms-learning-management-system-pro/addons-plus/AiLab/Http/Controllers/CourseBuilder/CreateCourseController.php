<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Models\Course;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Utility\Media;
use MasterStudy\Lms\Repositories\CourseRepository;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Repositories\CurriculumSectionRepository;
use MasterStudy\Lms\Repositories\FaqRepository;
use MasterStudy\Lms\Repositories\LessonRepository;
use MasterStudy\Lms\Repositories\QuizRepository;
use MasterStudy\Lms\Validation\Validator;

class CreateCourseController extends Controller {
	private CourseRepository $course_repository;
	private CurriculumSectionRepository $curriculum_section_repository;
	private CurriculumMaterialRepository $curriculum_material_repository;

	public function __construct() {
		$this->course_repository              = new CourseRepository();
		$this->curriculum_section_repository  = new CurriculumSectionRepository();
		$this->curriculum_material_repository = new CurriculumMaterialRepository();
	}

	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'title'             => 'required|string',
				'excerpt'           => 'nullable|string',
				'content'           => 'nullable|string',
				'image'             => 'required|string',
				'categories'        => 'nullable|array',
				'curriculum'        => 'required|array',
				'faq'               => 'required|array',
				'basic_info'        => 'nullable|string',
				'requirements'      => 'nullable|string',
				'intended_audience' => 'nullable|string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		// Upload Course Image
		$image = Media::create_attachment_from_url( $validated_data['image'], $validated_data['title'], true );
		if ( is_wp_error( $image ) ) {
			return WpResponseFactory::error( $image->get_error_message() );
		}

		$course_data = array(
			'title'    => $validated_data['title'],
			'slug'     => sanitize_title( $validated_data['title'] ),
			'image_id' => $image->ID,
			'category' => ! empty( $validated_data['categories'] )
				? array_column( $validated_data['categories'], 'id' )
				: array(),
		);

		$course_id = $this->course_repository->create( $course_data );
		$course    = $this->course_repository->find( $course_id );

		$this->course_repository->save( $this->fill_course_data( $course, $validated_data ) );

		// Save Curriculum
		foreach ( $validated_data['curriculum'] as $section ) {
			$created_section = $this->curriculum_section_repository->create(
				array(
					'title'     => $section['title'],
					'course_id' => $course_id,
				)
			);

			if ( isset( $section['materials'] ) ) {
				foreach ( $section['materials'] as $index => $material ) {
					$this->curriculum_material_repository->create(
						array(
							'post_id'    => $this->create_material_post( $material ),
							'section_id' => $created_section->id,
							'order'      => $index + 1,
						)
					);
				}
			}
		}

		// Save FAQ
		( new FaqRepository() )->save( $course_id, $validated_data['faq'] );

		return new \WP_REST_Response(
			array(
				'id' => $course_id,
			)
		);
	}

	private function fill_course_data( Course $course, array $data ): Course {
		$course->excerpt = $data['excerpt'] ?? '';
		$course->content = $data['content'] ?? '';

		if ( isset( $data['basic_info'] ) ) {
			$course->basic_info = $data['basic_info'];
		}

		if ( isset( $data['requirements'] ) ) {
			$course->requirements = $data['requirements'];
		}

		if ( isset( $data['intended_audience'] ) ) {
			$course->intended_audience = $data['intended_audience'];
		}

		return $course;
	}

	private function create_material_post( array $material ): int {
		switch ( $material['post_type'] ) {
			case PostType::LESSON:
				$material['type'] = $material['lesson_type'] ?? LessonType::TEXT;

				return ( new LessonRepository() )->create( $material );
			case PostType::QUIZ:
				return ( new QuizRepository() )->create( $material );
			case PostType::ASSIGNMENT:
				return ( new AssignmentRepository() )->create( $material );
		}

		return 0;
	}
}
