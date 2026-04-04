<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Routing\Middleware;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Routing\MiddlewareInterface;
use STM_LMS_Instructor;

class HasAiAccess implements MiddlewareInterface {
	public function process( $request, callable $next ) {
		$current_user = wp_get_current_user();

		// Allow administrators to access all routes
		if ( in_array( 'administrator', $current_user->roles, true ) ) {
			return $next( $request );
		}

		// Allow instructors to access AI features
		if ( in_array( 'stm_lms_instructor', $current_user->roles, true )
			&& STM_LMS_Instructor::has_ai_access( $current_user->ID ) ) {
			return $next( $request );
		}

		return WpResponseFactory::forbidden();
	}
}
