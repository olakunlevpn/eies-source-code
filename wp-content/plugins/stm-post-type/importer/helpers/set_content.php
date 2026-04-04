<?php
function stm_set_content_options( $layout, $builder ) {
	$locations = get_theme_mod( 'nav_menu_locations' );
	$menus     = wp_get_nav_menus();

	if ( ! empty( $menus ) ) {
		foreach ( $menus as $menu ) {
			$menu_names = array(
				'Primary menu',
				'Main Menu',
			);

			if ( is_object( $menu ) && in_array( $menu->name, $menu_names ) ) {
				$locations['primary'] = $menu->term_id;
			}
			if ( is_object( $menu ) && 'Footer menu' == $menu->name ) {
				$locations['secondary'] = $menu->term_id;
			}
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'show_on_front', 'page' );

	$front_pages = array(
		'Front Page',
		'Home',
	);

	foreach ( $front_pages as $front_page_name ) {
		$front_page = get_posts(
			array(
				'title'          => $front_page_name,
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $front_page ) ) {
			update_option( 'page_on_front', $front_page[0] );
		}
	}

	$blog_page = get_posts(
		array(
			'title'          => 'Blog',
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! empty( $blog_page ) ) {
		update_option( 'page_for_posts', $blog_page[0] );
	}

	$exclude_layouts = stm_import_products_exclude_layouts();

	if ( ! in_array( $layout, $exclude_layouts ) ) {

		$shop_page = get_posts(
			array(
				'title'          => 'Shop',
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $shop_page ) ) {
			update_option( 'woocommerce_shop_page_id', $shop_page[0] );
		} else {
			$page_id = stm_create_page( 'Shop', '' );
			if ( ! empty( $page_id ) ) {
				update_option( 'woocommerce_shop_page_id', $page_id );
			}
		}

		$cart_page = get_posts(
			array(
				'title'          => 'Cart',
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $cart_page ) ) {
			update_option( 'woocommerce_cart_page_id', $cart_page[0] );
		} else {
			$page_id = stm_create_page( 'Cart', '[woocommerce_cart]' );
			if ( ! empty( $page_id ) ) {
				update_option( 'woocommerce_cart_page_id', $page_id );
			}
		}

		$checkout_page = get_posts(
			array(
				'title'          => 'Checkout',
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $checkout_page ) ) {
			update_option( 'woocommerce_checkout_page_id', $checkout_page[0] );
		} else {
			$page_id = stm_create_page( 'Checkout', '[woocommerce_checkout]' );
			if ( ! empty( $page_id ) ) {
				update_option( 'woocommerce_checkout_page_id', $page_id );
			}
		}

		$account_page = get_posts(
			array(
				'title'          => 'My Account',
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $account_page ) ) {
			update_option( 'woocommerce_myaccount_page_id', $account_page[0] );
		} else {
			$page_id = stm_create_page( 'My Account', '[woocommerce_my_account]' );
			if ( ! empty( $page_id ) ) {
				update_option( 'woocommerce_myaccount_page_id', $page_id );
			}
		}

		if ( isset( $locations['primary'] ) && ! empty( $locations['primary'] ) ) {
			$menu_id       = intval( $locations['primary'] );
			$menu          = wp_get_nav_menu_items( $menu_id, array( 'post_type' => 'nav_menu_item' ) );
			$has_menu_item = false;
			if ( $menu ) {
				foreach ( $menu as $key => $item ) {
					if ( 'Shop' == $item->title ) {
						$has_menu_item = true;
					}
				}
				if ( ! $has_menu_item ) {
					$updated_item = wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'  => __( 'Shop' ),
							'menu-item-url'    => home_url( '/shop/' ),
							'menu-item-status' => 'publish',
						)
					);
				}
			}
		}
	}

	$single    = array(
		'width'  => '840',
		'height' => '400',
		'crop'   => 1,
	);
	$thumbnail = array(
		'width'  => '175',
		'height' => '100',
		'crop'   => 1,
	);
	update_option( 'shop_single_image_size', $single );
	update_option( 'shop_thumbnail_image_size', $thumbnail );

	$fxml = get_temp_dir() . $layout . '.xml';
	$fzip = get_temp_dir() . $layout . '.zip';
	if ( file_exists( $fxml ) ) {
		@unlink( $fxml );
	}
	if ( file_exists( $fzip ) ) {
		@unlink( $fzip );
	}

	if ( 'elementor' === $builder ) {

		$from = 'http://lmsdemomentor.loc';
		$to   = get_site_url();

		global $wpdb;
        // @codingStandardsIgnoreStart cannot use `$wpdb->prepare` because it remove's the backslashes
        $wpdb->query(
            "UPDATE {$wpdb->postmeta} " .
            "SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', $from ) . "', '" . str_replace( '/', '\\\/', $to ) . "') " .
            "WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;" ); // meta_value LIKE '[%' are json formatted
        // @codingStandardsIgnoreEnd
		Elementor\Plugin::$instance->files_manager->clear_cache();

	}
}


function stm_create_page( $title, $content = '' ) {
	$post_details = array(
		'post_title'   => $title,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_type'    => 'page',
	);
	return wp_insert_post( $post_details );
}
