<?php

add_filter( 'stm_lms_user_orders', 'stm_lms_user_orders_pro', 10, 4 );

function stm_lms_user_orders_pro( $response, $user_id, $pp, $offset ) {
	$posts     = array();
	$args      = array(
		'post_type'      => wc_get_order_types(),
		'posts_per_page' => $pp,
		'post_status'    => array_keys( wc_get_order_statuses() ),
		'offset'         => $offset,
		'customer_id'    => $user_id,
		'return'         => 'ids',
	);
	$order_ids = wc_get_orders( $args );
	$total     = count( $order_ids );

	if ( ! empty( $order_ids ) ) {
		foreach ( $order_ids as $order_id ) {
			$posts[] = STM_LMS_Order::get_order_info( $order_id );
		}
		wp_reset_postdata();
	}

	return array(
		'total' => $total,
		'posts' => $posts,
		'pages' => wc_get_customer_order_count( $user_id ),
	);
}

add_filter( 'stm_lms_order_details', 'stm_lms_order_details_pro', 10, 2 );

function stm_lms_order_details_pro( $order, $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order_id || ! $order ) {
		return array();
	}

	$items = array();

	foreach ( $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $order_item ) {
		if ( is_a( $order_item, 'WC_Order_Item_Product' ) ) {
			$downloads      = $order_item->get_item_downloads();
			$downloads_data = array();

			if ( ! empty( $downloads ) ) {
				foreach ( $downloads as $download ) {
					$downloads_data[] = array(
						'name'                => $download['name'],
						'url'                 => $download['download_url'],
						'downloads_remaining' => $download['downloads_remaining'],
						'access_expires'      => $download['access_expires'],
					);
				}
			}

			$line_total_net    = (float) $order_item->get_total();
			$line_total_tax    = (float) $order_item->get_total_tax();
			$line_subtotal_net = (float) $order_item->get_subtotal();
			$line_subtotal_tax = (float) $order_item->get_subtotal_tax();
			$line_total_gross  = $line_total_net + $line_total_tax;

			$item = array(
				'item_id'   => $order_item->get_product_id(),
				'title'     => $order_item->get_name(),
				'price'     => $line_total_gross,
				'subtotal'  => $line_subtotal_net,
				'tax'       => $line_total_tax,
				'downloads' => $downloads_data,
				'quantity'  => $order_item->get_quantity(),
			);

			if ( $order_item->get_meta( '_enterprise_id' ) ) {
				$item['enterprise_id'] = absint( $order_item->get_meta( '_enterprise_id' ) );
			}
			if ( $order_item->get_meta( '_bundle_id' ) ) {
				$item['bundle_id'] = absint( $order_item->get_meta( '_bundle_id' ) );
			}

			$items[] = $item;
		}
	}

	$billing_address = array(
		'first_name'  => $order->get_billing_first_name(),
		'last_name'   => $order->get_billing_last_name(),
		'company'     => $order->get_billing_company(),
		'address_1'   => $order->get_billing_address_1(),
		'address_2'   => $order->get_billing_address_2(),
		'city'        => $order->get_billing_city(),
		'postcode'    => $order->get_billing_postcode(),
		'country'     => $order->get_billing_country(),
		'state'       => $order->get_billing_state(),
		'email'       => $order->get_billing_email(),
		'phone'       => $order->get_billing_phone(),
		'transaction' => $order->get_transaction_id(),
	);

	return array(
		'user_id'      => $order->get_user_id(),
		'status'       => $order->get_status(),
		'status_name'  => wc_get_order_status_name( $order->get_status() ),
		'items'        => $items,
		'date'         => strtotime( $order->get_date_created() ),
		'order_key'    => "#{$order_id}",
		'payment_code' => $order->get_payment_method_title(),
		'billing'      => $billing_address,
		'totals'       => array(
			'order_subtotal_net' => $order->get_subtotal(),
			'order_total_tax'    => $order->get_total_tax(),
			'grand_total'        => $order->get_total(),
		),
	);
}
