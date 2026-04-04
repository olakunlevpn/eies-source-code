<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Services;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\CurriculumMaterialRepository;
use MasterStudy\Lms\Repositories\CurriculumSectionRepository;

class CourseDataCache {
	private static $instance = null;
	private array $cache     = array();

	private function __construct() {}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_course_data( $course_id, $featured_image_id = null ) {
		if ( isset( $this->cache[ $course_id ] ) ) {
			return $this->cache[ $course_id ];
		}

		$section_ids = ( new CurriculumSectionRepository() )->get_course_section_ids( $course_id );
		$materials   = new CurriculumMaterialRepository();

		$data = array(
			'assignments' => $materials->count_by_type( $section_ids, PostType::ASSIGNMENT ),
			'quizzes'     => $materials->count_by_type( $section_ids, PostType::QUIZ ),
			'image'       => ! empty( $featured_image_id )
				? esc_url( wp_get_attachment_image_url( $featured_image_id ) )
				: esc_url( STM_LMS_URL . 'assets/img/image_not_found.png' ),
		);

		$this->cache[ $course_id ] = $data;

		return $data;
	}
}
