<?php

STM_LMS_Templates::show_lms_template(
	'components/react-app-template/main',
	array(
		'app_id'     => 'ms_wp_react_coupons',
		'react_vars' => array(
			'object_name' => 'react_coupons',
			'vars'        => array(
				'course_category_url'  => esc_url( STM_LMS_Course::courses_page_url() ),
				'membership_plans_url' => admin_url( 'admin.php?page=manage_membership_plans' ),
			),
		),
	)
);
