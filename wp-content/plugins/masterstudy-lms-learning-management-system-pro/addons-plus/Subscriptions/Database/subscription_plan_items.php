<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_subscription_plan_items_table_query() {
	global $wpdb;

	$table_name      = stm_lms_subscription_plan_items_table_name( $wpdb );
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		plan_id BIGINT(20) UNSIGNED NOT NULL,
		object_type VARCHAR(50) NOT NULL,
		object_id BIGINT(20) UNSIGNED NOT NULL,
		INDEX idx_object_type (object_type),
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
