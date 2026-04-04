<?php

use MasterStudy\Lms\Enums\LessonType;
use MasterStudy\Lms\Enums\LessonVideoType;

/**
 * Question Media rules to fill in the validation rules array
 */
function masterstudy_lms_question_media_validation_rules( array $rules ): array {
	$video_types = apply_filters( 'masterstudy_lms_lesson_video_types', array_map( 'strval', LessonVideoType::cases() ) );
	$media_rules = array(
		'video_type'        => 'required_if,type;' . LessonType::VIDEO . '|contains_list,' . implode( ';', $video_types ),
		'embed_ctx'         => 'nullable|string',
		'external_url'      => 'nullable|string',
		'presto_player_idx' => 'nullable|integer',
		'vdocipher_id'      => 'nullable|string',
		'shortcode'         => 'nullable|string',
		'youtube_url'       => 'nullable|string',
		'video'             => 'nullable|integer',
		'video_poster'      => 'nullable|integer',
		'video_width'       => 'nullable|integer|min,1',
		'vimeo_url'         => 'nullable|string',
	);

	return array_merge( $rules, $media_rules );
}
add_filter( 'masterstudy_lms_question_validation_rules', 'masterstudy_lms_question_media_validation_rules', 10, 1 );
