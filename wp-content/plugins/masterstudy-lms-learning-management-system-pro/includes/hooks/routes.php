<?php
use MasterStudy\Lms\Plugin\Addons;

add_filter(
	'stm_lms_custom_routes_config',
	function ( $routes ) {
		$routes['user_url']['sub_pages']['gradebook'] = array(
			'template'         => 'account/instructor/gradebook',
			'protected'        => true,
			'instructors_only' => true,
			'url'              => 'gradebook',
		);

		$routes['user_url']['sub_pages']['enterprise_groups'] = array(
			'template'  => 'account/enterprise-groups',
			'protected' => true,
			'url'       => 'enterprise-groups',
		);

		$routes['user_url']['sub_pages']['google_meets'] = array(
			'template'  => 'account/instructor/google-meets',
			'protected' => true,
			'url'       => 'google-meets',
		);

		$routes['user_url']['sub_pages']['assignments'] = array(
			'template'  => 'account/instructor/assignments',
			'protected' => true,
			'url'       => 'assignments',
			'sub_pages' => array(
				'assignment' => array(
					'template'  => 'account/instructor/assignment',
					'protected' => true,
					'var'       => 'assignment_id',
				),
			),
		);

		$routes['user_url']['sub_pages']['enrolled_assignments'] = array(
			'template'  => 'account/enrolled-assignments',
			'protected' => true,
			'url'       => 'enrolled-assignments',
		);

		$routes['user_url']['sub_pages']['user_assignment'] = array(
			'template'  => 'account/instructor/user-assignment',
			'protected' => true,
			'url'       => 'user-assignment',
			'sub_pages' => array(
				'assignment' => array(
					'template'  => 'account/instructor/user-assignment',
					'protected' => true,
					'var'       => 'assignment_id',
				),
			),
		);

		$routes['user_url']['sub_pages']['points_history'] = array(
			'template'  => 'account/points-history',
			'protected' => true,
			'url'       => 'points-history',
		);

		$routes['user_url']['sub_pages']['points_distribution'] = array(
			'template'  => 'account/points-distribution',
			'protected' => true,
			'url'       => 'points-distribution',
		);

		$routes['user_url']['sub_pages']['bundles'] = array(
			'template'  => 'account/instructor/bundles',
			'protected' => true,
			'url'       => 'bundles',
			'sub_pages' => array(
				'bundle' => array(
					'template'  => 'account/instructor/bundle',
					'protected' => true,
					'var'       => 'bundle_id',
				),
			),
		);

		$routes['user_url']['sub_pages']['payout_statistic'] = array(
			'template'  => 'account/instructor/payout-statistic',
			'protected' => true,
			'url'       => 'payout',
		);

		$routes['user_url']['sub_pages']['manage_google_meet'] = array(
			'template'         => 'course-builder',
			'protected'        => true,
			'instructors_only' => true,
			'url'              => 'edit-google-meet',
			'sub_pages'        => array(
				'edit_course' => array(
					'template'  => 'course-builder',
					'protected' => true,
					'var'       => 'google_meet_id',
				),
			),
		);

		$routes['user_url']['sub_pages']['analytics'] = array(
			'title'            => esc_html__( 'Analytics', 'masterstudy-lms-learning-management-system-pro' ),
			'template'         => 'analytics/revenue',
			'protected'        => true,
			'instructors_only' => true,
			'url'              => 'analytics',
			'sub_pages'        => array(
				'engagement' => array(
					'template'         => 'analytics/engagement',
					'url'              => 'engagement',
					'instructors_only' => true,
					'protected'        => true,
				),
				'students'   => array(
					'template'         => 'analytics/instructor-students',
					'url'              => 'instructor-students',
					'instructors_only' => true,
					'protected'        => true,
				),
				'reviews'    => array(
					'template'         => 'analytics/reviews',
					'url'              => 'reviews',
					'instructors_only' => true,
					'protected'        => true,
				),
				'course'     => array(
					'template'         => 'analytics/course',
					'url'              => 'course',
					'instructors_only' => true,
					'protected'        => true,
				),
				'bundle'     => array(
					'template'         => 'analytics/bundle',
					'url'              => 'bundle',
					'instructors_only' => true,
					'protected'        => true,
				),
				'student'    => array(
					'template'         => 'analytics/student',
					'url'              => 'student',
					'instructors_only' => true,
					'protected'        => true,
				),
			),
		);

		if ( is_ms_lms_addon_enabled( 'grades' ) ) {
			$routes['user_url']['sub_pages']['grades'] = array(
				'title'            => esc_html__( 'Grades', 'masterstudy-lms-learning-management-system-pro' ),
				'template'         => 'grades/instructor',
				'protected'        => true,
				'instructors_only' => true,
				'url'              => 'grades',
			);

			$routes['user_url']['sub_pages']['my-grades'] = array(
				'title'     => esc_html__( 'My grades', 'masterstudy-lms-learning-management-system-pro' ),
				'template'  => 'grades/student',
				'url'       => 'my-grades',
				'protected' => true,
			);
		}

		if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
			$routes['certificate_page_url'] = array(
				'title'    => esc_html__( 'Certificate Page', 'masterstudy-lms-learning-management-system-pro' ),
				'template' => 'stm-lms-certificate',
			);
		}

		$routes['user_url']['sub_pages']['sales'] = array(
			'title'            => esc_html__( 'My Sales', 'masterstudy-lms-learning-management-system-pro' ),
			'template'         => 'account/instructor/my-sales',
			'protected'        => true,
			'instructors_only' => true,
			'url'              => 'sales',
			'sub_pages'        => array(),
		);

		if ( is_ms_lms_addon_enabled( defined( 'Addons::SUBSCRIPTIONS' ) ? Addons::SUBSCRIPTIONS : 'subscriptions' ) ) {
			$routes['user_url']['sub_pages']['my-subscriptions'] = array(
				'title'     => esc_html__( 'My Subscriptions', 'masterstudy-lms-learning-management-system-pro' ),
				'template'  => 'account/my-subscriptions',
				'protected' => true,
				'url'       => 'my-subscriptions',
				'sub_pages' => array(),
			);
		}

		return $routes;
	}
);
