<?php

/**
 * Upcoming Course Status Nuxy Settings
 */
function masterstudy_lms_coming_soon_settings( $setups ) {
	$fields = array(
		'credentials' => array(
			'name'   => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ),
			'label'  => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ),
			'fields' => array(
				'lms_coming_soon_instructor_allow_status' => array(
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Allow instructors to enable Upcoming course status', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'Instructors can create a course that it will be available in the future', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'lms_coming_soon_pre_ordering_status'     => array(
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Course preordering', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'Your students can buy the course but can’t start until the specified course launch date', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'lms_coming_soon_course_bundle_status'    => array(
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Allow upcoming courses to be added to the Course bundles', 'masterstudy-lms-learning-management-system-pro' ),
					'description' => esc_html__( 'Enable this option to allow upcoming courses to be included in course bundles', 'masterstudy-lms-learning-management-system-pro' ),
				),
			),
		),
	);

	$setups[] = array(
		'page'        => array(
			'parent_slug' => 'stm-lms-settings',
			'page_title'  => esc_html__( 'Upcoming Course Status', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_title'  => esc_html__( 'Upcoming Course Status', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_slug'   => 'upcoming-course-status',
		),
		'fields'      => $fields,
		'option_name' => 'masterstudy_lms_coming_soon_settings',
	);

	return $setups;
}

add_filter( 'wpcfto_options_page_setup', 'masterstudy_lms_coming_soon_settings' );
