<?php
namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\Woocommerce;

use Automattic\WooCommerce\Admin\API\Reports\Categories\DataStore;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\BundleAbstractRepository;

class BundleRepository extends BundleAbstractRepository {
	public function get_query() {
		$wc_orders_table      = OrdersTableDataStore::get_orders_table_name();
		$table_product_lookup = DataStore::get_db_table_name();

		// Get all courses in order from bundle
		$this->join[]   = "LEFT JOIN (
		    SELECT product_lookup.product_id AS course_id, SUM(product_lookup.product_qty * product_lookup.product_net_revenue) AS total_revenue, COUNT(wc_orders.id) as counts
		    FROM {$wc_orders_table} wc_orders
		    LEFT JOIN {$table_product_lookup} product_lookup ON product_lookup.order_id = wc_orders.id
		    LEFT JOIN {$this->db->prefix}woocommerce_order_itemmeta meta ON meta.order_item_id = product_lookup.order_item_id AND meta.meta_key = '_bundle_id'
			WHERE wc_orders.status = 'wc-completed' AND wc_orders.date_created_gmt BETWEEN '{$this->date_from}' AND '{$this->date_to}'
		    GROUP BY meta.meta_value
		) AS orders ON orders.course_id = bundle_data.bundle_id";
		$this->group_by = array_merge(
			$this->group_by,
			array(
				'orders.counts',
			)
		);
	}
}
