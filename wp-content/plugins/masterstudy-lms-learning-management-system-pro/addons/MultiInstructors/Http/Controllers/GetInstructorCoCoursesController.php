<?php

namespace MasterStudy\Lms\Pro\addons\MultiInstructors\Http\Controllers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\MultiInstructors\Repository\MultiInstructorsRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

class GetInstructorCoCoursesController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page'     => 'required|integer',
				'per_page' => 'required|integer',
				'user'     => 'required|integer',
				'render'   => 'string',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated = $validator->get_validated();

		$per_page = (int) ( $validated['per_page'] ?? 6 );
		$page     = (int) ( $validated['page'] ?? 1 );
		$render   = (string) ( $validated['render'] ?? 'json' );
		$user_id  = (int) ( $validated['user'] ?? 0 );

		$page     = max( 1, $page );
		$per_page = max( 1, $per_page );

		$args = ( new MultiInstructorsRepository() )->getCoCourses( $user_id, true );

		$args['posts_per_page'] = $per_page;
		$args['paged']          = $page;

		if ( isset( $args['offset'] ) ) {
			unset( $args['offset'] );
		}

		$payload = array(
			'posts' => array(),
			'pages' => 0,
			'found' => 0,
		);

		if ( class_exists( '\STM_LMS_Instructor' ) && method_exists( '\STM_LMS_Instructor', 'get_instructor_courses' ) ) {
			$payload = \STM_LMS_Instructor::get_instructor_courses( $args, $per_page );
		}

		if ( 'html' === $render ) {
			$reviews = \STM_LMS_Options::get_option( 'course_tab_reviews', true );

			ob_start();
			if ( ! empty( $payload['posts'] ) ) {
				foreach ( $payload['posts'] as $course ) {
					\STM_LMS_Templates::show_lms_template(
						'components/course/card/default',
						array(
							'course'          => $course,
							'public'          => false,
							'reviews'         => (bool) $reviews,
							'student_card'    => false,
							'instructor_card' => true,
						)
					);
				}
			}
			$html = ob_get_clean();

			$pagination = '';
			if ( ! empty( $payload['pages'] ) && (int) $payload['pages'] > 1 ) {
				ob_start();
				\STM_LMS_Templates::show_lms_template(
					'components/pagination',
					array(
						'max_visible_pages' => 5,
						'total_pages'       => (int) $payload['pages'],
						'current_page'      => $page,
						'dark_mode'         => false,
						'is_queryable'      => false,
						'done_indicator'    => false,
						'is_api'            => true,
					)
				);
				$pagination = ob_get_clean();
			}

			$payload['html']        = $html;
			$payload['pagination']  = $pagination;
			$payload['page']        = $page;
			$payload['total_pages'] = (int) $payload['pages'];
		}

		return new WP_REST_Response( $payload );
	}
}
