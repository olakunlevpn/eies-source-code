<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_subscription_plans_table_query() {
	global $wpdb;

	$table_name      = stm_lms_subscription_plans_table_name( $wpdb );
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        type VARCHAR(50) NOT NULL,
		name VARCHAR(255) NOT NULL,
		description VARCHAR(255) NULL,
		recurring_value INT(11) DEFAULT 1 NOT NULL,
		recurring_interval VARCHAR(10) DEFAULT 'month' NOT NULL,
		billing_cycles INT(11) DEFAULT 0,
		price DECIMAL(10,2) NOT NULL,
		sale_price DECIMAL(10,2) NULL,
		sale_price_from DATETIME NULL,
		sale_price_to DATETIME NULL,
		plan_features TEXT NULL,
		enrollment_fee DECIMAL(10,2) DEFAULT 0,
		trial_period INT(11) DEFAULT 0,
		is_featured TINYINT(1) DEFAULT 0,
		featured_text VARCHAR(255) NULL,
		is_certified TINYINT(1) DEFAULT 1,
		is_enabled TINYINT(1) DEFAULT 1,
		plan_order BIGINT(20) UNSIGNED DEFAULT 0,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
