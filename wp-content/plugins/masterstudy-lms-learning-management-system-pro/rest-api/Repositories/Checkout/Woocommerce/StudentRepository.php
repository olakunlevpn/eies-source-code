<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\WooCommerce;

use Automattic\WooCommerce\Admin\API\Reports\Categories\DataStore;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\StudentAbstractRepository;

class StudentRepository extends StudentAbstractRepository {

	public function build_query(): string {
		$this->select = array(
			'u.ID AS student_id',
			'u.display_name AS name',
			'revenue_data.total_orders',
			'revenue_data.order_ids',
			'revenue_data.revenue AS revenue',
			'COUNT(DISTINCT CASE WHEN pm_bundle.meta_value LIKE \'a:%\' THEN pm_bundle.post_id ELSE NULL END) AS bundle_count',
			'SUM(
                CASE 
                    WHEN pm_bundle.meta_value LIKE \'a:%\' THEN 
                        (LENGTH(pm_bundle.meta_value) - LENGTH(REPLACE(pm_bundle.meta_value, \'s:\', \'\'))) / LENGTH(\'s:\')
                    ELSE 0
                END
            ) AS bundles',
			'COUNT(DISTINCT CASE WHEN pm_bundle.meta_value IS NULL THEN meta.meta_value ELSE NULL END) AS courses',
			'COUNT(DISTINCT enterprise_meta.meta_value) AS purchased_groups',
		);

		$wc_orders_table      = OrdersTableDataStore::get_orders_table_name();
		$table_product_lookup = DataStore::get_db_table_name();

		$this->join = array(
			"LEFT JOIN (
                SELECT 
                    wc_orders.customer_id,
                    COUNT(DISTINCT wc_orders.ID) AS total_orders,
                    GROUP_CONCAT(DISTINCT wc_orders.ID ORDER BY wc_orders.ID ASC) AS order_ids,
                    COALESCE(SUM(product_lookup.product_qty * product_lookup.product_net_revenue), 0) AS revenue  -- Assuming total_amount is the column for order revenue
                FROM {$wc_orders_table} wc_orders
                LEFT JOIN {$table_product_lookup} product_lookup ON product_lookup.order_id = wc_orders.id
                LEFT JOIN {$this->db->prefix}woocommerce_order_itemmeta meta ON meta.order_item_id = product_lookup.order_item_id AND meta.meta_key = '_masterstudy_lms-course'
                WHERE wc_orders.status = 'wc-completed' AND meta.meta_value = 'yes'
                AND wc_orders.date_created_gmt BETWEEN '{$this->date_from}' AND '{$this->date_to}'  -- Filter by date range
                GROUP BY wc_orders.customer_id
            ) AS revenue_data ON u.ID = revenue_data.customer_id",
			"LEFT JOIN {$wc_orders_table} wco ON u.ID = wco.customer_id AND wco.status = 'wc-completed'",
			"LEFT JOIN {$table_product_lookup} product_lookup ON wco.id = product_lookup.order_id",
			"LEFT JOIN {$this->db->prefix}woocommerce_order_itemmeta meta ON meta.order_item_id = product_lookup.order_item_id AND meta.meta_key = '_masterstudy_lms-course' AND meta.meta_value = 'yes'",
			"LEFT JOIN {$this->db->postmeta} pm_bundle ON product_lookup.product_id = pm_bundle.post_id AND pm_bundle.meta_key = 'stm_lms_bundle_ids'",
			"LEFT JOIN {$this->db->prefix}woocommerce_order_itemmeta enterprise_meta ON enterprise_meta.order_item_id = product_lookup.order_item_id AND enterprise_meta.meta_key = '_enterprise_id'",
			"LEFT JOIN {$this->db->posts} p_e ON p_e.ID = pm_bundle.post_id AND p_e.post_type = 'stm-ent-groups'",
		);

		$search_query = '';
		if ( ! empty( $this->search_value ) ) {
			$search_query = "AND u.display_name LIKE '%{$this->search_value}%'";
		}

		$where = "
            WHERE um.meta_key = '{$this->db->prefix}capabilities'
            AND (
                um.meta_value LIKE '%customer%' OR
                um.meta_value LIKE '%subscriber%' OR
                um.meta_value LIKE '%administrator%' OR
                um.meta_value LIKE '%stm_lms_instructor%'
            )
            {$search_query}
            GROUP BY u.ID, u.display_name, revenue_data.total_orders, revenue_data.order_ids, revenue_data.revenue
            ORDER BY {$this->sort_by} {$this->sort_dir}
            LIMIT {$this->limit} OFFSET {$this->start}";

		$select = implode( ', ', $this->select );
		$join   = implode( "\n", $this->join );

		$sql = "{$this->db->users} u";
		if ( $this->is_current_user_instructor() ) {
			$sql = $this->db->prepare(
				"(SELECT
		        DISTINCT u.id AS ID,
		        u.display_name AS display_name,
		        u.user_login as user_login
		        FROM {$this->db->users} u
		
				INNER JOIN {$this->db->prefix}stm_lms_user_courses course ON course.user_id = u.ID
				INNER JOIN {$this->db->posts} p ON p.post_type = 'stm-courses' AND p.post_status IN ('publish') AND p.post_author = %d AND course.course_id = p.ID
				WHERE u.ID != %d
			) AS u",
				$this->current_instructor_id,
				$this->current_instructor_id
			);
		}

		return "SELECT $select
                FROM $sql
                INNER JOIN {$this->db->usermeta} um ON u.ID = um.user_id
                $join
                $where";
	}

	public function build_all(): array {
		return $this->db->get_results(
			$this->build_query(),
			ARRAY_A
		);
	}
}
