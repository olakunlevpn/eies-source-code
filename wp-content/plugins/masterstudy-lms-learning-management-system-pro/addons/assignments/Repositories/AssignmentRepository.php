<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Repositories\AbstractRepository;

final class AssignmentRepository extends AbstractRepository {
	protected static array $fields_post_map = array(
		'title'   => 'post_title',
		'content' => 'post_content',
	);

	protected static array $fields_meta_map = array(
		'attempts'           => 'assignment_tries',
		'passing_grade'      => 'passing_grade',
		'time_limit_unit'    => 'assignment_time_limit_unit',
		'time_limit'         => 'assignment_time_limit',
		'retake_limit_reset' => 'assignment_retake_limit_reset',
	);

	protected static array $casts = array(
		'attempts'           => 'int|nullable',
		'passing_grade'      => 'int|nullable',
		'time_limit'         => 'float|nullable',
		'retake_limit_reset' => 'bool|nullable',
	);

	protected static string $post_type = PostType::ASSIGNMENT;
}
