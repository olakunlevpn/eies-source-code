<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migration_Base {

	protected $moodle_db;
	protected $wp_db;

	public function __construct() {
		global $wpdb;
		$this->wp_db = $wpdb;
		$this->connect_moodle();
	}

	protected function connect_moodle() {
		$this->moodle_db = new wpdb(
			MOODLE_DB_USER,
			MOODLE_DB_PASS,
			MOODLE_DB_NAME,
			MOODLE_DB_HOST
		);
		// I1 FIX: Check connection succeeded
		if ( ! $this->moodle_db->dbh ) {
			wp_die( 'Failed to connect to Moodle database. Check credentials in eies-migration.php. Error: ' . $this->moodle_db->last_error );
		}
		$this->moodle_db->set_charset( $this->moodle_db->dbh, 'utf8mb4' );
	}

	protected function clean_moodle_html( $html ) {
		if ( empty( $html ) ) return '';
		$html = preg_replace( '/@@PLUGINFILE@@/', '', $html );
		return wp_kses_post( $html );
	}

	protected function moodle_table( $name ) {
		return MOODLE_DB_PREFIX . $name;
	}

	protected function save_mapping( $type, $moodle_id, $wp_id ) {
		$table = $this->wp_db->prefix . 'eies_migration_map';
		$this->wp_db->replace( $table, array(
			'entity_type' => $type,
			'moodle_id'   => $moodle_id,
			'wp_id'       => $wp_id,
		) );
	}

	protected function get_wp_id( $type, $moodle_id ) {
		$table = $this->wp_db->prefix . 'eies_migration_map';
		return $this->wp_db->get_var( $this->wp_db->prepare(
			"SELECT wp_id FROM {$table} WHERE entity_type = %s AND moodle_id = %d",
			$type, $moodle_id
		) );
	}

	protected function get_moodle_id( $type, $wp_id ) {
		$table = $this->wp_db->prefix . 'eies_migration_map';
		return $this->wp_db->get_var( $this->wp_db->prepare(
			"SELECT moodle_id FROM {$table} WHERE entity_type = %s AND wp_id = %d",
			$type, $wp_id
		) );
	}

	protected function get_mapping_count( $type ) {
		$table = $this->wp_db->prefix . 'eies_migration_map';
		return (int) $this->wp_db->get_var( $this->wp_db->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE entity_type = %s",
			$type
		) );
	}

	public function reset_all() {
		$table = $this->wp_db->prefix . 'eies_migration_map';

		// Delete migrated posts
		$post_types = array( 'stm-courses', 'stm-lessons', 'stm-quizzes', 'stm-questions', 'stm-assignments' );
		foreach ( $post_types as $pt ) {
			$this->wp_db->query( $this->wp_db->prepare(
				"DELETE pm FROM {$this->wp_db->postmeta} pm INNER JOIN {$this->wp_db->posts} p ON pm.post_id = p.ID WHERE p.post_type = %s",
				$pt
			) );
			$this->wp_db->query( $this->wp_db->prepare(
				"DELETE FROM {$this->wp_db->posts} WHERE post_type = %s",
				$pt
			) );
		}

		// Delete migrated users (except admin)
		$migrated_users = $this->wp_db->get_col( "SELECT wp_id FROM {$table} WHERE entity_type = 'user'" );
		foreach ( $migrated_users as $uid ) {
			if ( (int) $uid > 1 ) {
				$this->wp_db->delete( $this->wp_db->usermeta, array( 'user_id' => $uid ) );
				$this->wp_db->delete( $this->wp_db->users, array( 'ID' => $uid ) );
			}
		}

		// Delete taxonomy terms
		$terms = $this->wp_db->get_col( "SELECT wp_id FROM {$table} WHERE entity_type = 'category'" );
		foreach ( $terms as $term_id ) {
			wp_delete_term( (int) $term_id, 'stm_lms_course_taxonomy' );
		}

		// Clear custom LMS tables
		$lms_tables = array(
			'stm_lms_curriculum_sections',
			'stm_lms_curriculum_materials',
			'stm_lms_user_courses',
			'stm_lms_user_lessons',
			'stm_lms_user_quizzes',
			'stm_lms_user_answers',
		);
		foreach ( $lms_tables as $t ) {
			$full = $this->wp_db->prefix . $t;
			$this->wp_db->query( "TRUNCATE TABLE {$full}" );
		}

		// Clear mapping table
		$this->wp_db->query( "TRUNCATE TABLE {$table}" );

		return array( 'success' => true, 'message' => 'All migrated data has been reset.' );
	}
}
