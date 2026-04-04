<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers;

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Serializers\CourseGradeSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Repositories\GradesRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use WP_REST_Response;

class GetCourseGradeController extends Controller {
	public function __invoke( int $user_course_id ): WP_REST_Response {
		return new WP_REST_Response(
			( new CourseGradeSerializer() )->toArray(
				( new GradesRepository() )->get_user_course_grade( $user_course_id )
			)
		);
	}
}
