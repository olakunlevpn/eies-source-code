<?php
use Automattic\Jetpack\Constants;
use MasterStudy\Lms\Plugin\PostType;

class STM_LMS_Woocommerce {

	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( $this, 'stm_lms_woocommerce_order_created' ) );
		add_action( 'woocommerce_order_status_pending', array( $this, 'stm_lms_woocommerce_order_cancelled' ) );
		add_action( 'woocommerce_order_status_failed', array( $this, 'stm_lms_woocommerce_order_cancelled' ) );
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'stm_lms_woocommerce_order_cancelled' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'stm_lms_woocommerce_order_cancelled' ) );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'stm_lms_woocommerce_order_cancelled' ) );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'stm_lms_woocommerce_order_cancelled' ) );

		remove_action( 'the_post', 'wc_setup_product_data' );
		add_action( 'the_post', array( $this, 'setup_product_data' ) );

		add_action(
			'woocommerce_cart_is_empty',
			function () {
				STM_LMS_Templates::show_lms_template( 'global/all_courses_link' );
			}
		);

		add_action( 'template_redirect', array( $this, 'masterstudy_thankyou_page' ), 100 );

		add_filter( 'masterstudy_woo_post_types', array( $this, 'add_post_types' ) );

		add_action( 'woocommerce_product_query', array( $this, 'toggle_visibility_product' ) );

		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'set_add_to_cart_text' ), 10, 2 );

		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'set_add_to_cart_url' ), 10, 2 );

		add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'set_add_to_cart_args' ), 10, 2 );

		add_filter(
			'woocommerce_checkout_create_order_line_item_object',
			array(
				$this,
				'create_order_line_item_object',
			),
			20,
			3
		);

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'create_order_line_item' ), 20, 4 );

		add_action(
			'woocommerce_rest_insert_system_status_tool',
			array(
				$this,
				'rest_insert_system_status_tool',
			),
			99
		);
		add_action( 'woocommerce_system_status_tool_executed', array( $this, 'rest_insert_system_status_tool' ), 99 );
		add_action( 'lms_insert_course_into_product_lookup', array( $this, 'insert_course_into_product_lookup' ) );
		add_action( 'woocommerce_price_filter_post_type', array( $this, 'set_price_filter_post_type' ) );
		add_action( 'masterstudy_lms_course_price_updated', array( $this, 'update_course_price' ) );

		add_filter( 'woocommerce_product_pre_search_products', array( $this, 'product_pre_search_products' ), 99, 6 );

		add_filter( 'woocommerce_ajax_order_item', array( $this, 'ajax_order_item' ), 99, 4 );

		add_action( 'pre_get_posts', array( $this, 'enable_courses_get_posts' ) );
	}

	public function enable_courses_get_posts( WP_Query $query ) {
		if ( $query->get( 'is_woosb' ) ) {
			if ( is_array( $query->get( 'post_type' ) ) ) {
				$post_types = array_merge( array( \MasterStudy\Lms\Plugin\PostType::COURSE ), $query->get( 'post_type' ) );
			} else {
				$post_types = array( \MasterStudy\Lms\Plugin\PostType::COURSE, $query->get( 'post_type' ) );
			}

			$query->set( 'post_type', $post_types );
		}
	}

	public function ajax_order_item( $order_item, $item_id, $order, $product ) {
		if ( in_array( get_post_type( $product->get_id() ), array( MasterStudy\Lms\Plugin\PostType::COURSE, 'stm-course-bundles' ), true ) ) {
			$order_item = $order->get_item( $item_id );

			if ( empty( $order_item ) || ! is_a( $order_item, 'WC_Order_Item_Product' ) ) {
				return $order_item;
			}

			$order_item->update_meta_data( '_masterstudy_lms-course', 'yes' );
			$order_item->set_product_id( $product->get_id() );

			if ( 'stm-course-bundles' === get_post_type( $product->get_id() ) ) {
				$order_item->update_meta_data( '_bundle_id', $product->get_id() );
			}

			$order_item->save();
			$order->save();
		}

		return $order_item;
	}

	/**
	 * Search product data for a term and return ids.
	 *
	 * @param  string     $results defaults value.
	 * @param  string     $term Search term.
	 * @param  string     $type Type of product.
	 * @param  bool       $include_variations Include variations in search or not.
	 * @param  bool       $all_statuses Should we search all statuses or limit to published.
	 * @param  null|int   $limit Limit returned results.
	 * @return array of ids
	 */
	public function product_pre_search_products( $results, $term, $type, $include_variations, $all_statuses, $limit ) {
		global $wpdb;

		$post_types   = $include_variations ? array( 'product', 'product_variation' ) : array( 'product' );
		$join_query   = '';
		$type_where   = '';
		$status_where = '';
		$limit_query  = '';

		$post_types[] = MasterStudy\Lms\Plugin\PostType::COURSE;

		if ( is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			$post_types[] = 'stm-course-bundles';
		}

		// When searching variations we should include the parent's meta table for use in searches.
		if ( $include_variations ) {
			$join_query = " LEFT JOIN {$wpdb->wc_product_meta_lookup} parent_wc_product_meta_lookup
			 ON posts.post_type = 'product_variation' AND parent_wc_product_meta_lookup.product_id = posts.post_parent ";
		}

		/**
		 * Hook woocommerce_search_products_post_statuses.
		 *
		 * @param array $post_statuses List of post statuses.
		 */
		$post_statuses = apply_filters(
			'woocommerce_search_products_post_statuses',
			current_user_can( 'edit_private_products' ) ? array( 'private', 'publish' ) : array( 'publish' )
		);

		// See if search term contains OR keywords.
		if ( stristr( $term, ' or ' ) ) {
			$term_groups = preg_split( '/\s+or\s+/i', $term );
		} else {
			$term_groups = array( $term );
		}

		$search_where   = '';
		$search_queries = array();

		foreach ( $term_groups as $term_group ) {
			// Parse search terms.
			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $term_group, $matches ) ) {
				$search_terms = $this->get_valid_search_terms( $matches[0] );
				$count        = count( $search_terms );

				// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
				if ( 9 < $count || 0 === $count ) {
					$search_terms = array( $term_group );
				}
			} else {
				$search_terms = array( $term_group );
			}

			$term_group_query = '';
			$searchand        = '';

			foreach ( $search_terms as $search_term ) {
				$like = '%' . $wpdb->esc_like( $search_term ) . '%';

				// Variations should also search the parent's meta table for fallback fields.
				if ( $include_variations ) {
					$variation_query = $wpdb->prepare( " OR ( wc_product_meta_lookup.sku = '' AND parent_wc_product_meta_lookup.sku LIKE %s ) ", $like );
				} else {
					$variation_query = '';
				}

				$term_group_query .= $wpdb->prepare( " {$searchand} ( ( posts.post_title LIKE %s) OR ( posts.post_excerpt LIKE %s) OR ( posts.post_content LIKE %s ) OR ( wc_product_meta_lookup.sku LIKE %s ) $variation_query)", $like, $like, $like, $like ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$searchand         = ' AND ';
			}

			if ( $term_group_query ) {
				$search_queries[] = $term_group_query;
			}
		}

		if ( ! empty( $search_queries ) ) {
			$search_where = ' AND (' . implode( ') OR (', $search_queries ) . ') ';
		}

		if ( ! empty( $include ) && is_array( $include ) ) {
			$search_where .= ' AND posts.ID IN(' . implode( ',', array_map( 'absint', $include ) ) . ') ';
		}

		if ( ! empty( $exclude ) && is_array( $exclude ) ) {
			$search_where .= ' AND posts.ID NOT IN(' . implode( ',', array_map( 'absint', $exclude ) ) . ') ';
		}

		if ( 'virtual' === $type ) {
			$type_where = ' AND ( wc_product_meta_lookup.virtual = 1 ) ';
		} elseif ( 'downloadable' === $type ) {
			$type_where = ' AND ( wc_product_meta_lookup.downloadable = 1 ) ';
		}

		if ( ! $all_statuses ) {
			$status_where = " AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ";
		}

		if ( $limit ) {
			$limit_query = $wpdb->prepare( ' LIMIT %d ', $limit );
		}

		// phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
		$search_results = $wpdb->get_results(
		// phpcs:disable
			"SELECT DISTINCT posts.ID as product_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
			 LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id
			 $join_query
			WHERE posts.post_type IN ('" . implode( "','", $post_types ) . "')
			$search_where
			$status_where
			$type_where
			ORDER BY posts.post_parent ASC, posts.post_title ASC
			$limit_query
			"
		// phpcs:enable
		);

		$product_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'product_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

		if ( is_numeric( $term ) ) {
			$post_id   = absint( $term );
			$post_type = get_post_type( $post_id );

			if ( 'product_variation' === $post_type && $include_variations ) {
				$product_ids[] = $post_id;
			} elseif ( 'product' === $post_type ) {
				$product_ids[] = $post_id;
			}

			$product_ids[] = wp_get_post_parent_id( $post_id );
		}

		return wp_parse_id_list( $product_ids );
	}

	/**
	 * Check if the terms are suitable for searching.
	 *
	 * Uses an array of stopwords (terms) that are excluded from the separate
	 * term matching when searching for posts. The list of English stopwords is
	 * the approximate search engines list, and is translatable.
	 *
	 * @param array $terms Terms to check.
	 * @return array Terms that are not stopwords.
	 */
	protected function get_valid_search_terms( $terms ) {
		$valid_terms = array();
		$stopwords   = $this->get_search_stopwords();

		foreach ( $terms as $term ) {
			// keep before/after spaces when term is for exact match, otherwise trim quotes and spaces.
			if ( preg_match( '/^".+"$/', $term ) ) {
				$term = trim( $term, "\"'" );
			} else {
				$term = trim( $term, "\"' " );
			}

			// Avoid single A-Z and single dashes.
			if ( empty( $term ) || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) ) {
				continue;
			}

			if ( in_array( wc_strtolower( $term ), $stopwords, true ) ) {
				continue;
			}

			$valid_terms[] = $term;
		}

		return $valid_terms;
	}

	/**
	 * Retrieve stopwords used when parsing search terms.
	 *
	 * @return array Stopwords.
	 */
	protected function get_search_stopwords() {
		// Translators: This is a comma-separated list of very common words that should be excluded from a search, like a, an, and the. These are usually called "stopwords". You should not simply translate these individual words into your language. Instead, look for and provide commonly accepted stopwords in your language.
		$stopwords = array_map(
			'wc_strtolower',
			array_map(
				'trim',
				explode(
					',',
					_x(
						'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
						'Comma-separated list of search stopwords in your language',
						'masterstudy-lms-learning-management-system-pro'
					)
				)
			)
		);

		return apply_filters( 'wp_search_stopwords', $stopwords );
	}

	public function set_price_filter_post_type( $post_types ) {
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			$post_types[] = MasterStudy\Lms\Plugin\PostType::COURSE;
		} else {
			$post_types = array( MasterStudy\Lms\Plugin\PostType::COURSE );
		}

		return $post_types;
	}

	/**
	 * Insert lms courses into product lookup table
	 * */
	public function rest_insert_system_status_tool( $tool ) {
		if ( ! empty( $tool ) && is_array( $tool ) ) {
			$action_id = $tool['id'];

			if ( 'regenerate_product_lookup_tables' === $action_id ) {
				$is_cli = Constants::is_true( 'WP_CLI' );

				if ( $is_cli ) {
					$this->insert_course_into_product_lookup( 'min_max_price' );
				} else {
					WC()->queue()->schedule_single(
						time() + 15,
						'lms_insert_course_into_product_lookup',
						array(
							'column' => 'min_max_price',
						),
						'lms_update_product_lookup_tables'
					);
				}
			}
		}
	}

	public function update_course_price() {
		if ( STM_LMS_Options::get_option( 'woocommerce_course_visibility', false ) ) {
			$this->insert_course_into_product_lookup( 'min_max_price' );
		}
	}

	public function insert_course_into_product_lookup( $column ) {
		if ( empty( $column ) ) {
			return;
		}

		global $wpdb;

		// Make a row per product in lookup table.
		$wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$wpdb->wc_product_meta_lookup} (`product_id`)
                    SELECT posts.ID FROM {$wpdb->posts} posts WHERE posts.post_type = %s",
				MasterStudy\Lms\Plugin\PostType::COURSE
			)
		);

		if ( 'min_max_price' === $column ) {
			$wpdb->query(
				"UPDATE {$wpdb->wc_product_meta_lookup} lookup_table
					INNER JOIN (
						SELECT lookup_table.product_id, MIN( meta_value+0 ) as min_price, MAX( meta_value+0 ) as max_price
						FROM {$wpdb->wc_product_meta_lookup} lookup_table
						LEFT JOIN {$wpdb->postmeta} meta1 ON lookup_table.product_id = meta1.post_id AND ( meta1.meta_key = 'sale_price' AND meta1.meta_value <> '' OR meta1.meta_key = 'price' AND meta1.meta_value <> '' )
						WHERE
							meta1.meta_value <> ''
						GROUP BY lookup_table.product_id
					) as source on source.product_id = lookup_table.product_id
				SET
					lookup_table.min_price = source.min_price,
					lookup_table.max_price = source.max_price"
			);
		}
	}

	/**
	 * When the_post is called, put product data into a global.
	 *
	 * @param mixed $post Post Object.
	 *
	 * @return WC_Product
	 */
	public function setup_product_data( $post ) {
		unset( $GLOBALS['product'] );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		$post_types = apply_filters( 'masterstudy_woo_post_types', array( 'product', 'product_variation' ) );

		if ( empty( $post->post_type ) || ! in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		$GLOBALS['product'] = wc_get_product( $post );

		return $GLOBALS['product'];
	}

	public function stm_lms_woocommerce_order_created( $order_id ) {
		$order      = new WC_Order( $order_id );
		$user_id    = $order->get_user_id();
		$post_types = apply_filters( 'masterstudy_woo_post_types', array() );

		foreach ( $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $order_item ) {
			if ( is_a( $order_item, 'WC_Order_Item_Product' ) && in_array( get_post_type( $order_item->get_product_id() ), $post_types, true ) ) {
				if ( empty( $order_item->get_meta( '_enterprise_id' ) ) ) {

					$order_courses = STM_LMS_Course::get_user_course( $user_id, (int) $order_item->get_product_id() );

					if ( ! empty( $order_courses ) ) {
						$order_courses = isset( $order_courses['course_id'] ) ? array( $order_courses ) : $order_courses;

						foreach ( $order_courses as $item ) {
							if ( isset( $item['course_id'] ) ) {
								$course_id = (int) $item['course_id'];

								if ( $course_id && metadata_exists( 'post', $course_id, 'end_time' ) ) {
									stm_lms_update_start_time_in_user_course( $user_id, $course_id );
								}
							}
						}
						continue;
					}

					STM_LMS_Course::add_user_course( $order_item->get_product_id(), $user_id, 0, 0 );
					STM_LMS_Course::add_student( $order_item->get_product_id() );
				}
			}

			do_action( 'stm_lms_woocommerce_order_approved', $order_item, $user_id );
		}
	}

	public function stm_lms_woocommerce_order_cancelled( $order_id ) {
		$order      = new WC_Order( $order_id );
		$user_id    = $order->get_user_id();
		$post_types = apply_filters( 'masterstudy_woo_post_types', array() );

		foreach ( $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) ) as $order_item ) {
			if (
				is_a( $order_item, 'STM_Course_Order_Item_Product' ) &&
				in_array( get_post_type( $order_item->get_product_id() ), $post_types, true ) &&
				! self::has_course_been_purchased( $user_id, $order_item->get_product_id() )
			) {
				stm_lms_get_delete_user_course( $user_id, $order_item->get_product_id() );
				STM_LMS_Course::remove_student( $order_item->get_product_id() );
			}

			do_action( 'stm_lms_woocommerce_order_cancelled', $order_item, $user_id );
		}
	}

	public static function has_course_been_purchased( $user_id, $course_id ) {
		return wc_customer_bought_product( '', $user_id, $course_id );
	}

	public static function add_to_cart( $item_id, $cart_item_data = array() ) {
		try {
			// Load cart functions which are loaded only on the front-end.
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
			include_once WC_ABSPATH . 'includes/class-wc-cart.php';

			if ( is_null( WC()->cart ) ) {
				wc_load_cart();
			}

			if ( PostType::COURSE_BUNDLES === get_post_type( $item_id ) ) {
				$cart_item_data = array( 'bundle_id' => $item_id );
			}

			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( $item_id === $cart_item['product_id'] ) {
					return false;
				}
			}

			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $item_id, 1, 0, array(), $cart_item_data );
			$add_to_cart       = WC()->cart->add_to_cart( $item_id, 1, 0, array(), $cart_item_data );

			if ( $passed_validation && false !== $add_to_cart ) {
				wc_add_to_cart_message( array( $item_id => 1 ), true );
				WC()->cart->calculate_totals();

				return true;
			}
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}

		return false;
	}

	/**
	 * Clear default WooCommerce content on the Thank You page.
	 */
	public function masterstudy_thankyou_page() {
		if ( is_wc_endpoint_url( 'order-received' ) ) {
			remove_all_actions( 'woocommerce_thankyou' );
			add_action( 'woocommerce_thankyou', array( $this, 'masterstudy_create_template_thankyou_message' ), 10 );
		}
	}

	/**
	 * Custom Thank You page template for WooCommerce orders.
	 *
	 * @param int $order_id The ID of the order.
	 */
	public function masterstudy_create_template_thankyou_message( $order_id ) {
		// Fetch the order object using the order ID
		$order = wc_get_order( $order_id );
		include STM_LMS_PRO_PATH . '/stm-lms-templates/checkout/woocommerce-thankyou.php';
	}

	/**
	 * List of LMS post types for purchase
	 *
	 * @param $post_types
	 *
	 * @return array|mixed
	 */
	public function add_post_types( $post_types ) {
		$post_type = MasterStudy\Lms\Plugin\PostType::COURSE;

		if ( is_array( $post_types ) && ! empty( $post_types ) ) {
			$post_types = array_merge( $post_types, array( $post_type ) );
		} else {
			$post_types = array( $post_type );
		}

		return $post_types;
	}

	/**
	 * Hook into woocommerce_product_query to do the main product query.
	 *
	 * @param WP_Query $query Query instance.
	 */
	public function toggle_visibility_product( WP_Query $query ) {
		if (
			STM_LMS_Options::get_option( 'woocommerce_course_visibility', false ) &&
			class_exists( 'MasterStudy\Lms\Plugin\PostType' ) &&
			! is_product_taxonomy()
		) {
			$post_types = array( $query->get( 'post_type' ), MasterStudy\Lms\Plugin\PostType::COURSE );

			$query->set( 'post_type', $post_types );
		}
	}

	public function set_add_to_cart_text( $text, $product ) {
		if ( MasterStudy\Lms\Plugin\PostType::COURSE === get_post_type( $product->get_id() ) ) {
			$text = __( 'View course', 'masterstudy-lms-learning-management-system-pro' );
		}

		return $text;
	}

	public function set_add_to_cart_url( $url, $product ) {
		if ( MasterStudy\Lms\Plugin\PostType::COURSE === get_post_type( $product->get_id() ) ) {
			$url = get_the_permalink( $product->get_id() );
		}

		return $url;
	}

	public function set_add_to_cart_args( $args, $product ) {
		if ( MasterStudy\Lms\Plugin\PostType::COURSE === get_post_type( $product->get_id() ) ) {
			$args['class']      = str_replace( 'product_type_simple add_to_cart_button ajax_add_to_cart', '', $args['class'] );
			$args['attributes'] = array();
		}

		return $args;
	}

	public function create_order_line_item_object( $item, $cart_item_key, $values ) {
		$product    = $values['data'];
		$post_types = apply_filters( 'masterstudy_woo_post_types', array() );

		if ( in_array( get_post_type( $product->get_id() ), $post_types, true ) ) {
			return new STM_Course_Order_Item_Product();
		}

		return $item;
	}

	public function create_order_line_item( $item, $cart_item_key, $values, $order ) {
		$product    = $values['data'];
		$post_types = apply_filters( 'masterstudy_woo_post_types', array() );
		$product_id = $product->get_id();

		if ( in_array( get_post_type( $product_id ), $post_types, true ) ) {
			$item->update_meta_data( '_masterstudy_lms-course', 'yes' );
			do_action( 'stm_lms_create_order_line_item', $item, $values, $order );
		}
	}

}
