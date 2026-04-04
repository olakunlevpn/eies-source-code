<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\Woocommerce;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use MasterStudy\Lms\Pro\RestApi\Interfaces\OrderInterface;
use MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\OrderAbstractRepository;
use Automattic\WooCommerce\Admin\API\Reports\Categories\DataStore;
use Automattic\WooCommerce\Admin\Overrides\OrderRefund;
use MasterStudy\Lms\Plugin\PostType;

final class OrderRepository extends OrderAbstractRepository implements OrderInterface {
	public function get_orders( $args = array() ) {
		$default_args = array(
			'limit'        => -1,
			'type'         => 'shop_order',
			'status'       => 'completed',
			'date_created' => $this->date_from . '...' . $this->date_to,
			'order'        => 'ASC',
		);

		$query = new \WC_Order_Query( wp_parse_args( $args, $default_args ) );

		return $query->get_orders();
	}

	public function get_instructor_orders( $instructor_course_ids, $user_id = null ) {
		$order_ids = $this->get_order_ids_for_courses( $instructor_course_ids );

		if ( empty( $order_ids ) ) {
			return array();
		}

		// TODO - Replace get_orders() with get_orders_by_ids()
		$orders    = $this->get_orders();
		$order_ids = array_map( 'intval', $order_ids );

		return array_filter(
			$orders,
			function( $order ) use ( $order_ids, $user_id ) {
				$checked = in_array( $order->get_id(), $order_ids, true );

				if ( $this->is_current_user_instructor() && ! empty( $user_id ) ) {
					$checked = $checked && $order->get_customer_id() === $user_id;
				}

				return $checked;
			}
		);
	}

	public function get_detailed_instructor_orders( $instructor_course_ids, $search, $length, $start, $columns, $order ) {
		$order_ids = array_values( array_unique( $this->get_order_ids_for_courses( $instructor_course_ids ) ) );

		if ( empty( $order_ids ) ) {
			return array(
				'detailed_orders' => array(),
				'total_orders'    => 0,
			);
		}

		$wc_orders_table  = OrdersTableDataStore::get_orders_table_name();
		$order_ids_string = implode( ',', array_map( 'intval', $order_ids ) );

		$sql = $this->db->prepare(
			"SELECT wc.id AS order_id, wc.status AS post_status,
			wc.billing_email AS email, 
			pm1.meta_value AS first_name, 
			pm2.meta_value AS last_name,
			wc.date_created_gmt
			FROM {$wc_orders_table} wc
			LEFT JOIN {$this->db->postmeta} pm1 ON wc.id = pm1.post_id AND pm1.meta_key = '_billing_first_name'
			LEFT JOIN {$this->db->postmeta} pm2 ON wc.id = pm2.post_id AND pm2.meta_key = '_billing_last_name'
			WHERE wc.id IN ($order_ids_string)
			AND wc.date_created_gmt >= %s
			AND wc.date_created_gmt <= %s",
			$this->date_from,
			$this->date_to
		);

		$sql .= $this->get_search_filter_sql( $search );

		$order_column_index = intval( $order[0]['column'] ?? -1 );
		$order_direction    = strtolower( $order[0]['dir'] ?? 'asc' );

		if ( $order_column_index >= 0 && isset( $columns[ $order_column_index ]['orderable'] ) && 'true' === $columns[ $order_column_index ]['orderable'] ) {
			if ( ! in_array( $order_direction, array( 'asc', 'desc' ), true ) ) {
				$order_direction = 'asc';
			}

			if ( 'date' === $columns[ $order_column_index ]['data'] ) {
				$sql .= " ORDER BY wc.date_created_gmt {$order_direction}";
			} elseif ( 'user_info' === $columns[ $order_column_index ]['data'] ) {
				$sql .= " ORDER BY wc.billing_email {$order_direction}";
			} elseif ( 'total_items' === $columns[ $order_column_index ]['data'] ) {
				$sql .= " ORDER BY (SELECT COUNT(*) FROM {$this->db->prefix}woocommerce_order_items WHERE order_id = wc.id) {$order_direction}";
			} elseif ( 'payment_code' === $columns[ $order_column_index ]['data'] ) {
				$sql .= " ORDER BY wc.payment_method {$order_direction}";
			} elseif ( 'status_name' === $columns[ $order_column_index ]['data'] ) {
				$sql .= " ORDER BY wc.status {$order_direction}";
			} elseif ( 'total_price' === $columns[ $order_column_index ]['data'] ) {
				$sql .= " ORDER BY wc.total_amount {$order_direction}";
			}
		}

		$sql .= $this->db->prepare( ' LIMIT %d, %d', $start, $length );

		$orders          = $this->db->get_results( $sql );
		$wc_orders_table = OrdersTableDataStore::get_orders_table_name();

		if ( empty( $orders ) ) {
			return array(
				'detailed_orders' => array(),
				'total_orders'    => 0,
			);
		}

		$count_sql = $this->db->prepare(
			"SELECT COUNT(wc.id)
			FROM {$wc_orders_table} wc
			LEFT JOIN {$this->db->postmeta} pm1 ON wc.id = pm1.post_id AND pm1.meta_key = '_billing_first_name'
			LEFT JOIN {$this->db->postmeta} pm2 ON wc.id = pm2.post_id AND pm2.meta_key = '_billing_last_name'
			WHERE wc.id IN ($order_ids_string)
			AND wc.date_created_gmt >= %s
			AND wc.date_created_gmt <= %s",
			$this->date_from,
			$this->date_to
		);

		$count_sql .= $this->get_search_filter_sql( $search );

		$total_orders    = $this->db->get_var( $count_sql );
		$detailed_orders = array();

		foreach ( $orders as $order ) {
			$order                = wc_get_order( $order->order_id );
			$table_product_lookup = DataStore::get_db_table_name();

			if ( $order instanceof OrderRefund ) {
				continue;
			}

			$courses = $this->db->get_results(
				$this->db->prepare(
					"SELECT 
                        product_lookup.order_item_id, 
                        p.ID AS course_id,
                        p.post_author,
                        product_lookup.product_net_revenue as price
                        FROM {$this->db->posts} p
                        INNER JOIN {$table_product_lookup} product_lookup ON product_lookup.product_id = p.ID AND product_lookup.order_id = %d
                        WHERE p.post_type IN ( %s, %s ) AND p.post_author = %d
                    ",
					$order->get_id(),
					PostType::COURSE,
					'stm-course-bundles',
					get_current_user_id()
				)
			);

			$courses_data = array();
			foreach ( $courses as $course ) {
				$terms = stm_lms_get_terms_array( $course->course_id, 'stm_lms_course_taxonomy', 'name' );

				$courses_data[] = array(
					'course_id'    => $course->course_id,
					'course_title' => get_the_title( $course->course_id ),
					'course_link'  => get_the_permalink( $course->course_id ),
					'course_terms' => ! empty( $terms ) ? $terms : wp_list_pluck( get_the_terms( $course->course_id, 'product_cat' ), 'name' ),
					'course_image' => get_the_post_thumbnail( $course->course_id, 'full' ),
					'price'        => $course->price,
				);
			}

			$total_price           = array_sum(
				array_map(
					function ( $course ) {
						return (float) $course['price'];
					},
					$courses_data
				)
			);
			$formatted_total_price = 0 === $total_price
				? __( 'Free', 'masterstudy-lms-learning-management-system-pro' )
				: \STM_LMS_Helpers::display_price( $order->get_total() );

			$user_info = esc_attr( $order->get_billing_first_name() ) . ' ' . esc_attr( $order->get_billing_last_name() ) .
				'<br><a href="mailto:' . esc_attr( $order->get_billing_email() ) . '">' . esc_attr( $order->get_billing_email() ) . '</a>';

			$detailed_orders[] = array(
				'order_id'     => $order->get_id(),
				'user_info'    => $user_info,
				'status_name'  => sprintf(
					'<span class="order-status %s">%s</span>',
					$order->get_status() ?? '',
					wc_get_order_status_name( $order->get_status() ) ?? ''
				),
				'total_price'  => $formatted_total_price,
				'total_items'  => count( $courses_data ),
				'payment_code' => $order->get_payment_method_title(),
				'courses'      => $courses_data,
				'date'         => date_i18n( 'd.m.Y, H:i', strtotime( $order->get_date_created() ) ),
			);
		}

		return array(
			'detailed_orders' => $detailed_orders,
			'total_orders'    => $total_orders,
		);
	}

	private function get_search_filter_sql( $search ) {
		if ( is_numeric( $search ) ) {
			return ' AND o.ID = ' . intval( $search );
		} elseif ( is_string( $search ) && '' !== $search ) {
			$sql = ' AND (';

			$search_parts = explode( ' ', trim( $search ), 2 );

			if ( 2 === count( $search_parts ) ) {
				$sql .= $this->db->prepare(
					" EXISTS (SELECT 1 FROM {$this->db->prefix}wc_order_addresses wa
							WHERE wa.order_id = o.ID AND wa.first_name LIKE %s AND wa.last_name LIKE %s)",
					'%' . $this->db->esc_like( $search_parts[0] ) . '%',
					'%' . $this->db->esc_like( $search_parts[1] ) . '%'
				);
			} else {
				$search_like = '%' . $this->db->esc_like( $search ) . '%';

				$wc_orders_table = OrdersTableDataStore::get_orders_table_name();

				$sql .= $this->db->prepare(
					" EXISTS (SELECT 1 FROM {$wc_orders_table} wo
							WHERE wo.id = o.ID AND wo.billing_email LIKE %s)",
					$search_like
				);

				$sql .= $this->db->prepare(
					" OR EXISTS (SELECT 1 FROM {$this->db->prefix}wc_order_addresses wa
								WHERE wa.order_id = o.ID AND wa.first_name LIKE %s)",
					$search_like
				);

				$sql .= $this->db->prepare(
					" OR EXISTS (SELECT 1 FROM {$this->db->prefix}wc_order_addresses wa
								WHERE wa.order_id = o.ID AND wa.last_name LIKE %s)",
					$search_like
				);
			}

			$sql .= ' )';
			return $sql;
		}

		return '';
	}


	private function get_order_ids_for_courses( $instructor_course_ids ) {
		if ( empty( $instructor_course_ids ) ) {
			return array();
		}

		$courses_in_clause    = implode( ',', array_map( 'intval', $instructor_course_ids ) );
		$table_product_lookup = DataStore::get_db_table_name();

		return $this->db->get_col(
			$this->db->prepare( "SELECT product_lookup.order_id FROM {$table_product_lookup} product_lookup WHERE product_lookup.product_id IN ($courses_in_clause)" )
		);
	}

	public function get_student_orders( $user_id ) {
		// TODO - Replace get_orders() with get_orders_by_author()
		$orders = $this->get_orders();

		return array_filter(
			$orders,
			function( $order ) use ( $user_id ) {
				return $order->get_customer_id() === $user_id;
			}
		);
	}

	public function get_order_total( $order ): float {
		return floatval( $order->get_total() );
	}

	public function get_order_customer_id( $order ) {
		return $order->get_customer_id();
	}

	public function get_order_date( $order ) {
		return $order->get_date_created()->date( 'Y-m-d H:i:s' );
	}

	public function get_order_items( $order ) {
		$filtered_items = array();

		foreach ( $order->get_items() as $item ) {
			$product    = wc_get_product( $item->get_product_id() );
			$post_types = apply_filters( 'masterstudy_woo_post_types', array() );

			if ( $product && in_array( get_post_type( $item->get_product_id() ), $post_types, true ) ) {
				// Condition for Single Course Analytics
				if ( ! empty( $this->course_id ) ) {
					$product_id = $item->get_product_id();

					// TODO - Remove after fixing WooCommerce Group Purchases issue
					if ( $this->is_group_item( $item ) ) {
						$product_id = $this->get_item_group_id( $item );
					}

					if ( $this->is_bundle_item( $item, $product_id ) ) {
						$bundle_item_ids = $this->get_bundle_course_ids( $product_id );
						if ( ! in_array( $this->course_id, array_map( 'intval', $bundle_item_ids ), true ) ) {
							continue;
						}
					} elseif ( $product_id !== $this->course_id ) {
						continue;
					}
				}

				$filtered_items[] = $item;
			}
		}

		return $filtered_items;
	}

	public function get_item_total( $item ): float {
		return floatval( $item->get_total() );
	}

	public function is_bundle_item( $item, $source_id = null ): bool {
		if ( ! $source_id ) {
			$source_id = $this->get_item_course_id( $item );
		}

		return ! empty( $source_id ) && 'stm-course-bundles' === get_post_type( $source_id );
	}

	public function is_group_item( $item ): bool {
		return ! empty( $this->get_item_group_id( $item ) );
	}

	public function get_item_group_id( $item ): int {
		return (int) $item->get_meta( '_enterprise_id' );
	}

	public function is_preorder_item( $item, $order_date ): bool {
		$lms_course_id = $this->get_item_course_id( $item );

		// TODO - Remove after fixing WooCommerce Group Purchases issue
		if ( empty( $lms_course_id ) ) {
			$lms_course_id = $this->get_item_group_id( $item );
		}

		$coming_soon_date = get_post_meta( $lms_course_id, 'coming_soon_date', true );

		if ( ! empty( $coming_soon_date ) ) {
			return intval( $coming_soon_date ) > strtotime( $order_date );
		}

		return false;
	}

	public function get_item_course_id( $item ): int {
		return (int) $item->get_product_id();
	}

	public function format_order_date( $order, $date_format ) {
		return $order->get_date_created()->format( $date_format );
	}

	public function get_user_lastest_order( $user_id, $order_date ) {
		return wc_get_orders(
			array(
				'customer_id'  => $user_id,
				'limit'        => 1,
				'status'       => 'completed',
				'date_created' => '<' . $order_date,
			)
		);
	}
}
