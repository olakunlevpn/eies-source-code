<?php
use MasterStudy\Lms\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Table Names
function stm_lms_subscription_plans_table_name( $wpdb ) {
	return "{$wpdb->prefix}stm_lms_subscription_plans";
}

function stm_lms_subscription_plan_items_table_name( $wpdb ) {
	return "{$wpdb->prefix}stm_lms_subscription_plan_items";
}

function stm_lms_subscriptions_table_name( $wpdb ) {
	return "{$wpdb->prefix}stm_lms_subscriptions";
}

function stm_lms_subscription_meta_table_name( $wpdb ) {
	return "{$wpdb->prefix}stm_lms_subscription_meta";
}

function stm_lms_subscription_tables() {
	stm_lms_subscription_plans_table_query();
	stm_lms_subscription_plan_items_table_query();
	stm_lms_subscriptions_table_query();
	stm_lms_subscription_meta_table_query();
}
