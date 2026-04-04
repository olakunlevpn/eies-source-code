<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\Course;

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Http\Serializers\AbstractSerializer;
use MasterStudy\Lms\Plugin\PostType;

final class CurriculumSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		$section      = ! empty( $data['section'] ) ? $data['section'] : $data;
		$lesson_types = apply_filters( 'masterstudy_lms_lesson_types', array_map( 'strval', LessonType::cases() ) );
		$post_types   = array(
			PostType::LESSON,
			PostType::ASSIGNMENT,
			PostType::QUIZ,
		);

		return array(
			'title'     => $section['title'] ?? 'Section',
			'materials' => array_map(
				function( $material ) use ( $lesson_types, $post_types ) {
					$filtered_material = array(
						'title'     => $material['title'] ?? '',
						'post_type' => in_array( $material['post_type'], $post_types, true )
							? $material['post_type']
							: PostType::LESSON,
					);

					if ( ! empty( $material['lesson_type'] ) ) {
						$filtered_material['lesson_type'] = in_array( $material['lesson_type'], $lesson_types, true )
							? $material['lesson_type']
							: LessonType::TEXT;
					} else {
						$filtered_material['lesson_type'] = '';
					}

					return $filtered_material;
				},
				$section['materials'] ?? array()
			),
		);
	}
}
