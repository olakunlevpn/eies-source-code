<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_Categories extends EIES_Migration_Base {

	public function run() {
		$table = $this->moodle_table( 'course_categories' );
		$categories = $this->moodle_db->get_results(
			"SELECT id, name, parent, depth, sortorder FROM {$table} ORDER BY depth ASC, sortorder ASC"
		);

		if ( empty( $categories ) ) {
			return array( 'success' => false, 'message' => 'No categories found in Moodle.' );
		}

		$count = 0;

		foreach ( $categories as $cat ) {
			// Skip if already migrated
			if ( $this->get_wp_id( 'category', $cat->id ) ) {
				$count++;
				continue;
			}

			$parent_wp_id = 0;
			if ( $cat->parent > 0 ) {
				$parent_wp_id = (int) $this->get_wp_id( 'category', $cat->parent );
			}

			$name = trim( $cat->name );

			// Check if term already exists
			$existing = term_exists( $name, 'stm_lms_course_taxonomy', $parent_wp_id );

			if ( $existing ) {
				$term_id = is_array( $existing ) ? $existing['term_id'] : $existing;
			} else {
				$result = wp_insert_term( $name, 'stm_lms_course_taxonomy', array(
					'parent' => $parent_wp_id,
				) );

				if ( is_wp_error( $result ) ) {
					continue;
				}

				$term_id = $result['term_id'];
			}

			$this->save_mapping( 'category', $cat->id, $term_id );
			$count++;
		}

		return array(
			'success' => true,
			'message' => sprintf( '%d categories migrated successfully.', $count ),
		);
	}
}
