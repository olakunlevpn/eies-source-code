<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_WP_Orders extends EIES_Migration_Base {

	private $batch_size = 100;
	private $old_db;

	public function run() {
		$this->connect_old_wp();

		if ( ! $this->old_db || ! $this->old_db->dbh ) {
			return array( 'success' => false, 'message' => 'Cannot connect to old WordPress database.' );
		}

		$total = (int) $this->old_db->get_var(
			"SELECT COUNT(*) FROM wp_posts WHERE post_type = 'shop_order' AND post_status != 'trash'"
		);

		if ( ! $total ) {
			return array( 'success' => false, 'message' => 'No orders found.' );
		}

		$already_done = $this->get_mapping_count( 'wp_order' );
		if ( $already_done >= $total ) {
			return array( 'success' => true, 'message' => sprintf( 'All %d orders already imported.', $already_done ) );
		}

		global $wpdb;
		$offset = 0;
		$count = 0;
		$skipped = 0;

		while ( $offset < $total ) {
			$orders = $this->old_db->get_results(
				$this->old_db->prepare(
					"SELECT ID, post_status, post_date, post_date_gmt, post_modified, post_modified_gmt, post_excerpt
					 FROM wp_posts
					 WHERE post_type = 'shop_order' AND post_status != 'trash'
					 ORDER BY ID ASC
					 LIMIT %d OFFSET %d",
					$this->batch_size, $offset
				)
			);

			if ( empty( $orders ) ) break;

			foreach ( $orders as $order ) {
				if ( $this->get_wp_id( 'wp_order', $order->ID ) ) {
					$count++;
					continue;
				}

				// Create order post
				$new_order_id = $wpdb->insert(
					$wpdb->posts,
					array(
						'post_author'           => 1,
						'post_date'             => $order->post_date,
						'post_date_gmt'         => $order->post_date_gmt,
						'post_content'          => '',
						'post_title'            => 'Order',
						'post_excerpt'          => $order->post_excerpt ?: '',
						'post_status'           => $order->post_status,
						'comment_status'        => 'open',
						'ping_status'           => 'closed',
						'post_password'         => '',
						'post_name'             => 'order-' . $order->ID,
						'to_ping'               => '',
						'pinged'                => '',
						'post_modified'         => $order->post_modified,
						'post_modified_gmt'     => $order->post_modified_gmt,
						'post_content_filtered' => '',
						'post_parent'           => 0,
						'guid'                  => '',
						'menu_order'            => 0,
						'post_type'             => 'shop_order',
						'post_mime_type'        => '',
						'comment_count'         => 0,
					)
				);

				$new_id = $wpdb->insert_id;
				if ( ! $new_id ) {
					$skipped++;
					continue;
				}

				// Copy all order meta
				$this->copy_order_meta( $order->ID, $new_id );

				// Copy order items
				$this->copy_order_items( $order->ID, $new_id );

				// Copy order notes (comments)
				$this->copy_order_notes( $order->ID, $new_id );

				$this->save_mapping( 'wp_order', $order->ID, $new_id );
				$count++;
			}

			$offset += $this->batch_size;
		}

		return array(
			'success' => true,
			'message' => sprintf( 'Orders: %d imported, %d skipped.', $count, $skipped ),
		);
	}

	private function copy_order_meta( $old_order_id, $new_order_id ) {
		global $wpdb;

		$metas = $this->old_db->get_results(
			$this->old_db->prepare(
				"SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = %d",
				$old_order_id
			)
		);

		foreach ( $metas as $meta ) {
			$value = $meta->meta_value;

			// Re-map customer user ID
			if ( $meta->meta_key === '_customer_user' && $value > 0 ) {
				$new_user_id = $this->get_wp_id( 'wp_user', $value );
				if ( $new_user_id && $new_user_id > 0 ) {
					$value = $new_user_id;
				}
				// If not found in wp_user mapping, try moodle user mapping
				if ( ! $new_user_id || $new_user_id == 0 ) {
					// User might exist by email
					$old_email = $this->old_db->get_var(
						$this->old_db->prepare( "SELECT user_email FROM wp_users WHERE ID = %d", $meta->meta_value )
					);
					if ( $old_email ) {
						$found = email_exists( $old_email );
						if ( $found ) $value = $found;
					}
				}
			}

			$wpdb->insert(
				$wpdb->postmeta,
				array(
					'post_id'    => $new_order_id,
					'meta_key'   => $meta->meta_key,
					'meta_value' => $value,
				)
			);
		}
	}

	private function copy_order_items( $old_order_id, $new_order_id ) {
		global $wpdb;

		$items = $this->old_db->get_results(
			$this->old_db->prepare(
				"SELECT order_item_id, order_item_name, order_item_type
				 FROM wp_woocommerce_order_items
				 WHERE order_id = %d",
				$old_order_id
			)
		);

		foreach ( $items as $item ) {
			$wpdb->insert(
				$wpdb->prefix . 'woocommerce_order_items',
				array(
					'order_item_name' => $item->order_item_name,
					'order_item_type' => $item->order_item_type,
					'order_id'        => $new_order_id,
				)
			);
			$new_item_id = $wpdb->insert_id;

			if ( $new_item_id ) {
				// Copy item meta
				$item_metas = $this->old_db->get_results(
					$this->old_db->prepare(
						"SELECT meta_key, meta_value FROM wp_woocommerce_order_itemmeta WHERE order_item_id = %d",
						$item->order_item_id
					)
				);

				foreach ( $item_metas as $im ) {
					$value = $im->meta_value;

					// H6 FIX: Remap product IDs to new course IDs
					if ( in_array( $im->meta_key, array( '_product_id', '_variation_id' ), true ) && $value > 0 ) {
						$new_id = $this->get_wp_id( 'wp_product', $value );
						if ( $new_id && $new_id > 0 ) {
							$value = $new_id;
						}
					}

					$wpdb->insert(
						$wpdb->prefix . 'woocommerce_order_itemmeta',
						array(
							'order_item_id' => $new_item_id,
							'meta_key'      => $im->meta_key,
							'meta_value'    => $value,
						)
					);
				}
			}
		}
	}

	private function copy_order_notes( $old_order_id, $new_order_id ) {
		global $wpdb;

		$notes = $this->old_db->get_results(
			$this->old_db->prepare(
				"SELECT comment_author, comment_author_email, comment_date, comment_date_gmt,
				        comment_content, comment_approved, comment_type, comment_agent
				 FROM wp_comments
				 WHERE comment_post_ID = %d AND comment_type = 'order_note'",
				$old_order_id
			)
		);

		foreach ( $notes as $note ) {
			$wpdb->insert(
				$wpdb->comments,
				array(
					'comment_post_ID'      => $new_order_id,
					'comment_author'       => $note->comment_author,
					'comment_author_email' => $note->comment_author_email,
					'comment_author_url'   => '',
					'comment_author_IP'    => '',
					'comment_date'         => $note->comment_date,
					'comment_date_gmt'     => $note->comment_date_gmt,
					'comment_content'      => $note->comment_content,
					'comment_karma'        => 0,
					'comment_approved'     => $note->comment_approved,
					'comment_agent'        => $note->comment_agent ?: '',
					'comment_type'         => 'order_note',
					'comment_parent'       => 0,
					'user_id'              => 0,
				)
			);
		}
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
