<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories;

use MasterStudy\Lms\Pro\RestApi\Repositories\DataTable\DataTableAbstractRepository;

class BundlesRepository extends DataTableAbstractRepository {
	public function get_bundle_post_data( $post_id, $columns = array(), $order = array() ): array {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return array();
		}
		// Define the database tables
		$posts_table    = $this->db->prefix . 'posts';
		$postmeta_table = $this->db->prefix . 'postmeta';

		// Apply sorting (if needed)
		$this->apply_sort( $order, $columns, 'post_date', 'desc' );

		// Retrieve the bundle meta (serialized list of post IDs)
		$bundle_meta = get_post_meta( $post_id, 'stm_lms_bundle_ids', true );

		// Unserialize the bundle meta
		$bundle_ids = maybe_unserialize( $bundle_meta );

		// Check if we have valid post IDs
		if ( ! is_array( $bundle_ids ) || empty( $bundle_ids ) ) {
			return array();
		}

		// Build the placeholders for the IN clause
		$placeholders = implode( ',', array_fill( 0, count( $bundle_ids ), '%d' ) );

		// Map sort columns to database columns
		$sort_map = array(
			'course_name'  => 'p.post_title',
			'enrollments'  => 'current_students',
			'date_created' => 'p.post_date',
		);

		if ( isset( $sort_map[ $this->sort_by ] ) ) {
			$this->sort_by = $sort_map[ $this->sort_by ];
		}

		// Execute the query to fetch post data
		return $this->db->get_results(
			$this->db->prepare(
				"SELECT p.ID, p.post_title, p.post_name,
                    DATE_FORMAT(p.post_date, '%%d.%%m.%%Y %%H:%%i') AS formatted_date, 
                    pm.meta_value AS current_students
             FROM {$posts_table} p
             LEFT JOIN {$postmeta_table} pm ON p.ID = pm.post_id AND pm.meta_key = 'current_students'
             WHERE p.ID IN ($placeholders)
             ORDER BY {$this->sort_by} {$this->sort_dir}
             LIMIT {$this->limit} OFFSET {$this->start}",
				...$bundle_ids
			)
		);
	}

	public function get_bundles_by_course_id( $course_id, $columns = array(), $order = array(), $search_value = null ): array {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return array();
		}

		// Define the database tables
		$posts_table    = $this->db->prefix . 'posts';
		$postmeta_table = $this->db->prefix . 'postmeta';

		// Apply sorting (if needed)
		$this->apply_sort( $order, $columns, 'post_date', 'desc' );

		// Define column mappings for sorting
		$sort_map = array(
			'bundle_name'  => 'p.post_title',
			'orders'       => 'total_orders',
			'date_created' => 'p.post_date',
		);

		if ( isset( $sort_map[ $this->sort_by ] ) ) {
			$this->sort_by = $sort_map[ $this->sort_by ];
		}

		// Start building the SQL query
		$sql = "SELECT 
                p.ID AS bundle_id, 
                p.post_title AS bundle_name, 
                p.post_name AS bundle_slug, 
                DATE_FORMAT(p.post_date, '%%d.%%m.%%Y %%H:%%i') AS formatted_date,
                COALESCE(SUM(orders.counts), 0) AS total_orders
            FROM {$posts_table} p
            LEFT JOIN {$postmeta_table} pm ON p.ID = pm.post_id AND pm.meta_key = 'stm_lms_bundle_ids'
            LEFT JOIN (
                SELECT object_id AS course_id, COUNT(DISTINCT oi.order_id) AS counts
                FROM {$this->table_orders} oi
                LEFT JOIN {$this->db->postmeta} om ON oi.order_id = om.post_id AND om.meta_key = 'date'
                JOIN {$this->db->postmeta} m ON oi.order_id = m.post_id AND m.meta_key = 'status' AND m.meta_value = 'completed'
                WHERE om.meta_value BETWEEN '{$this->get_timestamp($this->date_from)}' AND '{$this->get_timestamp($this->date_to)}'
                GROUP BY object_id
            ) AS orders ON orders.course_id = p.ID
            WHERE p.post_type = 'stm-course-bundles'
            AND pm.meta_value LIKE %s";

		// Add search filtering if search_value is provided
		if ( ! empty( $search_value ) ) {
			$sql .= ' AND (p.post_title LIKE %s)';
		}

		// Add sorting and pagination
		$sql .= " GROUP BY p.ID, p.post_title, p.post_date
              ORDER BY {$this->sort_by} {$this->sort_dir}
              LIMIT {$this->limit} OFFSET {$this->start}";

		// Prepare parameters for the SQL query
		$params = array( '%' . $course_id . '%' );

		if ( ! empty( $search_value ) ) {
			$params[] = '%' . $this->db->esc_like( $search_value ) . '%';
		}

		// Execute query
		$prepared_sql = $this->db->prepare( $sql, ...$params );
		$results      = $this->db->get_results( $prepared_sql, ARRAY_A );

		return $results;
	}

}
