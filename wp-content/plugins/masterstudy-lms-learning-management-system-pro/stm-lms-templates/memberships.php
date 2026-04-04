<?php

STM_LMS_Templates::show_lms_template(
	'components/react-app-template/main',
	array(
		'app_id'     => 'ms_wp_react_memberships',
		'react_vars' => array(
			'object_name' => 'react_memberships',
			'vars'        => array(
				'taxes_info'         => function_exists( 'masterstudy_lms_ecommerce_options' ) ? masterstudy_lms_ecommerce_options() : array(),
				'is_coupons_enabled' => is_ms_lms_coupons_enabled(),
			),
		),
	)
);
