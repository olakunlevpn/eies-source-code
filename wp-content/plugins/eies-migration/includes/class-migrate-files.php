<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_Files extends EIES_Migration_Base {

	private $batch_size = 50;

	public function run() {
		if ( empty( MOODLE_DATA_PATH ) ) {
			return array( 'success' => false, 'message' => 'MOODLE_DATA_PATH not set. Add it to wp-config.php.' );
		}

		if ( ! is_dir( MOODLE_DATA_PATH ) ) {
			return array( 'success' => false, 'message' => 'Moodle data path not found: ' . MOODLE_DATA_PATH );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$results = array();

		$results[] = $this->migrate_course_images();
		$results[] = $this->migrate_resource_files();
		$results[] = $this->migrate_user_avatars();

		$messages = array_filter( array_column( $results, 'message' ) );
		// I6 FIX: Use computed $all_success
		$all_success = ! in_array( false, array_column( $results, 'success' ), true );

		return array(
			'success' => $all_success,
			'message' => implode( ' | ', $messages ),
		);
	}

	private function migrate_course_images() {
		$files_table = $this->moodle_table( 'files' );
		$ctx_table = $this->moodle_table( 'context' );

		$files = $this->moodle_db->get_results(
			"SELECT f.id, f.contenthash, f.filename, f.mimetype, f.filesize, ctx.instanceid as course_id
			 FROM {$files_table} f
			 JOIN {$ctx_table} ctx ON f.contextid = ctx.id
			 WHERE f.component = 'course'
			   AND f.filearea = 'overviewfiles'
			   AND f.filesize > 0
			   AND f.filename != '.'
			   AND ctx.contextlevel = 50
			 ORDER BY f.id ASC"
		);

		if ( empty( $files ) ) {
			return array( 'success' => true, 'message' => 'No course images found.' );
		}

		$count = 0;
		$skipped = 0;

		foreach ( $files as $file ) {
			if ( $this->get_wp_id( 'file_course_img', $file->id ) ) {
				$count++;
				continue;
			}

			$wp_course_id = $this->get_wp_id( 'course', $file->course_id );
			if ( ! $wp_course_id ) {
				$skipped++;
				continue;
			}

			$attachment_id = $this->import_moodle_file( $file );
			if ( $attachment_id ) {
				set_post_thumbnail( (int) $wp_course_id, $attachment_id );
				$this->save_mapping( 'file_course_img', $file->id, $attachment_id );
				$count++;
			} else {
				$skipped++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf( 'Course images: %d imported, %d skipped.', $count, $skipped ),
		);
	}

	private function migrate_resource_files() {
		$files_table = $this->moodle_table( 'files' );
		$ctx_table = $this->moodle_table( 'context' );

		$total = (int) $this->moodle_db->get_var(
			"SELECT COUNT(*)
			 FROM {$files_table} f
			 JOIN {$ctx_table} ctx ON f.contextid = ctx.id
			 WHERE f.component = 'mod_resource'
			   AND f.filearea = 'content'
			   AND f.filesize > 0
			   AND f.filename != '.'
			   AND ctx.contextlevel = 70"
		);

		$already_done = $this->get_mapping_count( 'file_resource' );
		if ( $already_done >= $total ) {
			return array( 'success' => true, 'message' => sprintf( 'Resource files: all %d already imported.', $already_done ) );
		}

		// I3 FIX: Use keyset pagination instead of OFFSET
		$last_id = 0;
		$count = 0;
		$skipped = 0;

		while ( true ) {
			$files = $this->moodle_db->get_results(
				$this->moodle_db->prepare(
					"SELECT f.id, f.contenthash, f.filename, f.mimetype, f.filesize, ctx.instanceid as cm_id
					 FROM {$files_table} f
					 JOIN {$ctx_table} ctx ON f.contextid = ctx.id
					 WHERE f.component = 'mod_resource'
					   AND f.filearea = 'content'
					   AND f.filesize > 0
					   AND f.filename != '.'
					   AND ctx.contextlevel = 70
					   AND f.id > %d
					 ORDER BY f.id ASC
					 LIMIT %d",
					$last_id, $this->batch_size
				)
			);

			if ( empty( $files ) ) break;

			foreach ( $files as $file ) {
				$last_id = $file->id;

				if ( $this->get_wp_id( 'file_resource', $file->id ) ) {
					$count++;
					continue;
				}

				$wp_lesson_id = $this->get_wp_id( 'activity_resource', $file->cm_id );
				if ( ! $wp_lesson_id ) {
					$skipped++;
					continue;
				}

				$attachment_id = $this->import_moodle_file( $file );
				if ( $attachment_id ) {
					update_post_meta( (int) $wp_lesson_id, 'lesson_file_id', $attachment_id );
					update_post_meta( (int) $wp_lesson_id, 'lesson_file_url', wp_get_attachment_url( $attachment_id ) );

					$url = wp_get_attachment_url( $attachment_id );
					$lesson = get_post( (int) $wp_lesson_id );
					if ( $lesson ) {
						$content = $lesson->post_content;
						$content = preg_replace( '/<!-- Moodle file:.*?-->/', '', $content );
						$content .= sprintf(
							"\n\n<p><a href=\"%s\" target=\"_blank\">%s</a></p>",
							esc_url( $url ),
							esc_html( $file->filename )
						);
						wp_update_post( array(
							'ID'           => (int) $wp_lesson_id,
							'post_content' => $content,
						) );
					}

					$this->save_mapping( 'file_resource', $file->id, $attachment_id );
					$count++;
				} else {
					$skipped++;
				}
			}
		}

		return array(
			'success' => true,
			'message' => sprintf( 'Resource files: %d imported, %d skipped.', $count, $skipped ),
		);
	}

	private function migrate_user_avatars() {
		$files_table = $this->moodle_table( 'files' );
		$ctx_table = $this->moodle_table( 'context' );

		// C3 FIX: Match both f1.png AND f1.jpg
		$files = $this->moodle_db->get_results(
			"SELECT f.id, f.contenthash, f.filename, f.mimetype, f.filesize, ctx.instanceid as user_id
			 FROM {$files_table} f
			 JOIN {$ctx_table} ctx ON f.contextid = ctx.id
			 WHERE f.component = 'user'
			   AND f.filearea = 'icon'
			   AND f.filename LIKE 'f1.%'
			   AND f.filesize > 0
			   AND ctx.contextlevel = 30
			 ORDER BY f.id ASC"
		);

		if ( empty( $files ) ) {
			return array( 'success' => true, 'message' => 'No user avatars found.' );
		}

		$count = 0;
		$skipped = 0;

		foreach ( $files as $file ) {
			if ( $this->get_wp_id( 'file_avatar', $file->id ) ) {
				$count++;
				continue;
			}

			$wp_user_id = $this->get_wp_id( 'user', $file->user_id );
			if ( ! $wp_user_id ) {
				$skipped++;
				continue;
			}

			$attachment_id = $this->import_moodle_file( $file );
			if ( $attachment_id ) {
				// C2 FIX: Store URL, not attachment ID
				$avatar_url = wp_get_attachment_url( $attachment_id );
				update_user_meta( (int) $wp_user_id, 'stm_lms_user_avatar', $avatar_url );
				$this->save_mapping( 'file_avatar', $file->id, $attachment_id );
				$count++;
			} else {
				$skipped++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf( 'User avatars: %d imported, %d skipped.', $count, $skipped ),
		);
	}

	private function import_moodle_file( $file ) {
		// C1 FIX: Correct Moodle path: filedir/{first2}/{next2}/{fullhash}
		$hash = $file->contenthash;
		$source = MOODLE_DATA_PATH . substr( $hash, 0, 2 ) . '/' . substr( $hash, 2, 2 ) . '/' . $hash;

		if ( ! file_exists( $source ) ) {
			return false;
		}

		$upload_dir = wp_upload_dir();
		$target_dir = $upload_dir['path'];
		if ( ! is_dir( $target_dir ) ) {
			wp_mkdir_p( $target_dir );
		}

		$filename = sanitize_file_name( $file->filename );
		$target = $target_dir . '/' . $filename;

		$i = 1;
		$pathinfo = pathinfo( $filename );
		$ext = isset( $pathinfo['extension'] ) ? '.' . $pathinfo['extension'] : '';
		while ( file_exists( $target ) ) {
			$filename = $pathinfo['filename'] . '-' . $i . $ext;
			$target = $target_dir . '/' . $filename;
			$i++;
		}

		if ( ! copy( $source, $target ) ) {
			return false;
		}

		// I1 FIX: Use relative path for wp_insert_attachment
		$relative = $upload_dir['subdir'] . '/' . $filename;

		$attachment = array(
			'post_mime_type' => $file->mimetype,
			'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attachment_id = wp_insert_attachment( $attachment, $target );

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			@unlink( $target );
			return false;
		}

		// I2 FIX: Only generate metadata for images
		if ( strpos( $file->mimetype, 'image/' ) === 0 ) {
			$metadata = wp_generate_attachment_metadata( $attachment_id, $target );
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		return $attachment_id;
	}
}
