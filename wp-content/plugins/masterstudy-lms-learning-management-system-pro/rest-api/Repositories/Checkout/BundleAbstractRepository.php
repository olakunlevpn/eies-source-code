<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout;

use Automattic\WooCommerce\Admin\API\Reports\Categories\DataStore;
use MasterStudy\Lms\Pro\RestApi\Repositories\DataTable\DataTableAbstractRepository;
use STM_LMS_Options;

class BundleAbstractRepository extends DataTableAbstractRepository {
	public function get_bundles_data( $columns = array(), $order = array() ): array {
		// If Bundle Groups Addon is not enabled
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return array();
		}

		$this->apply_sort( $order, $columns );
		$this->search_column = array( 'bundle_data.bundle_name' );

		$this->select    = array(
			'bundle_data.bundle_name AS name',
			'bundle_data.bundle_id',
			'bundle_data.date_created',
			'COALESCE(SUM(SUBSTRING_INDEX(SUBSTRING_INDEX( courses_data.value, ":{", 1), ":", -1)), 0) AS courses_inside',
			'COALESCE(SUM(orders.total_revenue), 0) AS revenue',
			'COALESCE(orders.counts, 0) AS orders',
		);
		$this->group_by  = array(
			'bundle_data.bundle_id',
			'bundle_data.bundle_name',
			'bundle_data.date_created',
		);
		$this->post_type = 'stm-course-bundles';

		// Select aggregate tables
		$this->get_query();

		$instructor_where = '';
		if ( $this->is_current_user_instructor() ) {
			$instructor_where = $this->db->prepare( ' AND p.post_author = %d', $this->current_instructor_id );
		}

		// Select bundles
		$sql          = 'SELECT ' . implode( ',', $this->select ) . "
			FROM (
			    SELECT
			        p.ID AS bundle_id,
			        p.post_title AS bundle_name,
			        p.post_author as bundle_author,
			        p.post_date AS date_created
			    FROM {$this->db->posts} p
			    WHERE p.post_type = '{$this->post_type}' AND p.post_status IN ('publish') {$instructor_where}
			    GROUP BY p.ID, p.post_title, p.post_date
			) AS bundle_data\n";
		$this->join[] = "LEFT JOIN (
		    SELECT post_id AS bundle_id, meta.meta_value AS value
		    FROM {$this->db->postmeta} meta
		    WHERE meta.meta_key = 'stm_lms_bundle_ids'
		    GROUP BY post_id,value
		) AS courses_data ON courses_data.bundle_id = bundle_data.bundle_id";
		$sql         .= implode( "\n", $this->join ) . "\n";
		$sql         .= $this->where_query();
		$sql         .= $this->group_query();

		// Add order, limit & offset
		$sql .= $this->pagination_query();

		return $this->db->get_results( $sql, ARRAY_A );
	}

	public function get_woocommerce_bundle_revenue( int $bundle_id ): array {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return array();
		}

		$this->post_type = 'stm-course-bundles';

		// Call get_query() to ensure necessary joins are applied
		$this->get_query();

		// Select revenue & orders for this bundle
		$sql = "SELECT 
                COALESCE(SUM(orders.total_revenue), 0) AS revenue, 
                COALESCE(SUM(orders.counts), 0) AS orders
            FROM (
                SELECT
                    p.ID AS bundle_id
                FROM {$this->db->posts} p
                WHERE p.ID = %d 
                AND p.post_type = '{$this->post_type}' 
                AND p.post_status = 'publish'
            ) AS bundle_data
            " . implode( "\n", $this->join ) . "\n";

		$prepared_sql = $this->db->prepare( $sql, $bundle_id );
		$results      = $this->db->get_results( $prepared_sql, ARRAY_A );

		return ! empty( $results ) ? $results[0] : array(
			'revenue' => 0,
			'orders'  => 0,
		);
	}

	public function get_bundle_revenue( int $bundle_id ): array {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return array();
		}

		$this->select = array(
			'COALESCE(SUM(orders.total_revenue), 0) AS revenue',
			'COALESCE(SUM(orders.counts), 0) AS orders',
		);

		$this->post_type = 'stm-course-bundles';

		// Filter only for this specific bundle
		$bundle_filter = $this->db->prepare( ' WHERE bundle_data.bundle_id = %d', $bundle_id );

		// Select aggregate tables
		$this->get_query();

		$sql = 'SELECT ' . implode( ',', $this->select ) . "
        FROM (
            SELECT
                p.ID AS bundle_id
            FROM {$this->db->posts} p
            WHERE p.post_type = '{$this->post_type}' AND p.post_status = 'publish'
        ) AS bundle_data
        LEFT JOIN (
            SELECT post_id AS bundle_id, meta.meta_value AS value
            FROM {$this->db->postmeta} meta
            WHERE meta.meta_key = 'stm_lms_bundle_ids'
            GROUP BY post_id, value
        ) AS courses_data ON courses_data.bundle_id = bundle_data.bundle_id
        LEFT JOIN (
            SELECT object_id AS course_id, SUM(quantity * price) AS total_revenue, COUNT(order_id) as counts
            FROM {$this->table_orders} oi
            LEFT JOIN {$this->db->postmeta} om ON oi.order_id = om.post_id AND om.meta_key = 'date'
            JOIN {$this->db->postmeta} m ON oi.order_id = m.post_id AND m.meta_key = 'status' AND m.meta_value = 'completed'
            WHERE om.meta_value BETWEEN '{$this->get_timestamp($this->date_from)}' AND '{$this->get_timestamp($this->date_to)}'
            GROUP BY object_id
        ) AS orders ON orders.course_id = courses_data.bundle_id
        {$bundle_filter}"; // Filtering applied after all joins

		// Execute query
		$results = $this->db->get_results( $sql, ARRAY_A );

		return ! empty( $results )
			? $results[0]
			: array(
				'revenue' => 0,
				'orders'  => 0,
			);
	}

	public function get_bundle_revenue_by_period( int $bundle_id ): array {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return array();
		}

		$this->post_type = 'stm-course-bundles';

		// Select aggregate tables
		$this->get_query();

		$sql = "SELECT 
                DATE_FORMAT(FROM_UNIXTIME(om.meta_value), '%m/%d') AS period,  -- ✅ Changed '%m/%y' to '%m/%d'
                oi.order_id,
                SUM(oi.quantity * oi.price) AS total_revenue
            FROM {$this->table_orders} oi
            LEFT JOIN {$this->db->postmeta} om ON oi.order_id = om.post_id AND om.meta_key = 'date'
            JOIN {$this->db->postmeta} m ON oi.order_id = m.post_id AND m.meta_key = 'status' AND m.meta_value = 'completed'
            WHERE om.meta_value BETWEEN '{$this->get_timestamp($this->date_from)}' AND '{$this->get_timestamp($this->date_to)}'
            AND oi.object_id = {$bundle_id}
            GROUP BY period, oi.order_id
            ORDER BY om.meta_value ASC";

		// Execute query with bundle ID
		$results = $this->db->get_results( $sql, ARRAY_A );

		// 🔹 Create a full period range from `date_from` to `date_to`
		$start_date  = new \DateTime( $this->date_from );
		$end_date    = new \DateTime( $this->date_to );
		$period_data = array();

		while ( $start_date <= $end_date ) {
			$formatted_date                 = $start_date->format( 'm/d' ); // e.g., '02/03'
			$period_data[ $formatted_date ] = 0; // Default revenue is 0
			$start_date->modify( '+1 day' );
		}

		// Populate revenue data into full period range
		foreach ( $results as $row ) {
			$formatted_date = $row['period']; // e.g., '02/03'
			if ( isset( $period_data[ $formatted_date ] ) ) {
				$period_data[ $formatted_date ] = (float) $row['total_revenue']; // Replace 0 with actual revenue
			}
		}

		// Return correctly formatted arrays
		return array(
			'period' => array_keys( $period_data ),
			'values' => array_values( $period_data ),
		);
	}

	public function get_woocommerce_bundle_revenue_by_period( int $bundle_id ): array {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ||
			! STM_LMS_Options::get_option( 'ecommerce_engine', 'native' ) === 'woocommerce' ||
			! class_exists( 'WooCommerce' ) ) {
			return array();
		}

		$this->post_type = 'stm-course-bundles';
		$start_timestamp = strtotime( $this->date_from );
		$end_timestamp   = strtotime( $this->date_to );
		$period_data     = array();

		while ( $start_timestamp <= $end_timestamp ) {
			$formatted_date                 = wp_date( 'm/d', $start_timestamp ); // e.g., '02/03'
			$period_data[ $formatted_date ] = 0; // Default revenue is 0
			$start_timestamp                = strtotime( '+1 day', $start_timestamp );
		}

		$table_product_lookup = DataStore::get_db_table_name();

		// Query to get revenue grouped by **day**
		$sql = "SELECT 
                DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(wc_orders.date_created_gmt)), '%m/%d') AS period,
                COALESCE(SUM(product_lookup.product_net_revenue), 0) AS total_revenue
            FROM {$this->db->prefix}wc_orders wc_orders
            JOIN {$table_product_lookup} product_lookup ON product_lookup.order_id = wc_orders.id
            JOIN {$this->db->prefix}woocommerce_order_itemmeta bundle_meta ON bundle_meta.meta_key = '_bundle_id'
            WHERE bundle_meta.order_item_id = product_lookup.order_item_id
            AND wc_orders.status = 'wc-completed'
            AND wc_orders.date_created_gmt BETWEEN '{$this->date_from}' AND '{$this->date_to}'
            AND bundle_meta.meta_value = {$bundle_id}
            GROUP BY period
            ORDER BY period ASC";

		$results = $this->db->get_results( $sql, ARRAY_A );

		// Populate existing revenue data into full period range
		foreach ( $results as $row ) {
			$formatted_date = $row['period']; // e.g., '02/03'
			if ( isset( $period_data[ $formatted_date ] ) ) {
				$period_data[ $formatted_date ] = (float) $row['total_revenue']; // Replace 0 with actual revenue
			}
		}

		// Return correctly formatted arrays
		return array(
			'period' => array_keys( $period_data ),
			'values' => array_values( $period_data ),
		);
	}
}
