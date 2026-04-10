<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_WP_Data extends EIES_Migration_Base {

	public function run() {
		// Connect to old WordPress database
		$old_db = new wpdb(
			defined( 'MOODLE_DB_USER' ) ? MOODLE_DB_USER : 'root',
			defined( 'MOODLE_DB_PASS' ) ? MOODLE_DB_PASS : '',
			'marceloeies_restore',
			defined( 'MOODLE_DB_HOST' ) ? MOODLE_DB_HOST : 'localhost'
		);

		if ( ! $old_db->dbh ) {
			return array( 'success' => false, 'message' => 'Cannot connect to old WordPress database (marceloeies_restore).' );
		}

		$old_db->set_charset( $old_db->dbh, 'utf8mb4' );

		// Get old products with all data
		$products = $old_db->get_results(
			"SELECT p.ID, p.post_title, p.post_content, p.post_excerpt,
			        att.meta_value as file_path, img.post_mime_type,
			        price.meta_value as price, sale.meta_value as sale_price
			 FROM wp_posts p
			 LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
			 LEFT JOIN wp_postmeta att ON pm.meta_value = att.post_id AND att.meta_key = '_wp_attached_file'
			 LEFT JOIN wp_posts img ON pm.meta_value = img.ID
			 LEFT JOIN wp_postmeta price ON p.ID = price.post_id AND price.meta_key = '_regular_price'
			 LEFT JOIN wp_postmeta sale ON p.ID = sale.post_id AND sale.meta_key = '_sale_price'
			 WHERE p.post_type = 'product' AND p.post_status = 'publish'"
		);

		if ( empty( $products ) ) {
			return array( 'success' => false, 'message' => 'No products found in old WordPress database.' );
		}

		// Build normalized lookup
		$old_map = array();
		foreach ( $products as $p ) {
			$old_map[ $this->normalize( $p->post_title ) ] = $p;
		}

		// Get migrated courses
		global $wpdb;
		$courses = $wpdb->get_results(
			"SELECT p.ID, p.post_title, p.post_content, p.post_excerpt,
			        pm.meta_value as has_thumbnail
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
			 WHERE p.post_type = 'stm-courses' AND p.post_status IN ('publish', 'draft')
			 AND p.ID IN (SELECT wp_id FROM {$wpdb->prefix}eies_migration_map WHERE entity_type = 'course')"
		);

		$img_count = 0;
		$desc_count = 0;
		$price_count = 0;
		$matched = 0;

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$old_uploads = '/home/marceloeies/public_html/wp-content/uploads/';

		foreach ( $courses as $course ) {
			$key = $this->normalize( $course->post_title );
			$match = isset( $old_map[ $key ] ) ? $old_map[ $key ] : null;

			// Partial match
			if ( ! $match ) {
				foreach ( $old_map as $ok => $ov ) {
					if ( strlen( $key ) > 10 && strlen( $ok ) > 10 && ( strpos( $key, $ok ) !== false || strpos( $ok, $key ) !== false ) ) {
						$match = $ov;
						break;
					}
				}
			}

			if ( ! $match ) continue;
			$matched++;

			// 1. Import image (only if course has NO thumbnail)
			$has_thumb = ! empty( $course->has_thumbnail ) && $course->has_thumbnail > 0;
			if ( ! $has_thumb && ! empty( $match->file_path ) ) {
				$source = $old_uploads . $match->file_path;
				if ( file_exists( $source ) ) {
					$att_id = $this->copy_and_attach( $source, $match->file_path, $match->post_mime_type ?: 'image/jpeg' );
					if ( $att_id ) {
						set_post_thumbnail( $course->ID, $att_id );
						$img_count++;
					}
				}
			}

			// 2. Import description (only if course has empty content)
			$has_content = ! empty( $course->post_content ) && strlen( trim( wp_strip_all_tags( $course->post_content ) ) ) > 20;
			if ( ! $has_content && ! empty( $match->post_content ) && strlen( trim( $match->post_content ) ) > 20 ) {
				$clean_desc = $this->clean_wpbakery( $match->post_content );
				$clean_excerpt = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $clean_desc ) ) );
				if ( strlen( $clean_excerpt ) > 300 ) {
					$clean_excerpt = substr( $clean_excerpt, 0, 297 ) . '...';
				}

				wp_update_post( array(
					'ID'           => $course->ID,
					'post_content' => $clean_desc,
					'post_excerpt' => $clean_excerpt,
				) );
				$desc_count++;
			}

			// 3. Import price (only if course price is 0 or empty)
			$current_price = get_post_meta( $course->ID, 'price', true );
			if ( ( empty( $current_price ) || $current_price == '0' ) && ! empty( $match->price ) && $match->price > 0 ) {
				update_post_meta( $course->ID, 'price', $match->price );
				if ( ! empty( $match->sale_price ) && $match->sale_price > 0 ) {
					update_post_meta( $course->ID, 'sale_price', $match->sale_price );
				}
				update_post_meta( $course->ID, 'single_sale', '1' );
				$price_count++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf(
				'Matched %d courses. Images: %d imported. Descriptions: %d imported. Prices: %d imported.',
				$matched, $img_count, $desc_count, $price_count
			),
		);
	}

	private function normalize( $str ) {
		$str = mb_strtolower( trim( $str ) );
		$str = preg_replace( '/[^a-z0-9\s]/', '', $str );
		$str = preg_replace( '/\s+/', ' ', $str );
		return $str;
	}

	private function clean_wpbakery( $content ) {
		// Remove WPBakery/Visual Composer shortcodes
		$content = preg_replace( '/\[\/?(vc_[a-z_]+|rev_slider)[^\]]*\]/', '', $content );
		// Remove empty paragraphs
		$content = preg_replace( '/<p>\s*<\/p>/', '', $content );
		// Remove Moodle pluginfile URLs
		$content = preg_replace( '/@@PLUGINFILE@@/', '', $content );
		// Remove image tags pointing to old domains
		$content = preg_replace( '/<img[^>]*src=["\']https?:\/\/virtual\.eies\.com\.bo[^"\']*["\'][^>]*>/', '', $content );
		// Clean up whitespace
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

		if ( ! copy( $source, $target ) ) {
			return false;
		}

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
}
