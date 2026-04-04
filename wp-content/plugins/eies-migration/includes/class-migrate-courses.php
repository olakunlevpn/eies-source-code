<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_Courses extends EIES_Migration_Base {

	public function run() {
		$course_table = $this->moodle_table( 'course' );
		$courses = $this->moodle_db->get_results(
			"SELECT id, category, fullname, shortname, summary, format, startdate, visible
			 FROM {$course_table}
			 WHERE id > 1
			 ORDER BY id ASC"
		);

		if ( empty( $courses ) ) {
			return array( 'success' => false, 'message' => 'No courses found.' );
		}

		$count = 0;
		$lessons_count = 0;

		foreach ( $courses as $course ) {
			// Skip if already migrated
			if ( $this->get_wp_id( 'course', $course->id ) ) {
				$count++;
				continue;
			}

			// Find instructor (editingteacher) for this course
			$instructor_id = $this->get_course_instructor( $course->id );
			$wp_author = 1; // default admin
			if ( $instructor_id ) {
				$mapped = $this->get_wp_id( 'user', $instructor_id );
				if ( $mapped ) {
					$wp_author = (int) $mapped;
				}
			}

			// Clean summary HTML
			$summary = $this->clean_html( $course->summary );

			// Create course post
			$post_id = wp_insert_post( array(
				'post_type'    => 'stm-courses',
				'post_title'   => trim( $course->fullname ),
				'post_content' => $summary,
				'post_status'  => $course->visible ? 'publish' : 'draft',
				'post_author'  => $wp_author,
				'post_date'    => date( 'Y-m-d H:i:s', $course->startdate ),
			) );

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			// Set category
			$wp_term_id = $this->get_wp_id( 'category', $course->category );
			if ( $wp_term_id ) {
				wp_set_object_terms( $post_id, (int) $wp_term_id, 'stm_lms_course_taxonomy' );
			}

			// Set course meta
			update_post_meta( $post_id, 'price', '0' );
			update_post_meta( $post_id, 'single_sale', '' );
			update_post_meta( $post_id, 'not_membership', '' );
			update_post_meta( $post_id, 'current_students', '0' );
			update_post_meta( $post_id, 'views', '0' );

			// Migrate sections and activities
			$lessons_count += $this->migrate_curriculum( $course->id, $post_id );

			$this->save_mapping( 'course', $course->id, $post_id );
			$count++;
		}

		return array(
			'success' => true,
			'message' => sprintf( '%d courses and %d lessons/activities migrated.', $count, $lessons_count ),
		);
	}

	private function get_course_instructor( $moodle_course_id ) {
		$ctx_table = $this->moodle_table( 'context' );
		$ra_table = $this->moodle_table( 'role_assignments' );
		$role_table = $this->moodle_table( 'role' );

		// Context level 50 = course
		$result = $this->moodle_db->get_var(
			$this->moodle_db->prepare(
				"SELECT ra.userid
				 FROM {$ra_table} ra
				 JOIN {$ctx_table} ctx ON ra.contextid = ctx.id
				 JOIN {$role_table} r ON ra.roleid = r.id
				 WHERE ctx.contextlevel = 50
				   AND ctx.instanceid = %d
				   AND r.shortname = 'editingteacher'
				 LIMIT 1",
				$moodle_course_id
			)
		);

		return $result;
	}

	private function migrate_curriculum( $moodle_course_id, $wp_course_id ) {
		global $wpdb;
		$section_table = $this->moodle_table( 'course_sections' );
		$cm_table = $this->moodle_table( 'course_modules' );
		$modules_table = $this->moodle_table( 'modules' );

		$sections = $this->moodle_db->get_results(
			$this->moodle_db->prepare(
				"SELECT id, section, name, summary, sequence
				 FROM {$section_table}
				 WHERE course = %d
				 ORDER BY section ASC",
				$moodle_course_id
			)
		);

		$items_count = 0;
		$section_order = 0;

		foreach ( $sections as $section ) {
			if ( empty( $section->sequence ) ) {
				continue;
			}

			$section_name = ! empty( $section->name )
				? $section->name
				: sprintf( 'Section %d', $section->section );

			// Create curriculum section
			$wpdb->insert(
				$wpdb->prefix . 'stm_lms_curriculum_sections',
				array(
					'title'     => $section_name,
					'course_id' => $wp_course_id,
					'order'     => $section_order,
				)
			);
			$wp_section_id = $wpdb->insert_id;
			$section_order++;

			// Get activities in this section
			$cm_ids = explode( ',', $section->sequence );
			$material_order = 0;

			foreach ( $cm_ids as $cm_id ) {
				$cm_id = (int) trim( $cm_id );
				if ( ! $cm_id ) continue;

				$cm = $this->moodle_db->get_row(
					$this->moodle_db->prepare(
						"SELECT cm.id, cm.module, cm.instance, cm.visible, m.name as module_type
						 FROM {$cm_table} cm
						 JOIN {$modules_table} m ON cm.module = m.id
						 WHERE cm.id = %d",
						$cm_id
					)
				);

				if ( ! $cm ) continue;

				$wp_post_id = $this->create_activity( $cm, $moodle_course_id, $wp_course_id );

				if ( $wp_post_id ) {
					$wpdb->insert(
						$wpdb->prefix . 'stm_lms_curriculum_materials',
						array(
							'post_id'    => $wp_post_id,
							'post_type'  => get_post_type( $wp_post_id ),
							'section_id' => $wp_section_id,
							'order'      => $material_order,
						)
					);
					$material_order++;
					$items_count++;
				}
			}
		}

		return $items_count;
	}

	private function create_activity( $cm, $moodle_course_id, $wp_course_id ) {
		$mapping_key = 'activity_' . $cm->module_type;

		// Skip if already migrated
		$existing = $this->get_wp_id( $mapping_key, $cm->id );
		if ( $existing ) return (int) $existing;

		switch ( $cm->module_type ) {
			case 'resource':
				return $this->create_resource_lesson( $cm, $wp_course_id );
			case 'page':
				return $this->create_page_lesson( $cm, $wp_course_id );
			case 'url':
				return $this->create_url_lesson( $cm, $wp_course_id );
			case 'label':
				return $this->create_label_lesson( $cm, $wp_course_id );
			case 'folder':
				return $this->create_folder_lesson( $cm, $wp_course_id );
			case 'quiz':
				// Quizzes are handled in Phase 5, save mapping for later
				$this->save_mapping( 'quiz_cm', $cm->id, $cm->instance );
				return null;
			case 'assign':
				return $this->create_assignment( $cm, $wp_course_id );
			case 'forum':
				return $this->create_forum_lesson( $cm, $wp_course_id );
			default:
				return null;
		}
	}

	private function create_resource_lesson( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'resource' );
		$resource = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, intro FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $resource ) return null;

		$content = $this->clean_html( $resource->intro );

		// Get associated file info
		$file_info = $this->get_activity_file_info( $cm->id, 'mod_resource', 'content' );
		if ( $file_info ) {
			$content .= sprintf(
				"\n\n<!-- Moodle file: %s (SHA1: %s) -->",
				esc_html( $file_info->filename ),
				esc_html( $file_info->contenthash )
			);
		}

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-lessons',
			'post_title'   => trim( $resource->name ),
			'post_content' => $content,
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->save_mapping( 'activity_resource', $cm->id, $post_id );
			return $post_id;
		}
		return null;
	}

	private function create_page_lesson( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'page' );
		$page = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, content, intro FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $page ) return null;

		$content = $this->clean_html( $page->content );

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-lessons',
			'post_title'   => trim( $page->name ),
			'post_content' => $content,
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->save_mapping( 'activity_page', $cm->id, $post_id );
			return $post_id;
		}
		return null;
	}

	private function create_url_lesson( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'url' );
		$url = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, intro, externalurl FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $url ) return null;

		$content = $this->clean_html( $url->intro );
		$content .= sprintf( "\n\n<p><a href=\"%s\" target=\"_blank\">%s</a></p>", esc_url( $url->externalurl ), esc_html( $url->name ) );

		// Check if it's a video URL
		$video_url = '';
		if ( preg_match( '/(youtube\.com|youtu\.be|vimeo\.com)/', $url->externalurl ) ) {
			$video_url = $url->externalurl;
		}

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-lessons',
			'post_title'   => trim( $url->name ),
			'post_content' => $content,
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( $video_url ) {
				update_post_meta( $post_id, 'lesson_video_url', $video_url );
				update_post_meta( $post_id, 'type', 'video' );
			}
			$this->save_mapping( 'activity_url', $cm->id, $post_id );
			return $post_id;
		}
		return null;
	}

	private function create_label_lesson( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'label' );
		$label = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, intro FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $label ) return null;

		$title = trim( $label->name );
		if ( empty( $title ) || $title === '' ) {
			$title = wp_trim_words( wp_strip_all_tags( $label->intro ), 10, '...' );
		}
		if ( empty( $title ) ) return null;

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-lessons',
			'post_title'   => $title,
			'post_content' => $this->clean_html( $label->intro ),
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->save_mapping( 'activity_label', $cm->id, $post_id );
			return $post_id;
		}
		return null;
	}

	private function create_folder_lesson( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'folder' );
		$folder = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, intro FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $folder ) return null;

		$content = $this->clean_html( $folder->intro );
		$content .= "\n\n<!-- Moodle folder - files need to be migrated -->";

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-lessons',
			'post_title'   => trim( $folder->name ),
			'post_content' => $content,
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->save_mapping( 'activity_folder', $cm->id, $post_id );
			return $post_id;
		}
		return null;
	}

	private function create_forum_lesson( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'forum' );
		$forum = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, intro FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $forum ) return null;

		$content = $this->clean_html( $forum->intro );

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-lessons',
			'post_title'   => trim( $forum->name ),
			'post_content' => $content,
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->save_mapping( 'activity_forum', $cm->id, $post_id );
			return $post_id;
		}
		return null;
	}

	private function create_assignment( $cm, $wp_course_id ) {
		$table = $this->moodle_table( 'assign' );
		$assign = $this->moodle_db->get_row(
			$this->moodle_db->prepare( "SELECT name, intro, duedate, grade FROM {$table} WHERE id = %d", $cm->instance )
		);
		if ( ! $assign ) return null;

		$post_id = wp_insert_post( array(
			'post_type'    => 'stm-assignments',
			'post_title'   => trim( $assign->name ),
			'post_content' => $this->clean_html( $assign->intro ),
			'post_status'  => $cm->visible ? 'publish' : 'draft',
			'post_author'  => get_post_field( 'post_author', $wp_course_id ),
		) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->save_mapping( 'activity_assign', $cm->id, $post_id );
			$this->save_mapping( 'assignment', $cm->instance, $post_id );
			return $post_id;
		}
		return null;
	}

	private function get_activity_file_info( $cm_id, $component, $filearea ) {
		$ctx_table = $this->moodle_table( 'context' );
		$files_table = $this->moodle_table( 'files' );

		return $this->moodle_db->get_row(
			$this->moodle_db->prepare(
				"SELECT f.id, f.filename, f.contenthash, f.mimetype, f.filesize
				 FROM {$files_table} f
				 JOIN {$ctx_table} ctx ON f.contextid = ctx.id
				 WHERE ctx.contextlevel = 70
				   AND ctx.instanceid = %d
				   AND f.component = %s
				   AND f.filearea = %s
				   AND f.filename != '.'
				   AND f.filesize > 0
				 LIMIT 1",
				$cm_id, $component, $filearea
			)
		);
	}

	private function clean_html( $html ) {
		if ( empty( $html ) ) return '';
		// Fix Moodle's pluginfile URLs - will be updated when files are migrated
		$html = preg_replace( '/@@PLUGINFILE@@/', '', $html );
		return wp_kses_post( $html );
	}
}
