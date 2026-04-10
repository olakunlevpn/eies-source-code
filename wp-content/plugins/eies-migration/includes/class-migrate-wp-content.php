<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_WP_Content extends EIES_Migration_Base {

	private $old_db;

	public function run() {
		$this->connect_old_wp();

		if ( ! $this->old_db || ! $this->old_db->dbh ) {
			return array( 'success' => false, 'message' => 'Cannot connect to old WordPress database.' );
		}

		$results = array();
		$results[] = $this->migrate_reviews();
		$results[] = $this->migrate_custom_tables();
		$results[] = $this->migrate_testimonials();
		$results[] = $this->migrate_coupons();

		$messages = array_filter( array_column( $results, 'message' ) );

		return array(
			'success' => true,
			'message' => implode( ' | ', $messages ),
		);
	}

	private function migrate_reviews() {
		global $wpdb;

		$reviews = $this->old_db->get_results(
			"SELECT c.comment_ID, c.comment_post_ID, c.comment_author, c.comment_author_email,
			        c.comment_date, c.comment_date_gmt, c.comment_content, c.comment_approved,
			        c.user_id, cm.meta_value as rating
			 FROM wp_comments c
			 LEFT JOIN wp_commentmeta cm ON c.comment_ID = cm.comment_id AND cm.meta_key = 'rating'
			 WHERE c.comment_type = 'review' AND c.comment_approved = '1'
			 ORDER BY c.comment_ID ASC"
		);

		if ( empty( $reviews ) ) {
			return array( 'success' => true, 'message' => 'Reviews: 0 found.' );
		}

		$count = 0;

		foreach ( $reviews as $review ) {
			if ( $this->get_wp_id( 'wp_review', $review->comment_ID ) ) {
				$count++;
				continue;
			}

			// Find the matching course for this product
			$new_course_id = $this->get_wp_id( 'wp_product', $review->comment_post_ID );
			if ( ! $new_course_id ) {
				// Try mapping via course name
				$product_title = $this->old_db->get_var(
					$this->old_db->prepare( "SELECT post_title FROM wp_posts WHERE ID = %d", $review->comment_post_ID )
				);
				if ( $product_title ) {
					$new_course_id = $wpdb->get_var( $wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'stm-courses' AND post_title = %s LIMIT 1",
						$product_title
					) );
				}
			}

			if ( ! $new_course_id ) continue;

			// Re-map user ID
			$new_user_id = 0;
			if ( $review->user_id > 0 ) {
				$new_user_id = $this->get_wp_id( 'wp_user', $review->user_id );
				if ( ! $new_user_id ) $new_user_id = 0;
			}

			$wpdb->insert(
				$wpdb->comments,
				array(
					'comment_post_ID'      => (int) $new_course_id,
					'comment_author'       => $review->comment_author,
					'comment_author_email' => $review->comment_author_email,
					'comment_author_url'   => '',
					'comment_author_IP'    => '',
					'comment_date'         => $review->comment_date,
					'comment_date_gmt'     => $review->comment_date_gmt,
					'comment_content'      => $review->comment_content,
					'comment_karma'        => 0,
					'comment_approved'     => '1',
					'comment_agent'        => '',
					'comment_type'         => 'review',
					'comment_parent'       => 0,
					'user_id'              => (int) $new_user_id,
				)
			);

			$new_comment_id = $wpdb->insert_id;
			if ( $new_comment_id && ! empty( $review->rating ) ) {
				$wpdb->insert(
					$wpdb->commentmeta,
					array(
						'comment_id' => $new_comment_id,
						'meta_key'   => 'rating',
						'meta_value' => $review->rating,
					)
				);
			}

			if ( $new_comment_id ) {
				$this->save_mapping( 'wp_review', $review->comment_ID, $new_comment_id );
				$count++;
			}
		}

		return array( 'success' => true, 'message' => sprintf( 'Reviews: %d imported.', $count ) );
	}

	private function migrate_custom_tables() {
		global $wpdb;
		$results = array();

		// sys_evaluaciones
		$table = 'sys_evaluaciones';
		if ( ! $this->table_exists( $table, $wpdb ) ) {
			$this->old_db->query( "SHOW CREATE TABLE {$table}" );
			$create = $this->old_db->get_row( "SHOW CREATE TABLE {$table}", ARRAY_N );
			if ( $create && isset( $create[1] ) ) {
				$wpdb->query( $create[1] );
			}
		}
		$rows = $this->old_db->get_results( "SELECT * FROM {$table}" );
		$eval_count = 0;
		foreach ( $rows as $row ) {
			$data = (array) $row;
			// Re-map order ID
			if ( isset( $data['id_orden'] ) && $data['id_orden'] > 0 ) {
				$new_order_id = $this->get_wp_id( 'wp_order', $data['id_orden'] );
				if ( $new_order_id ) $data['id_orden'] = $new_order_id;
			}
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d", $data['id'] ) );
			if ( ! $exists ) {
				$wpdb->insert( $table, $data );
				$eval_count++;
			}
		}
		$results[] = "sys_evaluaciones: {$eval_count}";

		// sys_lista_correos (direct copy, no mapping)
		$table = 'sys_lista_correos';
		if ( ! $this->table_exists( $table, $wpdb ) ) {
			$create = $this->old_db->get_row( "SHOW CREATE TABLE {$table}", ARRAY_N );
			if ( $create && isset( $create[1] ) ) {
				$wpdb->query( $create[1] );
			}
		}
		$count_before = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		if ( $count_before === 0 ) {
			$rows = $this->old_db->get_results( "SELECT * FROM {$table}" );
			foreach ( $rows as $row ) {
				$wpdb->insert( $table, (array) $row );
			}
		}
		$count_after = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		$results[] = "sys_lista_correos: {$count_after}";

		// sys_fechas_sedes
		$table = 'sys_fechas_sedes';
		if ( ! $this->table_exists( $table, $wpdb ) ) {
			$create = $this->old_db->get_row( "SHOW CREATE TABLE {$table}", ARRAY_N );
			if ( $create && isset( $create[1] ) ) {
				$wpdb->query( $create[1] );
			}
		}
		$count_before = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		if ( $count_before === 0 ) {
			$rows = $this->old_db->get_results( "SELECT * FROM {$table}" );
			foreach ( $rows as $row ) {
				$wpdb->insert( $table, (array) $row );
			}
		}
		$count_after = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		$results[] = "sys_fechas_sedes: {$count_after}";

		// sys_firma3
		$table = 'sys_firma3';
		if ( ! $this->table_exists( $table, $wpdb ) ) {
			$create = $this->old_db->get_row( "SHOW CREATE TABLE {$table}", ARRAY_N );
			if ( $create && isset( $create[1] ) ) {
				$wpdb->query( $create[1] );
			}
		}
		$count_before = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		if ( $count_before === 0 ) {
			$rows = $this->old_db->get_results( "SELECT * FROM {$table}" );
			foreach ( $rows as $row ) {
				$wpdb->insert( $table, (array) $row );
			}
		}
		$count_after = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		$results[] = "sys_firma3: {$count_after}";

		return array(
			'success' => true,
			'message' => 'Custom tables: ' . implode( ', ', $results ),
		);
	}

	private function migrate_testimonials() {
		global $wpdb;

		$testimonials = $this->old_db->get_results(
			"SELECT ID, post_title, post_content, post_excerpt, post_date, post_status
			 FROM wp_posts WHERE post_type = 'testimonial' AND post_status = 'publish'"
		);

		$count = 0;
		foreach ( $testimonials as $t ) {
			if ( $this->get_wp_id( 'wp_testimonial', $t->ID ) ) {
				$count++;
				continue;
			}

			$post_id = wp_insert_post( array(
				'post_type'    => 'testimonial',
				'post_title'   => $t->post_title,
				'post_content' => wp_kses_post( $t->post_content ),
				'post_excerpt' => $t->post_excerpt,
				'post_status'  => 'publish',
				'post_date'    => $t->post_date,
			) );

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Copy testimonial meta
				$metas = $this->old_db->get_results(
					$this->old_db->prepare( "SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = %d", $t->ID )
				);
				foreach ( $metas as $m ) {
					update_post_meta( $post_id, $m->meta_key, $m->meta_value );
				}
				$this->save_mapping( 'wp_testimonial', $t->ID, $post_id );
				$count++;
			}
		}

		return array( 'success' => true, 'message' => sprintf( 'Testimonials: %d imported.', $count ) );
	}

	private function migrate_coupons() {
		global $wpdb;

		$coupons = $this->old_db->get_results(
			"SELECT ID, post_title, post_content, post_excerpt, post_date, post_status
			 FROM wp_posts WHERE post_type = 'shop_coupon' AND post_status = 'publish'"
		);

		$count = 0;
		foreach ( $coupons as $c ) {
			if ( $this->get_wp_id( 'wp_coupon', $c->ID ) ) {
				$count++;
				continue;
			}

			// Check if coupon code already exists
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'shop_coupon' AND post_title = %s",
				$c->post_title
			) );
			if ( $exists ) {
				$this->save_mapping( 'wp_coupon', $c->ID, $exists );
				$count++;
				continue;
			}

			$post_id = wp_insert_post( array(
				'post_type'    => 'shop_coupon',
				'post_title'   => $c->post_title,
				'post_content' => $c->post_content,
				'post_excerpt' => $c->post_excerpt,
				'post_status'  => 'publish',
				'post_date'    => $c->post_date,
			) );

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$metas = $this->old_db->get_results(
					$this->old_db->prepare( "SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = %d", $c->ID )
				);
				foreach ( $metas as $m ) {
					update_post_meta( $post_id, $m->meta_key, $m->meta_value );
				}
				$this->save_mapping( 'wp_coupon', $c->ID, $post_id );
				$count++;
			}
		}

		return array( 'success' => true, 'message' => sprintf( 'Coupons: %d imported.', $count ) );
	}

	private function table_exists( $table, $wpdb ) {
		return (bool) $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
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
