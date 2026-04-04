<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\Masterstudy;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\RestApi\Interfaces\OrderInterface;
use MasterStudy\Lms\Pro\RestApi\Repositories\Checkout\OrderAbstractRepository;

final class OrderRepository extends OrderAbstractRepository implements OrderInterface {
	public function get_orders( $args = array() ) {
		$default_args = array(
			'post_type'      => PostType::ORDER,
			'posts_per_page' => -1,
			'date_query'     => array(
				'after'  => $this->date_from,
				'before' => $this->date_to,
			),
			'meta_query'     => array(
				array(
					'key'     => 'status',
					'value'   => 'completed',
					'compare' => '=',
				),
			),
			'order'          => 'ASC',
		);

		$query = new \WP_Query(
			wp_parse_args( $args, $default_args )
		);

		return $query->posts;
	}

	public function get_instructor_orders( $instructor_course_ids, $student_id = null ) {
		if ( empty( $instructor_course_ids ) ) {
			return array();
		}

		$in_clause   = implode( ',', array_map( 'intval', $instructor_course_ids ) );
		$extra_query = '';

		if ( $this->is_current_user_instructor() && ! empty( $student_id ) ) {
			$extra_query = $this->db->prepare( 'AND p.post_author = %d', $student_id );
		}

		return $this->db->get_results(
			$this->db->prepare(
				"SELECT p.ID, p.post_date
				FROM {$this->db->prefix}stm_lms_order_items oi
				LEFT JOIN {$this->db->posts} p ON p.ID = oi.order_id
				LEFT JOIN {$this->db->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'status'
				WHERE oi.object_id IN ($in_clause) AND p.post_type = %s AND p.post_date BETWEEN %s AND %s AND pm.meta_value = %s $extra_query GROUP BY p.ID",
				PostType::ORDER,
				$this->date_from,
				$this->date_to,
				'completed'
			)
		);
	}

	public function get_detailed_instructor_orders( $instructor_course_ids, $search, $length, $start, $columns, $order ) {
		if ( empty( $instructor_course_ids ) ) {
			return array(
				'detailed_orders' => array(),
				'total_orders'    => 0,
			);
		}

		$in_clause = implode( ',', array_map( 'intval', $instructor_course_ids ) );
		$length    = intval( $length );
		$start     = max( 0, intval( $start ) );

		$search_condition = '';
		if ( ! empty( $search ) ) {
			$search           = '%' . $this->db->esc_like( $search ) . '%';
			$search_condition = $this->db->prepare(
				'AND (p.ID LIKE %s
				OR u.ID LIKE %s
				OR CONCAT(um_first_name.meta_value, " ", um_last_name.meta_value) LIKE %s
				OR u.user_email LIKE %s)',
				$search,
				$search,
				$search,
				$search
			);
		}

		$base_query = "
			FROM {$this->db->prefix}stm_lms_order_items oi
			LEFT JOIN {$this->db->posts} p ON p.ID = oi.order_id
			LEFT JOIN {$this->db->users} u ON u.ID = p.post_author
			LEFT JOIN {$this->db->prefix}postmeta order_meta_date ON order_meta_date.post_id = p.ID AND order_meta_date.meta_key = 'date'
			LEFT JOIN {$this->db->usermeta} um_first_name ON um_first_name.user_id = u.ID AND um_first_name.meta_key = 'first_name'
			LEFT JOIN {$this->db->usermeta} um_last_name ON um_last_name.user_id = u.ID AND um_last_name.meta_key = 'last_name'
			LEFT JOIN {$this->db->prefix}postmeta payment_meta ON payment_meta.post_id = p.ID AND payment_meta.meta_key = 'payment_code'
			LEFT JOIN {$this->db->prefix}postmeta order_meta_status ON order_meta_status.post_id = p.ID AND order_meta_status.meta_key = 'status'
			WHERE oi.object_id IN ($in_clause)
			AND p.post_type = %s
			AND p.post_date BETWEEN %s AND %s
			{$search_condition}
		";

		$order_column_index = intval( $order[0]['column'] ?? -1 );
		$order_direction    = strtolower( $order[0]['dir'] ?? 'asc' );
		$query_order        = '';

		if ( $order_column_index >= 0 && isset( $columns[ $order_column_index ]['orderable'] ) && 'true' === $columns[ $order_column_index ]['orderable'] ) {
			if ( ! in_array( $order_direction, array( 'asc', 'desc' ), true ) ) {
				$order_direction = 'asc';
			}

			switch ( $columns[ $order_column_index ]['data'] ) {
				case 'user_info':
					$query_order = " ORDER BY CONCAT_WS(' ', um_first_name.meta_value, um_last_name.meta_value) {$order_direction}";
					break;
				case 'date':
					$query_order = " ORDER BY FROM_UNIXTIME(order_meta_date.meta_value) {$order_direction}";
					break;
				case 'total_items':
					$query_order = " ORDER BY total_items {$order_direction}";
					break;
				case 'payment_code':
					$query_order = " ORDER BY payment_meta.meta_value {$order_direction}";
					break;
				case 'status_name':
					$query_order = " ORDER BY order_meta_status.meta_value {$order_direction}";
					break;
				case 'total_price':
					$query_order = " ORDER BY total_price {$order_direction}";
					break;
				default:
					$query_order = '';
					break;
			}
		}

		$query = $this->db->prepare(
			'SELECT
				p.ID as order_id,
				p.post_author as user_id,
				p.post_date as date,
				oi.object_id as item_id,
				oi.price as price,
				oi.quantity as quantity,
				p.post_title as order_key,
				u.user_email as user_email,
				COALESCE(order_meta_date.meta_value, NULL) as order_meta_date,
				SUM(oi.price * oi.quantity) as total_price,
				COUNT(DISTINCT oi.object_id) as total_items,
				COALESCE(payment_meta.meta_value, "") as payment_meta,
				COALESCE(order_meta_status.meta_value, "") as order_meta_status
			' . $base_query . '
			GROUP BY p.ID' . $query_order,
			PostType::ORDER,
			$this->date_from,
			$this->date_to
		);

		$query .= $this->db->prepare( ' LIMIT %d OFFSET %d', $length, $start );

		$results = $this->db->get_results( $query, ARRAY_A );

		$count_query = $this->db->prepare(
			'SELECT COUNT(DISTINCT p.ID) ' . $base_query,
			PostType::ORDER,
			$this->date_from,
			$this->date_to
		);

		$total_filtered_orders = $this->db->get_var( $count_query );
		$detailed_orders       = array();
		$taxes_display         = masterstudy_lms_taxes_display();

		foreach ( $results as $order ) {
			$order_id   = $order['order_id'];
			$order_meta = \STM_LMS_Helpers::parse_meta_field( $order_id );
			$user_data  = get_userdata( $order['user_id'] );
			$taxes      = isset( $order_meta['_order_taxes'] ) ? (float) $order_meta['_order_taxes'] : 0;
			$total      = isset( $order_meta['_order_total'] ) ? (float) $order_meta['_order_total'] : 0;
			$subtotal   = isset( $order_meta['_order_subtotal'] ) ? (float) $order_meta['_order_subtotal'] : $total;
			$price      = \STM_LMS_Helpers::display_price( $total );

			if ( ! isset( $detailed_orders[ $order_id ] ) ) {
				$first_name = $user_data ? $user_data->first_name : '';
				$last_name  = $user_data ? $user_data->last_name : '';
				$user_email = $user_data ? $user_data->user_email : '';
				$user_name  = $user_data ? $user_data->user_login : '';
				if ( empty( $first_name ) && empty( $last_name ) ) {
					$user_info = $user_name . '<br><a href="mailto:' . esc_attr( $user_email ) . '">' . $user_email . '</a>';
				} else {
					$user_info = $first_name . ' ' . $last_name . '<br><a href="mailto:' . esc_attr( $user_email ) . '">' . $user_email . '</a>';
				}

				if ( is_ms_lms_coupons_enabled() && ! empty( $order_meta['coupon_value'] ) && ! empty( $order_meta['coupon_type'] ) ) {
					$order_meta['coupon_value'] = 'amount' === $order_meta['coupon_type'] ? \STM_LMS_Helpers::display_price( (float) $order_meta['coupon_value'] ) : $order_meta['coupon_value'] . '%';
				}

				$order_data = array(
					'order_id'     => (int) $order['order_id'],
					'user_info'    => $user_info,
					'status_name'  => $order_meta['status'] ?? '',
					'total_price'  => $price,
					'total_items'  => (int) $order['total_items'],
					'payment_code' => 'wire_transfer' === $order_meta['payment_code']
						? __( 'wire transfer', 'masterstudy-lms-learning-management-system-pro' )
						: $order_meta['payment_code'],
					'coupon_value' => $order_meta['coupon_value'] ?? null,
					'coupon_type'  => $order_meta['coupon_type'] ?? null,
					'coupon_id'    => $order_meta['coupon_id'] ?? null,
					'date'         => date_i18n( 'd.m.Y, H:i', strtotime( $order['date'] ) ),
				);

				if ( $taxes_display['enabled'] || ! empty( $order_data['coupon_value'] ) ) {
					$order_data['subtotal'] = \STM_LMS_Helpers::display_price( $subtotal );
				}

				if ( $taxes_display['enabled'] ) {
					$order_data['taxes'] = \STM_LMS_Helpers::display_price( $taxes );
				}

				$detailed_orders[] = $order_data;
			}
		}

		return array(
			'detailed_orders' => $detailed_orders,
			'total_orders'    => $total_filtered_orders,
		);
	}

	public function count_instructor_orders( $instructor_course_ids ) {
		if ( empty( $instructor_course_ids ) ) {
			return 0;
		}

		$in_clause = implode( ',', array_map( 'intval', $instructor_course_ids ) );

		$results = $this->db->get_results(
			$this->db->prepare(
				"SELECT 
					p.ID as order_id
				FROM {$this->db->prefix}stm_lms_order_items oi
				LEFT JOIN {$this->db->posts} p ON p.ID = oi.order_id
				LEFT JOIN {$this->db->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'status'
				LEFT JOIN {$this->db->postmeta} bm ON bm.post_id = p.ID AND bm.meta_key = 'billing'
				WHERE oi.object_id IN ($in_clause) 
					AND p.post_type = %s 
					AND p.post_date BETWEEN %s AND %s
				GROUP BY p.ID",
				PostType::ORDER,
				$this->date_from,
				$this->date_to
			),
			ARRAY_A
		);

		return count( $results );
	}

	public function get_student_orders( $user_id ) {
		return $this->get_orders( array( 'author' => $user_id ) );
	}

	public function get_order_total( $order ): float {
		return floatval( get_post_meta( $order->ID, '_order_total', true ) );
	}

	public function get_order_customer_id( $order ) {
		return $order->post_author ?? null;
	}

	public function get_order_date( $order ) {
		return $order->post_date;
	}

	public function get_order_items( $order ) {
		$order_items    = get_post_meta( $order->ID, 'items', true );
		$filtered_items = array();

		foreach ( $order_items as $item ) {
			// Condition for Single Course Analytics
			if ( ! empty( $this->course_id ) ) {
				$item_id = (int) $item['item_id'];

				if ( $this->is_bundle_item( $item ) ) {
					$bundle_item_ids = $this->get_bundle_course_ids( $item_id );
					if ( ! in_array( $this->course_id, array_map( 'intval', $bundle_item_ids ), true ) ) {
						continue;
					}
				} elseif ( $item_id !== $this->course_id ) {
					continue;
				}
			}

			$filtered_items[] = $item;
		}

		return $filtered_items;
	}

	public function get_item_total( $item ): float {
		return floatval( $item['price'] );
	}

	public function is_bundle_item( $item, $source_id = null ): bool {
		return ! empty( $item['bundle'] );
	}

	public function is_group_item( $item ): bool {
		return ! empty( $item['enterprise'] );
	}

	public function get_item_group_id( $item ): int {
		return intval( $item['enterprise'] ?? 0 );
	}

	public function is_preorder_item( $item, $order_date ): bool {
		$coming_soon_date = get_post_meta( $item['item_id'], 'coming_soon_date', true );

		if ( ! empty( $coming_soon_date ) ) {
			return intval( $coming_soon_date ) > strtotime( $order_date );
		}

		return false;
	}

	public function get_item_course_id( $item ): int {
		return (int) $item['item_id'];
	}

	public function format_order_date( $order, $date_format ) {
		return gmdate( $date_format, strtotime( $order->post_date ) );
	}

	public function get_user_lastest_order( $user_id, $order_date ) {
		return $this->db->get_var( $this->db->prepare( "SELECT COUNT(1) FROM {$this->db->posts} WHERE post_type = %s AND post_author = %d AND post_date < %s", PostType::ORDER, $user_id, $order_date ) );
	}
}
