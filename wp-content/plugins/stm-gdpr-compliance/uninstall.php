<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
	die();
}

global $wpdb;

$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE 'stm_gdpr_compliance\_%';");

wp_cache_flush();