<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function stm_lms_subscription_meta_table_query() {
	global $wpdb;

	$table_name      = stm_lms_subscription_meta_table_name( $wpdb );
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		subscription_id BIGINT(20) UNSIGNED NOT NULL,
		meta_key VARCHAR(255) NOT NULL,
		meta_value LONGTEXT,
		PRIMARY KEY (id),
		INDEX idx_subscription_id (subscription_id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( $sql );
}
