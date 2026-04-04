<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_subscriptions_table_query() {
	global $wpdb;

	$table_name      = stm_lms_subscriptions_table_name( $wpdb );
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		user_id BIGINT(20) UNSIGNED NOT NULL,
		plan_id BIGINT(20) UNSIGNED NOT NULL,
		first_order_id BIGINT(20) UNSIGNED NOT NULL,
		active_order_id BIGINT(20) UNSIGNED NOT NULL,
		status VARCHAR(50) NOT NULL,
		is_trial_used TINYINT(1) DEFAULT 0,
		trial_end_date DATETIME NULL,
		start_date DATETIME NULL,
		end_date DATETIME NULL,
		next_payment_date DATETIME NULL,
		note TEXT,
		created_at DATETIME NULL,
		updated_at DATETIME NULL,
		PRIMARY KEY (id),
		INDEX idx_user_id (user_id),
		INDEX idx_plan_id (plan_id),
		INDEX idx_first_order_id (first_order_id),
		INDEX idx_active_order_id (active_order_id),
		INDEX idx_status (status)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
