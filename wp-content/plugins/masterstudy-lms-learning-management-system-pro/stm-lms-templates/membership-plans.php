<?php

STM_LMS_Templates::show_lms_template(
	'components/react-app-template/main',
	array(
		'app_id'     => 'ms_wp_react_membership_plans',
		'react_vars' => array(
			'object_name' => 'react_membership_plans',
			'vars'        => masterstudy_lms_get_membership_plans_template_vars(),
		),
	)
);
