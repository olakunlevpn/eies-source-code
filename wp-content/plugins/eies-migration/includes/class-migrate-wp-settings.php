<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_WP_Settings extends EIES_Migration_Base {

	private $old_db;

	public function run() {
		$this->connect_old_wp();

		if ( ! $this->old_db || ! $this->old_db->dbh ) {
			return array( 'success' => false, 'message' => 'Cannot connect to old WordPress database.' );
		}

		$results = array();
		$results[] = $this->migrate_site_identity();
		$results[] = $this->migrate_woocommerce_settings();
		$results[] = $this->migrate_pages();
		$results[] = $this->migrate_media();
		$results[] = $this->migrate_cf7_forms();
		$results[] = $this->migrate_menu();

		$messages = array_filter( array_column( $results, 'message' ) );

		return array(
			'success' => true,
			'message' => implode( ' | ', $messages ),
		);
	}

	private function migrate_site_identity() {
		$settings = array(
			'blogname', 'blogdescription', 'admin_email', 'WPLANG',
			'timezone_string', 'date_format', 'time_format',
			'start_of_week', 'permalink_structure',
		);

		$count = 0;
		foreach ( $settings as $key ) {
			$val = $this->old_db->get_var(
				$this->old_db->prepare( "SELECT option_value FROM wp_options WHERE option_name = %s", $key )
			);
			if ( $val !== null && $val !== '' ) {
				update_option( $key, $val );
				$count++;
			}
		}

		return array( 'success' => true, 'message' => sprintf( 'Site identity: %d settings.', $count ) );
	}

	private function migrate_woocommerce_settings() {
		$wc_settings = array(
			'woocommerce_currency', 'woocommerce_currency_pos',
			'woocommerce_price_thousand_sep', 'woocommerce_price_decimal_sep',
			'woocommerce_price_num_decimals', 'woocommerce_default_country',
			'woocommerce_calc_taxes', 'woocommerce_weight_unit',
			'woocommerce_dimension_unit', 'woocommerce_store_address',
			'woocommerce_store_city', 'woocommerce_store_postcode',
			'woocommerce_default_customer_address',
		);

		// Also copy currency switcher settings
		$wc_settings[] = 'woocs';

		$count = 0;
		foreach ( $wc_settings as $key ) {
			$val = $this->old_db->get_var(
				$this->old_db->prepare( "SELECT option_value FROM wp_options WHERE option_name = %s", $key )
			);
			if ( $val !== null ) {
				update_option( $key, maybe_unserialize( $val ) );
				$count++;
			}
		}

		// Set WooCommerce pages to existing pages
		$this->set_wc_pages();

		return array( 'success' => true, 'message' => sprintf( 'WooCommerce: %d settings.', $count ) );
	}

	private function set_wc_pages() {
		$page_map = array(
			'woocommerce_shop_page_id'     => 'Tienda',
			'woocommerce_cart_page_id'      => 'Carrito',
			'woocommerce_checkout_page_id'  => 'Finalizar compra',
			'woocommerce_myaccount_page_id' => 'Mi cuenta',
		);

		global $wpdb;
		foreach ( $page_map as $option => $title ) {
			$page_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_title = %s AND post_status = 'publish' LIMIT 1",
				$title
			) );
			if ( $page_id ) {
				update_option( $option, $page_id );
			}
		}
	}

	private function migrate_pages() {
		global $wpdb;

		$important_pages = array(
			'Inicio', 'Nosotros', 'Contacto', 'Tienda', 'Carrito',
			'Finalizar compra', 'Mi cuenta', 'Términos y Condiciones',
			'Política de privacidad', 'Galería de Fotos',
			'Galería Nº1', 'Galería Nº2', 'Galería Nº3',
		);

		$placeholders = implode( ',', array_fill( 0, count( $important_pages ), '%s' ) );
		$query = $this->old_db->prepare(
			"SELECT ID, post_title, post_content, post_excerpt, post_name, post_date, post_status, menu_order
			 FROM wp_posts
			 WHERE post_type = 'page' AND post_status = 'publish'
			 AND post_title IN ({$placeholders})",
			...$important_pages
		);
		$pages = $this->old_db->get_results( $query );

		$count = 0;
		foreach ( $pages as $page ) {
			if ( $this->get_wp_id( 'wp_page', $page->ID ) ) {
				$count++;
				continue;
			}

			// Check if page already exists by title
			$existing = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_title = %s AND post_status = 'publish' LIMIT 1",
				$page->post_title
			) );

			if ( $existing ) {
				// Update existing page content
				wp_update_post( array(
					'ID'           => $existing,
					'post_content' => $this->clean_page_content( $page->post_content ),
					'post_excerpt' => $page->post_excerpt,
				) );
				$this->save_mapping( 'wp_page', $page->ID, $existing );
			} else {
				$new_id = wp_insert_post( array(
					'post_type'    => 'page',
					'post_title'   => $page->post_title,
					'post_content' => $this->clean_page_content( $page->post_content ),
					'post_excerpt' => $page->post_excerpt,
					'post_name'    => $page->post_name,
					'post_status'  => 'publish',
					'post_date'    => $page->post_date,
					'menu_order'   => $page->menu_order,
				) );
				if ( $new_id && ! is_wp_error( $new_id ) ) {
					$this->save_mapping( 'wp_page', $page->ID, $new_id );
				}
			}
			$count++;
		}

		// Set homepage
		$home_id = $wpdb->get_var(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_title = 'Inicio' AND post_status = 'publish' LIMIT 1"
		);
		if ( $home_id ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home_id );
		}

		return array( 'success' => true, 'message' => sprintf( 'Pages: %d imported.', $count ) );
	}

	private function migrate_media() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$old_uploads = '/home/marceloeies/public_html/wp-content/uploads/';

		// Get attachments from old site that are used by pages/products
		$attachments = $this->old_db->get_results(
			"SELECT ID, post_title, post_mime_type, post_date
			 FROM wp_posts WHERE post_type = 'attachment' AND post_status = 'inherit'
			 ORDER BY ID ASC"
		);

		$count = 0;
		$skipped = 0;

		foreach ( $attachments as $att ) {
			if ( $this->get_wp_id( 'wp_media', $att->ID ) ) {
				$count++;
				continue;
			}

			$file_path = $this->old_db->get_var(
				$this->old_db->prepare( "SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = '_wp_attached_file'", $att->ID )
			);

			if ( empty( $file_path ) ) {
				$skipped++;
				continue;
			}

			$source = $old_uploads . $file_path;
			if ( ! file_exists( $source ) ) {
				$skipped++;
				continue;
			}

			$upload_dir = wp_upload_dir();
			$filename = sanitize_file_name( basename( $file_path ) );
			$target_dir = $upload_dir['path'];
			if ( ! is_dir( $target_dir ) ) wp_mkdir_p( $target_dir );

			$target = $target_dir . '/' . $filename;
			$pathinfo = pathinfo( $filename );
			$ext = isset( $pathinfo['extension'] ) ? '.' . $pathinfo['extension'] : '';
			$i = 1;
			while ( file_exists( $target ) ) {
				$filename = $pathinfo['filename'] . '-' . $i . $ext;
				$target = $target_dir . '/' . $filename;
				$i++;
			}

			if ( ! copy( $source, $target ) ) {
				$skipped++;
				continue;
			}

			$new_att = array(
				'post_mime_type' => $att->post_mime_type,
				'post_title'     => $att->post_title,
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_date'      => $att->post_date,
			);

			$new_att_id = wp_insert_attachment( $new_att, $target );
			if ( $new_att_id && ! is_wp_error( $new_att_id ) ) {
				$rel_path = ltrim( $upload_dir['subdir'] . '/' . $filename, '/' );
				update_post_meta( $new_att_id, '_wp_attached_file', $rel_path );

				if ( strpos( $att->post_mime_type, 'image/' ) === 0 ) {
					$metadata = wp_generate_attachment_metadata( $new_att_id, $target );
					wp_update_attachment_metadata( $new_att_id, $metadata );
				}

				$this->save_mapping( 'wp_media', $att->ID, $new_att_id );
				$count++;
			} else {
				@unlink( $target );
				$skipped++;
			}
		}

		return array( 'success' => true, 'message' => sprintf( 'Media: %d imported, %d skipped.', $count, $skipped ) );
	}

	private function migrate_cf7_forms() {
		global $wpdb;

		$forms = $this->old_db->get_results(
			"SELECT ID, post_title, post_content, post_date
			 FROM wp_posts WHERE post_type = 'wpcf7_contact_form' AND post_status = 'publish'"
		);

		$count = 0;
		foreach ( $forms as $form ) {
			if ( $this->get_wp_id( 'wp_cf7', $form->ID ) ) {
				$count++;
				continue;
			}

			$new_id = wp_insert_post( array(
				'post_type'    => 'wpcf7_contact_form',
				'post_title'   => $form->post_title,
				'post_content' => $form->post_content,
				'post_status'  => 'publish',
				'post_date'    => $form->post_date,
			) );

			if ( $new_id && ! is_wp_error( $new_id ) ) {
				// Copy CF7 meta
				$metas = $this->old_db->get_results(
					$this->old_db->prepare( "SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = %d", $form->ID )
				);
				foreach ( $metas as $m ) {
					update_post_meta( $new_id, $m->meta_key, maybe_unserialize( $m->meta_value ) );
				}
				$this->save_mapping( 'wp_cf7', $form->ID, $new_id );
				$count++;
			}
		}

		return array( 'success' => true, 'message' => sprintf( 'CF7 forms: %d.', $count ) );
	}

	private function migrate_menu() {
		// Create main menu
		$menu_name = 'Main menu';
		$menu_exists = wp_get_nav_menu_object( $menu_name );

		if ( $menu_exists ) {
			return array( 'success' => true, 'message' => 'Menu: already exists.' );
		}

		$menu_id = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $menu_id ) ) {
			return array( 'success' => false, 'message' => 'Menu: failed to create.' );
		}

		// Add standard menu items
		global $wpdb;

		$items = array(
			array( 'title' => 'Inicio', 'type' => 'page' ),
			array( 'title' => 'Cursos', 'type' => 'page' ),
			array( 'title' => 'Tienda', 'type' => 'page' ),
			array( 'title' => 'Nosotros', 'type' => 'page' ),
			array( 'title' => 'Galería de Fotos', 'type' => 'page' ),
			array( 'title' => 'Contacto', 'type' => 'page' ),
		);

		$order = 1;
		foreach ( $items as $item ) {
			$page_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_title = %s AND post_status = 'publish' LIMIT 1",
				$item['title']
			) );

			if ( $page_id ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => $item['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page_id,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $order,
				) );
			}
			$order++;
		}

		// Assign menu to primary location
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		return array( 'success' => true, 'message' => sprintf( 'Menu: created with %d items.', count( $items ) ) );
	}

	private function clean_page_content( $content ) {
		if ( empty( $content ) ) return '';
		// Remove WPBakery shortcodes
		$content = preg_replace( '/\[\/?(vc_[a-z_]+|rev_slider)[^\]]*\]/', '', $content );
		// Clean old domain URLs
		$content = str_replace( 'http://eies.com.bo', 'https://testeoprevio.eies.com.bo', $content );
		$content = str_replace( 'https://eies.com.bo', 'https://testeoprevio.eies.com.bo', $content );
		$content = str_replace( 'http://eies.test', 'https://testeoprevio.eies.com.bo', $content );
		return $content;
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
