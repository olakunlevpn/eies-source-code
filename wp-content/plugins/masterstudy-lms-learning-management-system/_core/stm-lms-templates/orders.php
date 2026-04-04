<?php

use MasterStudy\Lms\Enums\OrderStatus;

$app_id = 'ms_wp_react_orders';

STM_LMS_Templates::show_lms_template(
	'components/react-app-template/main',
	array(
		'app_id'     => $app_id,
		'react_vars' => array(
			'object_name' => 'react_orders',
			'vars'        => array(
				'statuses'               => array_map( 'strval', OrderStatus::cases() ),
				'taxes_info'             => masterstudy_lms_ecommerce_options(),
				'is_woocommerce'         => STM_LMS_Cart::woocommerce_checkout_enabled(),
				'woocommerce_orders_url' => admin_url( 'admin.php?page=wc-orders' ),
				'countries'              => masterstudy_lms_get_countries( false ),
				'regions'                => array( 'US' => masterstudy_lms_get_us_states( false ) ),
				'is_coupons_enabled'     => STM_LMS_Helpers::is_coupons_enabled(),
			),
		),
	)
);
