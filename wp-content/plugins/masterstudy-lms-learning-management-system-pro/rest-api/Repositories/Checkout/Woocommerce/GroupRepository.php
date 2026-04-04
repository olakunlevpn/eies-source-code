<?php
namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\Woocommerce;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\GroupAbstractRepository;

class GroupRepository extends GroupAbstractRepository {
	public function get_query() {
		$wc_orders_table = OrdersTableDataStore::get_orders_table_name();

		$this->join[] = "LEFT JOIN (
			SELECT orders.id AS post_id, meta.meta_value AS value
		    FROM {$this->db->prefix}woocommerce_order_itemmeta meta
		    LEFT JOIN {$this->db->prefix}woocommerce_order_items items ON meta.order_item_id = items.order_item_id
		    LEFT JOIN {$wc_orders_table} orders ON items.order_id = orders.id AND orders.status = 'wc-completed'
			WHERE meta.meta_key = '_enterprise_id' AND orders.date_created_gmt BETWEEN '{$this->date_from}' AND '{$this->date_to}'
		    GROUP BY orders.id,value
		) AS lms_orders ON lms_orders.value = group_data.group_id";
	}
}
