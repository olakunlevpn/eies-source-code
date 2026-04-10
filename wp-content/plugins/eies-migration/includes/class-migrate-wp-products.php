<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_WP_Products extends EIES_Migration_Base {

	private $old_db;

	public function run() {
		$this->connect_old_wp();

		if ( ! $this->old_db || ! $this->old_db->dbh ) {
			return array( 'success' => false, 'message' => 'Cannot connect to old WordPress database.' );
		}

		// Get all published products from old site
		$products = $this->old_db->get_results(
			"SELECT p.ID, p.post_title, p.post_content, p.post_excerpt, p.post_date, p.post_status
			 FROM wp_posts p
			 WHERE p.post_type = 'product' AND p.post_status = 'publish'
			 ORDER BY p.ID ASC"
		);

		if ( empty( $products ) ) {
			return array( 'success' => false, 'message' => 'No products found.' );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$old_uploads = '/home/marceloeies/public_html/wp-content/uploads/';
		$created = 0;
		$skipped = 0;
		$cat_created = 0;

		// First, migrate product categories to LMS taxonomy
		$cat_created = $this->migrate_product_categories();

		foreach ( $products as $product ) {
			// Skip if already processed
			if ( $this->get_wp_id( 'wp_product', $product->ID ) ) {
				$skipped++;
				continue;
			}

			// Skip if a course with this exact title already exists (from Moodle migration)
			$existing = get_page_by_title( $product->post_title, OBJECT, 'stm-courses' );
			if ( $existing ) {
				$this->save_mapping( 'wp_product', $product->ID, $existing->ID );
				$skipped++;
				continue;
			}

			// Also check by normalized name match against existing courses
			if ( $this->course_exists_by_name( $product->post_title ) ) {
				$skipped++;
				$this->save_mapping( 'wp_product', $product->ID, 0 );
				continue;
			}

			// Clean description
			$content = $this->clean_wpbakery( $product->post_content );
			$excerpt = $product->post_excerpt;
			if ( empty( $excerpt ) && ! empty( $content ) ) {
				$excerpt = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $content ) ) );
				if ( strlen( $excerpt ) > 300 ) {
					$excerpt = substr( $excerpt, 0, 297 ) . '...';
				}
			}

			// Create course
			$post_id = wp_insert_post( array(
				'post_type'    => 'stm-courses',
				'post_title'   => trim( $product->post_title ),
				'post_content' => $content,
				'post_excerpt' => wp_kses_post( $excerpt ),
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_date'    => $product->post_date,
			) );

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			// Set price
			$price = $this->old_db->get_var( $this->old_db->prepare(
				"SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = '_regular_price'",
				$product->ID
			) );
			$sale_price = $this->old_db->get_var( $this->old_db->prepare(
				"SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = '_sale_price'",
				$product->ID
			) );

			if ( ! empty( $price ) && $price > 0 ) {
				update_post_meta( $post_id, 'price', $price );
				update_post_meta( $post_id, 'single_sale', '1' );
			} else {
				update_post_meta( $post_id, 'price', '0' );
			}
			if ( ! empty( $sale_price ) && $sale_price > 0 ) {
				update_post_meta( $post_id, 'sale_price', $sale_price );
			}

			update_post_meta( $post_id, 'current_students', '0' );
			update_post_meta( $post_id, 'views', '0' );

			// Set category
			$this->assign_product_category( $product->ID, $post_id );

			// Copy thumbnail
			$thumb_id = $this->old_db->get_var( $this->old_db->prepare(
				"SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = '_thumbnail_id'",
				$product->ID
			) );

			if ( $thumb_id ) {
				$file_path = $this->old_db->get_var( $this->old_db->prepare(
					"SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = '_wp_attached_file'",
					$thumb_id
				) );
				$mime = $this->old_db->get_var( $this->old_db->prepare(
					"SELECT post_mime_type FROM wp_posts WHERE ID = %d",
					$thumb_id
				) );

				if ( $file_path ) {
					$source = $old_uploads . $file_path;
					if ( file_exists( $source ) ) {
						$att_id = $this->copy_and_attach( $source, $file_path, $mime ?: 'image/jpeg' );
						if ( $att_id ) {
							set_post_thumbnail( $post_id, $att_id );
						}
					}
				}
			}

			$this->save_mapping( 'wp_product', $product->ID, $post_id );
			$created++;
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'Products: %d created as courses, %d skipped (already exist). %d new categories created.',
				$created, $skipped, $cat_created
			),
		);
	}

	private function course_exists_by_name( $title ) {
		global $wpdb;
		$norm = $this->normalize( $title );
		$courses = $wpdb->get_col( "SELECT post_title FROM {$wpdb->posts} WHERE post_type = 'stm-courses' AND post_status IN ('publish','draft')" );
		foreach ( $courses as $ct ) {
			$cn = $this->normalize( $ct );
			if ( $norm === $cn ) return true;
			if ( strlen( $norm ) > 10 && strlen( $cn ) > 10 && ( strpos( $norm, $cn ) !== false || strpos( $cn, $norm ) !== false ) ) return true;
		}
		return false;
	}

	private function normalize( $str ) {
		$str = mb_strtolower( trim( $str ) );
		$str = preg_replace( '/[^a-z0-9\s]/', '', $str );
		$str = preg_replace( '/\s+/', ' ', $str );
		return $str;
	}

	private function migrate_product_categories() {
		$cats = $this->old_db->get_results(
			"SELECT t.term_id, t.name, tt.parent, tt.count
			 FROM wp_terms t
			 JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
			 WHERE tt.taxonomy = 'product_cat'
			 ORDER BY tt.parent ASC, t.name ASC"
		);

		$count = 0;
		foreach ( $cats as $cat ) {
			if ( $this->get_wp_id( 'wp_product_cat', $cat->term_id ) ) continue;

			$existing = term_exists( $cat->name, 'stm_lms_course_taxonomy' );
			if ( $existing ) {
				$term_id = is_array( $existing ) ? $existing['term_id'] : $existing;
			} else {
				$result = wp_insert_term( $cat->name, 'stm_lms_course_taxonomy' );
				if ( is_wp_error( $result ) ) continue;
				$term_id = $result['term_id'];
				$count++;
			}
			$this->save_mapping( 'wp_product_cat', $cat->term_id, $term_id );
		}
		return $count;
	}

	private function assign_product_category( $old_product_id, $new_course_id ) {
		$old_cat_ids = $this->old_db->get_col(
			$this->old_db->prepare(
				"SELECT tt.term_id
				 FROM wp_term_relationships tr
				 JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				 WHERE tr.object_id = %d AND tt.taxonomy = 'product_cat'",
				$old_product_id
			)
		);

		foreach ( $old_cat_ids as $old_cat_id ) {
			$new_term_id = $this->get_wp_id( 'wp_product_cat', $old_cat_id );
			if ( $new_term_id ) {
				wp_set_object_terms( $new_course_id, (int) $new_term_id, 'stm_lms_course_taxonomy', true );
			}
		}
	}

	private function clean_wpbakery( $content ) {
		if ( empty( $content ) ) return '';
		$content = preg_replace( '/\[\/?(vc_[a-z_]+|rev_slider)[^\]]*\]/', '', $content );
		$content = preg_replace( '/<p>\s*<\/p>/', '', $content );
		$content = preg_replace( '/@@PLUGINFILE@@/', '', $content );
		$content = preg_replace( '/<img[^>]*src=["\']https?:\/\/virtual\.eies\.com\.bo[^"\']*["\'][^>]*>/', '', $content );
		$content = trim( preg_replace( '/\n{3,}/', "\n\n", $content ) );
		return wp_kses_post( $content );
	}

	private function copy_and_attach( $source, $rel_path, $mime_type ) {
		$upload_dir = wp_upload_dir();
		$filename = sanitize_file_name( basename( $rel_path ) );
		$target_dir = $upload_dir['path'];
		if ( ! is_dir( $target_dir ) ) {
			wp_mkdir_p( $target_dir );
		}

		$target = $target_dir . '/' . $filename;
		$pathinfo = pathinfo( $filename );
		$ext = isset( $pathinfo['extension'] ) ? '.' . $pathinfo['extension'] : '';
		$i = 1;
		while ( file_exists( $target ) ) {
			$filename = $pathinfo['filename'] . '-' . $i . $ext;
			$target = $target_dir . '/' . $filename;
			$i++;
		}

		if ( ! copy( $source, $target ) ) return false;

		$attachment = array(
			'post_mime_type' => $mime_type,
			'post_title'     => $pathinfo['filename'],
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$att_id = wp_insert_attachment( $attachment, $target );
		if ( is_wp_error( $att_id ) || ! $att_id ) {
			@unlink( $target );
			return false;
		}

		if ( strpos( $mime_type, 'image/' ) === 0 ) {
			$metadata = wp_generate_attachment_metadata( $att_id, $target );
			wp_update_attachment_metadata( $att_id, $metadata );
		}

		return $att_id;
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
