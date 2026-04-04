<?php

namespace MasterStudy\Lms\Pro\RestApi\Routing\Middleware;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Routing\MiddlewareInterface;

/**
 * Checks if user can access to the post
 */
final class Student implements MiddlewareInterface {
	public function process( $request, callable $next ) {
		$course_id      = $this->get_source_id( $request );
		$user_course_id = $this->get_source_id( $request, array( 'user_course_id' ) );

		// Skip middleware if course_id and user_course_id are not provided
		if ( null === $course_id && null === $user_course_id ) {
			return $next( $request );
		}

		// Get course id by user course id
		if ( ! empty( $user_course_id ) ) {
			$course_id = function_exists( 'stm_lms_get_course_id_by_user_course_id' )
				? stm_lms_get_course_id_by_user_course_id( $user_course_id )
				: null;
		}

		// Check if user has access to the course
		if ( ! empty( $course_id ) && \STM_LMS_User::has_course_access( $course_id ) ) {
			return $next( $request );
		}

		return WpResponseFactory::forbidden();
	}

	private function get_source_id( $request, array $params = array() ): ?int {
		$url_params = $request->get_url_params();
		$params     = ! empty( $params ) ? $params : array( 'course_id', 'id' );

		foreach ( $params as $param_name ) {
			if ( array_key_exists( $param_name, $url_params ) ) {
				return (int) $url_params[ $param_name ];
			}
		}

		return null;
	}
}
