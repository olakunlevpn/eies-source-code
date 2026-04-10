<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_Users extends EIES_Migration_Base {

	public function run() {
		$user_table = $this->moodle_table( 'user' );
		// I2 FIX: Also filter suspended users
		$users = $this->moodle_db->get_results(
			"SELECT id, username, email, firstname, lastname, city, country, description
			 FROM {$user_table}
			 WHERE deleted = 0 AND suspended = 0 AND id > 1 AND email != ''
			 ORDER BY id ASC"
		);

		if ( empty( $users ) ) {
			return array( 'success' => false, 'message' => 'No users found in Moodle.' );
		}

		// Get role assignments from Moodle
		$role_table = $this->moodle_table( 'role_assignments' );
		$roles_table = $this->moodle_table( 'role' );
		$role_map = $this->moodle_db->get_results(
			"SELECT ra.userid, r.shortname
			 FROM {$role_table} ra
			 JOIN {$roles_table} r ON ra.roleid = r.id
			 GROUP BY ra.userid, r.shortname"
		);

		$user_roles = array();
		foreach ( $role_map as $rm ) {
			if ( ! isset( $user_roles[ $rm->userid ] ) ) {
				$user_roles[ $rm->userid ] = array();
			}
			$user_roles[ $rm->userid ][] = $rm->shortname;
		}

		$count = 0;
		$skipped = 0;

		foreach ( $users as $user ) {
			// Skip if already migrated
			if ( $this->get_wp_id( 'user', $user->id ) ) {
				$count++;
				continue;
			}

			// Skip if email already exists in WP
			if ( email_exists( $user->email ) ) {
				$existing_id = email_exists( $user->email );
				$this->save_mapping( 'user', $user->id, $existing_id );
				$count++;
				continue;
			}

			// Sanitize username
			$username = sanitize_user( $user->username, true );
			if ( empty( $username ) ) {
				$username = sanitize_user( $user->firstname . '.' . $user->lastname, true );
			}

			// Ensure unique username
			$original = $username;
			$i = 1;
			while ( username_exists( $username ) ) {
				$username = $original . $i;
				$i++;
			}

			// Determine WP role
			$wp_role = 'subscriber'; // default for students
			$is_instructor = false;

			// I4 FIX: Higher privilege roles take precedence
			if ( isset( $user_roles[ $user->id ] ) ) {
				$roles = $user_roles[ $user->id ];
				if ( in_array( 'editingteacher', $roles, true ) || in_array( 'teacher', $roles, true ) ) {
					$wp_role = 'author';
					$is_instructor = true;
				}
				if ( in_array( 'manager', $roles, true ) ) {
					$wp_role = 'editor';
				}
			}

			$wp_user_id = wp_insert_user( array(
				'user_login'   => $username,
				'user_email'   => $user->email,
				'first_name'   => $user->firstname,
				'last_name'    => $user->lastname,
				'display_name' => trim( $user->firstname . ' ' . $user->lastname ),
				'description'  => $user->description ?? '',
				'user_pass'    => wp_generate_password( 16, true, true ),
				'role'         => $wp_role,
			) );

			if ( is_wp_error( $wp_user_id ) ) {
				$skipped++;
				continue;
			}

			// Set instructor capability
			if ( $is_instructor ) {
				$wp_user = new WP_User( $wp_user_id );
				$wp_user->add_cap( 'stm_lms_instructor' );
			}

			// Save location meta
			if ( ! empty( $user->city ) ) {
				update_user_meta( $wp_user_id, 'stm_lms_city', $user->city );
			}
			if ( ! empty( $user->country ) ) {
				update_user_meta( $wp_user_id, 'stm_lms_country', $user->country );
			}

			$this->save_mapping( 'user', $user->id, $wp_user_id );
			$count++;
		}

		return array(
			'success' => true,
			'message' => sprintf( '%d users migrated, %d skipped.', $count, $skipped ),
		);
	}
}
