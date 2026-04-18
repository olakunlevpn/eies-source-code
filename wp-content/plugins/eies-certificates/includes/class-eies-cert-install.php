<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Cert_Install {

	public static function activate() {
		global $wpdb;

		$table   = $wpdb->prefix . EIES_CERT_TABLE;
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			code varchar(60) NOT NULL,
			student_name varchar(255) NOT NULL DEFAULT '',
			student_email varchar(190) NOT NULL DEFAULT '',
			course_name varchar(500) NOT NULL DEFAULT '',
			cert_type varchar(255) NOT NULL DEFAULT '',
			issued_date datetime NOT NULL,
			source varchar(20) NOT NULL DEFAULT 'moodle',
			moodle_issue_id bigint(20) unsigned DEFAULT NULL,
			wp_user_id bigint(20) unsigned DEFAULT NULL,
			wp_course_id bigint(20) unsigned DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY code (code),
			KEY student_email (student_email),
			UNIQUE KEY moodle_issue_id (moodle_issue_id)
		) {$charset}";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Register rewrite and flush.
		add_rewrite_rule( '^verificar-certificado/?$', 'index.php?eies_verify=1', 'top' );
		flush_rewrite_rules();
	}
}
