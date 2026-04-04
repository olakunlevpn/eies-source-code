<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers\Student;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Serializers\CourseGradeSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Repositories\GradesRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use WP_REST_Response;

class GetCourseGradeController extends Controller {
	public function __invoke( int $course_id ): WP_REST_Response {
		$user_course = class_exists( 'STM_LMS_Course' )
			? \STM_LMS_Course::get_user_course( get_current_user_id(), $course_id )
			: false;

		if ( empty( $user_course ) ) {
			return WpResponseFactory::not_found();
		}

		return new WP_REST_Response(
			( new CourseGradeSerializer() )->toArray(
				( new GradesRepository() )->get_user_course_grade( intval( $user_course['user_course_id'] ) )
			)
		);
	}
}
