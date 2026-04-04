<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers;

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Serializers\CourseGradeSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Repositories\GradesRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use WP_REST_Response;

class RegenerateCourseGradeController extends Controller {
	public function __invoke( int $user_course_id ): WP_REST_Response {
		$grades_repository = new GradesRepository();

		$grades_repository->regenerate_user_course_grade( $user_course_id );

		return new WP_REST_Response(
			( new CourseGradeSerializer() )->toArray(
				$grades_repository->get_user_course_grade( $user_course_id )
			)
		);
	}
}
