<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\WooCommerce;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\CourseAbstractRepository;

class CourseRepository extends CourseAbstractRepository {

	public function get_revenue_query(): string {
		$wc_orders_table = OrdersTableDataStore::get_orders_table_name();

		return $this->db->prepare(
			"SELECT
                oi.object_id AS course_id,
                SUM(oi.quantity * oi.price) AS total_revenue
            FROM (
                SELECT DISTINCT oi.order_id, oi.object_id, oi.quantity, oi.price
                FROM {$this->db->prefix}stm_lms_order_items oi
                LEFT JOIN {$this->db->prefix}woocommerce_order_items wc_order_items ON wc_order_items.order_id = oi.order_id  
                LEFT JOIN {$this->db->prefix}woocommerce_order_itemmeta wc_order_itemmeta ON wc_order_itemmeta.order_item_id = wc_order_items.order_item_id  
                WHERE wc_order_itemmeta.meta_key = '_masterstudy_lms-course' AND wc_order_itemmeta.meta_value = 'yes'
            ) AS oi
            WHERE EXISTS (
                SELECT 1 
                FROM {$wc_orders_table} oi2
                WHERE oi2.id = oi.order_id
                AND oi2.status = 'wc-completed'
            )
            GROUP BY oi.object_id"
		);
	}
}

