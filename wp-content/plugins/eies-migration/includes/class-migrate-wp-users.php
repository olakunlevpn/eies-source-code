<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_WP_Users extends EIES_Migration_Base {

	private $batch_size = 200;
	private $old_db;

	public function run() {
		$this->connect_old_wp();

		if ( ! $this->old_db || ! $this->old_db->dbh ) {
			return array( 'success' => false, 'message' => 'Cannot connect to old WordPress database (marceloeies_restore).' );
		}

		$total = (int) $this->old_db->get_var( "SELECT COUNT(*) FROM wp_users" );
		if ( ! $total ) {
			return array( 'success' => false, 'message' => 'No users found in old WordPress database.' );
		}

		$already_done = $this->get_mapping_count( 'wp_user' );
		if ( $already_done >= $total ) {
			return array( 'success' => true, 'message' => sprintf( 'All %d WP users already processed.', $already_done ) );
		}

		// Meta keys to copy
		$meta_keys = array(
			'billing_first_name', 'billing_last_name', 'billing_company',
			'billing_address_1', 'billing_address_2', 'billing_city',
			'billing_state', 'billing_postcode', 'billing_country',
			'billing_email', 'billing_phone',
			'shipping_first_name', 'shipping_last_name', 'shipping_company',
			'shipping_address_1', 'shipping_address_2', 'shipping_city',
			'shipping_state', 'shipping_postcode', 'shipping_country',
			'shipping_phone', 'shipping_method',
			'paying_customer', 'cohort_name', 'wc_last_active',
		);

		$offset = 0;
		$merged = 0;
		$created = 0;
		$skipped = 0;

		while ( $offset < $total ) {
			$users = $this->old_db->get_results(
				$this->old_db->prepare(
					"SELECT ID, user_login, user_email, user_registered, display_name
					 FROM wp_users ORDER BY ID ASC LIMIT %d OFFSET %d",
					$this->batch_size, $offset
				)
			);

			if ( empty( $users ) ) break;

			foreach ( $users as $old_user ) {
				// Skip if already processed
				if ( $this->get_wp_id( 'wp_user', $old_user->ID ) ) {
					continue;
				}

				$email = strtolower( trim( $old_user->user_email ) );
				if ( empty( $email ) ) {
					$skipped++;
					$this->save_mapping( 'wp_user', $old_user->ID, 0 );
					continue;
				}

				// Check if user already exists in new DB (from Moodle migration)
				$existing_id = email_exists( $email );

				if ( $existing_id ) {
					// MERGE: User exists, add WP meta
					$this->merge_user_meta( $old_user->ID, $existing_id, $meta_keys );
					$this->save_mapping( 'wp_user', $old_user->ID, $existing_id );
					$merged++;
				} else {
					// CREATE: New user from WP only
					$new_id = $this->create_wp_user( $old_user, $meta_keys );
					if ( $new_id ) {
						$this->save_mapping( 'wp_user', $old_user->ID, $new_id );
						$created++;
					} else {
						$skipped++;
						$this->save_mapping( 'wp_user', $old_user->ID, 0 );
					}
				}
			}

			$offset += $this->batch_size;
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'WP Users: %d merged with existing, %d created new, %d skipped.',
				$merged, $created, $skipped
			),
		);
	}

	private function merge_user_meta( $old_user_id, $new_user_id, $meta_keys ) {
		foreach ( $meta_keys as $key ) {
			$old_value = $this->old_db->get_var(
				$this->old_db->prepare(
					"SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = %s",
					$old_user_id, $key
				)
			);

			if ( ! empty( $old_value ) ) {
				// Only set if new user doesn't already have this meta
				$existing = get_user_meta( $new_user_id, $key, true );
				if ( empty( $existing ) ) {
					update_user_meta( $new_user_id, $key, $old_value );
				}
			}
		}

		// Ensure user has customer capabilities for WooCommerce
		$user = new WP_User( $new_user_id );
		if ( ! $user->has_cap( 'customer' ) && ! $user->has_cap( 'administrator' ) && ! $user->has_cap( 'editor' ) ) {
			// Don't change role, just ensure WC compatibility
			update_user_meta( $new_user_id, 'wc_last_active', time() );
		}
	}

	private function create_wp_user( $old_user, $meta_keys ) {
		$username = sanitize_user( $old_user->user_login, true );
		if ( empty( $username ) ) {
			$username = sanitize_user( $old_user->display_name, true );
		}
		if ( empty( $username ) ) {
			$username = sanitize_user( explode( '@', $old_user->user_email )[0], true );
		}

		// Ensure unique username
		$original = $username;
		$i = 1;
		while ( username_exists( $username ) ) {
			$username = $original . $i;
			$i++;
		}

		// Get old user's role
		$old_caps = $this->old_db->get_var(
			$this->old_db->prepare(
				"SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = 'wp_capabilities'",
				$old_user->ID
			)
		);
		$role = 'customer'; // WooCommerce default
		if ( $old_caps ) {
			$caps = @unserialize( $old_caps );
			if ( is_array( $caps ) ) {
				if ( isset( $caps['administrator'] ) ) $role = 'administrator';
				elseif ( isset( $caps['editor'] ) ) $role = 'editor';
				elseif ( isset( $caps['author'] ) ) $role = 'author';
				elseif ( isset( $caps['subscriber'] ) ) $role = 'subscriber';
				// customer stays as default
			}
		}

		// Get first/last name from old usermeta
		$first_name = $this->old_db->get_var(
			$this->old_db->prepare( "SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = 'first_name'", $old_user->ID )
		);
		$last_name = $this->old_db->get_var(
			$this->old_db->prepare( "SELECT meta_value FROM wp_usermeta WHERE user_id = %d AND meta_key = 'last_name'", $old_user->ID )
		);

		$new_user_id = wp_insert_user( array(
			'user_login'      => $username,
			'user_email'      => $old_user->user_email,
			'first_name'      => $first_name ?: '',
			'last_name'       => $last_name ?: '',
			'display_name'    => $old_user->display_name ?: trim( $first_name . ' ' . $last_name ),
			'user_pass'       => wp_generate_password( 16, true, true ),
			'role'            => $role,
			'user_registered' => $old_user->user_registered,
		) );

		if ( is_wp_error( $new_user_id ) ) {
			return false;
		}

		// Copy meta
		$this->merge_user_meta( $old_user->ID, $new_user_id, $meta_keys );

		return $new_user_id;
	}

	private function connect_old_wp() {
		$this->old_db = new wpdb(
			defined( 'MOODLE_DB_USER' ) ? MOODLE_DB_USER : 'root',
			defined( 'MOODLE_DB_PASS' ) ? MOODLE_DB_PASS : '',
			'marceloeies_restore',
			defined( 'MOODLE_DB_HOST' ) ? MOODLE_DB_HOST : 'localhost'
		);
		if ( $this->old_db->dbh ) {
			$this->old_db->set_charset( $this->old_db->dbh, 'utf8mb4' );
		}
	}
}
