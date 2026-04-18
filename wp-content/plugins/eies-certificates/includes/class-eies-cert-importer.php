<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Cert_Importer {

	const BATCH = 500;

	private static function moodle_db() {
		if ( ! defined( 'MOODLE_DB_HOST' ) ) {
			return new WP_Error( 'no_moodle', __( 'Moodle DB credentials not configured in wp-config.php', 'eies-certificates' ) );
		}
		$mdb = @new wpdb( MOODLE_DB_USER, MOODLE_DB_PASS, MOODLE_DB_NAME, MOODLE_DB_HOST );
		$mdb->suppress_errors( true );
		$mdb->hide_errors();
		if ( ! empty( $mdb->error ) || ! $mdb->dbh ) {
			return new WP_Error( 'moodle_connect_failed', __( 'Cannot connect to Moodle database', 'eies-certificates' ) );
		}
		return $mdb;
	}

	private static function empty_stats() {
		return array(
			'total'         => 0,
			'done'          => 0,
			'remaining'     => 0,
			'new_available' => 0,
		);
	}

	public static function count_remaining() {
		$mdb = self::moodle_db();
		if ( is_wp_error( $mdb ) ) {
			return self::empty_stats();
		}
		global $wpdb;
		$table         = $wpdb->prefix . EIES_CERT_TABLE;
		$last_imported = (int) $wpdb->get_var( "SELECT MAX(moodle_issue_id) FROM {$table} WHERE source = 'moodle'" );
		$total         = (int) $mdb->get_var( "SELECT COUNT(*) FROM mdl_customcert_issues" );
		$done          = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE source = 'moodle'" );
		$new_count     = (int) $mdb->get_var( $mdb->prepare( "SELECT COUNT(*) FROM mdl_customcert_issues WHERE id > %d", $last_imported ) );
		return array(
			'total'         => $total,
			'done'          => $done,
			'remaining'     => max( 0, $total - $done ),
			'new_available' => $new_count,
		);
	}

	public static function import_batch( $offset = 0 ) {
		$mdb = self::moodle_db();
		if ( is_wp_error( $mdb ) ) {
			return $mdb;
		}

		global $wpdb;
		$table = $wpdb->prefix . EIES_CERT_TABLE;

		$last_imported = (int) $wpdb->get_var( "SELECT MAX(moodle_issue_id) FROM {$table} WHERE source = 'moodle'" );

		$limit = self::BATCH;
		$sql   = $mdb->prepare(
			"SELECT ci.id, ci.userid, ci.customcertid, ci.code, ci.timecreated,
				u.firstname, u.lastname, u.email,
				cc.name AS cert_name, cc.course AS course_id,
				c.fullname AS course_name
			FROM mdl_customcert_issues ci
			LEFT JOIN mdl_user u ON u.id = ci.userid
			LEFT JOIN mdl_customcert cc ON cc.id = ci.customcertid
			LEFT JOIN mdl_course c ON c.id = cc.course
			WHERE ci.id > %d
			ORDER BY ci.id ASC
			LIMIT %d",
			$last_imported,
			$limit
		);

		$rows     = $mdb->get_results( $sql );
		$imported = 0;
		$skipped  = 0;

		foreach ( $rows as $row ) {
			$code = trim( (string) $row->code );
			if ( $code === '' ) {
				$skipped++;
				continue;
			}

			$name  = trim( $row->firstname . ' ' . $row->lastname );
			$email = strtolower( trim( (string) $row->email ) );

			$wp_user_id = null;
			if ( $email !== '' ) {
				$user = get_user_by( 'email', $email );
				if ( $user ) {
					$wp_user_id = (int) $user->ID;
				}
			}

			$moodle_id = (int) $row->id;

			$exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE moodle_issue_id = %d", $moodle_id ) );
			if ( $exists ) {
				$skipped++;
				continue;
			}

			$data = array(
				'code'             => $code,
				'student_name'     => $name,
				'student_email'    => $email,
				'course_name'      => (string) $row->course_name,
				'cert_type'        => (string) $row->cert_name,
				'issued_date'      => gmdate( 'Y-m-d H:i:s', (int) $row->timecreated ),
				'source'           => 'moodle',
				'moodle_issue_id'  => $moodle_id,
				'wp_user_id'       => $wp_user_id,
			);

			$result = $wpdb->insert( $table, $data );
			if ( $result === false ) {
				$skipped++;
			} else {
				$imported++;
			}
		}

		$stats = self::count_remaining();
		return array(
			'imported'      => $imported,
			'skipped'       => $skipped,
			'remaining'     => $stats['remaining'],
			'new_available' => $stats['new_available'],
			'total'         => $stats['total'],
			'done'          => $stats['done'],
		);
	}
}
