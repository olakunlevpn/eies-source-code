<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_coupons_table_name( $wpdb ): string {
	return "{$wpdb->prefix}stm_lms_coupons";
}

function stm_lms_coupons_table_query(): void {
	global $wpdb;

	$table_name      = stm_lms_coupons_table_name( $wpdb );
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		title VARCHAR(255) NOT NULL,
		coupon_status VARCHAR(20) NOT NULL DEFAULT 'active',
		code VARCHAR(100) NOT NULL,
		discount_type VARCHAR(20) NOT NULL DEFAULT 'percent',
		discount DECIMAL(20,6) NOT NULL DEFAULT 0,
		product_type VARCHAR(50) NOT NULL DEFAULT 'all',
		usage_limit INT(11) UNSIGNED NULL,
		used_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
		user_usage_limit INT(11) UNSIGNED NULL,
		min_purchase_amount DECIMAL(20,6) NULL,
		min_course_quantity INT(11) UNSIGNED NULL,
		start_at DATETIME NULL,
		end_at DATETIME NULL,
		items LONGTEXT NULL,
		created_at DATETIME NULL,
		updated_at DATETIME NULL,
		PRIMARY KEY (id),
		UNIQUE KEY uq_code (code),
		KEY idx_status (coupon_status),
		KEY idx_active_period (coupon_status, start_at, end_at),
		KEY idx_code_status (code, coupon_status),
		KEY idx_limit_usage (usage_limit, used_count)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
