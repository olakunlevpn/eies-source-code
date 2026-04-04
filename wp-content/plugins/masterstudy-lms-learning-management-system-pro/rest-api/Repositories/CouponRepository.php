<?php

namespace MasterStudy\Lms\Pro\RestApi\Repositories;

use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Coupons\CouponSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

class CouponRepository {
	protected $db;

	public function __construct() {
		global $wpdb;

		$this->db = $wpdb;
	}

	public function table_name(): string {
		return stm_lms_coupons_table_name( $this->db );
	}

	public function get( int $coupon_id ): ?array {
		$coupon = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE id = %d",
				$coupon_id
			),
			ARRAY_A
		);

		if ( ! $coupon ) {
			return null;
		}

		return $coupon;
	}

	public function get_by_code( string $code ): ?array {
		$coupon = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE code = %s",
				$code
			),
			ARRAY_A
		);

		if ( ! $coupon ) {
			return null;
		}

		return $coupon;
	}

	public function create( array $data ): int {
		$fields = $this->prepare_fields( $data, true );

		$result = $this->db->insert(
			$this->table_name(),
			$fields,
			$this->get_fields_format( $fields )
		);

		if ( false === $result ) {
			return 0;
		}

		return $this->db->insert_id;
	}

	public function update( int $coupon_id, array $data ): bool {
		$fields = $this->prepare_fields( $data, false );

		if ( empty( $fields ) ) {
			return true;
		}

		$result = $this->db->update(
			$this->table_name(),
			$fields,
			array( 'id' => $coupon_id ),
			$this->get_fields_format( $fields ),
			array( '%d' )
		);

		return false !== $result;
	}

	public function delete( int $coupon_id ): bool {
		return $this->db->delete(
			$this->table_name(),
			array( 'id' => $coupon_id ),
			array( '%d' )
		);
	}

	public function increment_used_count( int $coupon_id, int $by = 1 ): bool {
		if ( $coupon_id <= 0 || $by <= 0 ) {
			return false;
		}

		$table_name = $this->table_name();

		$sql = $this->db->prepare(
			"UPDATE {$table_name} SET used_count = COALESCE(used_count, 0) + %d WHERE id = %d",
			$by,
			$coupon_id
		);

		$result = $this->db->query( $sql );

		return ( false !== $result );
	}

	public function list(
		array $filters = array(),
		string $date_from = null,
		string $date_to = null,
		int $page = 1,
		int $per_page = 20
	): array {
		$offset = ( $page - 1 ) * $per_page;

		$where  = 'WHERE 1=1';
		$params = array();

		if ( ! empty( $filters['search'] ) ) {
			$search = $this->db->esc_like( $filters['search'] );
			$search = '%' . $search . '%';

			$where   .= ' AND (title LIKE %s OR code LIKE %s)';
			$params[] = $search;
			$params[] = $search;
		}

		if ( ! empty( $filters['status'] ) ) {
			$where   .= ' AND coupon_status = %s';
			$params[] = $filters['status'];
		}

		$tz = wp_timezone();

		if ( ! empty( $date_from ) ) {
			$dt       = new \DateTimeImmutable( $date_from . ' 00:00:00', $tz );
			$where   .= ' AND start_at >= %s';
			$params[] = $dt->format( 'Y-m-d H:i:s' );
		}

		if ( ! empty( $date_to ) ) {
			$dt       = new \DateTimeImmutable( $date_to . ' 23:59:59', $tz );
			$where   .= ' AND start_at <= %s';
			$params[] = $dt->format( 'Y-m-d H:i:s' );
		}

		$allowed_sort_columns = array(
			'coupon_status' => 'coupon_status',
			'start_date'    => 'start_at',
			'end_date'      => 'end_at',
			'used_count'    => 'used_count',
			'created_at'    => 'created_at',
		);

		$order_by = 'created_at DESC';

		if ( ! empty( $filters['sort'] ) ) {
			$parts = preg_split( '/\s+/', trim( $filters['sort'] ) );
			$col   = $parts[0] ?? '';
			$dir   = strtolower( $parts[1] ?? 'asc' );

			if ( isset( $allowed_sort_columns[ $col ] ) ) {
				$dir      = ( 'desc' === $dir ) ? 'DESC' : 'ASC';
				$order_by = $allowed_sort_columns[ $col ] . ' ' . $dir;
			}
		}

		$count_sql = "SELECT COUNT(*) FROM {$this->table_name()} {$where}";

		if ( ! empty( $params ) ) {
			$total = $this->db->get_var(
				$this->db->prepare( $count_sql, $params )
			);
		} else {
			$total = $this->db->get_var( $count_sql );
		}

		$select_sql = "SELECT * FROM {$this->table_name()} {$where} ORDER BY {$order_by} LIMIT %d OFFSET %d";

		$params_with_paging   = $params;
		$params_with_paging[] = $per_page;
		$params_with_paging[] = $offset;

		$coupons = $this->db->get_results(
			$this->db->prepare( $select_sql, $params_with_paging ),
			ARRAY_A
		);

		return array(
			'coupons' => $coupons,
			'total'   => $total ? (int) $total : 0,
		);
	}

	public function bulk_delete_coupons( array $request = array() ) {
		$coupons     = $request['coupons'] ?? array();
		$errors      = array();
		$success_ids = array();

		foreach ( $coupons as $coupon ) {
			$coupon_id = isset( $coupon['id'] ) ? (int) $coupon['id'] : 0;

			if ( $coupon_id <= 0 ) {
				$errors[] = array(
					'id'      => $coupon_id,
					'message' => esc_html__( 'Invalid coupon ID', 'masterstudy-lms-learning-management-system-pro' ),
				);
				continue;
			}

			$coupon_data = $this->get( $coupon_id );

			if ( ! $coupon_data ) {
				$errors[] = array(
					'id'      => $coupon_id,
					'message' => esc_html__( 'Coupon not found', 'masterstudy-lms-learning-management-system-pro' ),
				);
				continue;
			}

			$deleted = $this->delete( $coupon_id );

			if ( ! $deleted ) {
				$errors[] = array(
					'id'      => $coupon_id,
					'message' => esc_html__( 'Failed to delete coupon', 'masterstudy-lms-learning-management-system-pro' ),
				);
			} else {
				$success_ids[] = $coupon_id;
			}
		}

		return array(
			'success_ids' => $success_ids,
			'failed'      => $errors,
		);
	}

	public function bulk_status_inactive( array $request = array() ) {
		return $this->bulk_update_status( $request, 'inactive' );
	}

	public function bulk_status_active( array $request = array() ) {
		return $this->bulk_update_status( $request, 'active' );
	}

	public function bulk_status_trash( array $request = array() ) {
		return $this->bulk_update_status( $request, 'trash' );
	}

	public function apply_cart( string $code ) {
		$coupon = $this->get_by_code( $code );

		if ( ! $coupon ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'This coupon code does not exist.', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		if ( 'active' !== $coupon['coupon_status'] ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'Coupon is not active.', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$timezone = wp_timezone();
		$now      = new \DateTimeImmutable( 'now', $timezone );

		$start_at = ! empty( $coupon['start_at'] )
			? new \DateTimeImmutable( $coupon['start_at'], $timezone )
			: null;

		$end_at = ! empty( $coupon['end_at'] )
			? new \DateTimeImmutable( $coupon['end_at'], $timezone )
			: null;

		if ( $start_at && $now < $start_at ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'Coupon is not active yet.', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		if ( $end_at && $now > $end_at ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'Coupon has expired.', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$usage_limit = isset( $coupon['usage_limit'] ) ? (int) $coupon['usage_limit'] : 0;
		$used_count  = isset( $coupon['used_count'] ) ? (int) $coupon['used_count'] : 0;

		if ( $usage_limit > 0 && $used_count >= $usage_limit ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'This coupon has reached its usage limit.', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$coupon_id        = isset( $coupon['id'] ) ? (int) $coupon['id'] : 0;
		$user_usage_limit = isset( $coupon['user_usage_limit'] ) ? (int) $coupon['user_usage_limit'] : 0;
		$user_id          = get_current_user_id();

		if ( $user_usage_limit > 0 ) {
			if ( ! $user_id ) {
				return array(
					'status'  => 'error',
					'message' => esc_html__( 'You must be logged in to use this coupon.', 'masterstudy-lms-learning-management-system-pro' ),
				);
			}

			$usage_map = get_user_meta( $user_id, 'masterstudy_coupon_usage', true );

			if ( ! is_array( $usage_map ) ) {
				$usage_map = array();
			}

			$user_used_count = isset( $usage_map[ $coupon_id ] ) ? (int) $usage_map[ $coupon_id ] : 0;

			if ( $user_used_count >= $user_usage_limit ) {
				return array(
					'status'  => 'error',
					'message' => esc_html__( 'You have already used this coupon.', 'masterstudy-lms-learning-management-system-pro' ),
				);
			}
		}

		$items = stm_lms_get_cart_items(
			$user_id,
			array( 'item_id', 'price', 'quantity', 'bundle', 'enterprise', 'is_subscription' )
		);

		if ( empty( $items ) ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'Your cart is empty. This coupon cannot be applied.', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$stats = $this->evaluate_cart_for_coupon( $coupon, $items );

		$cart_subtotal     = $stats['cart_subtotal'];
		$cart_qty          = $stats['cart_qty'];
		$eligible_subtotal = $stats['eligible_subtotal'];
		$eligible_qty      = $stats['eligible_qty'];
		$eligible_item_ids = $stats['eligible_item_ids'];
		$eligible_items    = $stats['eligible_items'];

		if ( $eligible_subtotal <= 0 || empty( $eligible_items ) ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'This coupon cannot be used for the selected item(s).', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$min_qty = isset( $coupon['min_course_quantity'] ) ? (int) $coupon['min_course_quantity'] : 0;
		if ( $min_qty > 0 && $eligible_qty < $min_qty ) {
			return array(
				'status'  => 'error',
				'message' => sprintf(
					/* translators: %d: minimum items quantity */
					esc_html__( 'This coupon requires at least %d items in your cart.', 'masterstudy-lms-learning-management-system-pro' ),
					$min_qty
				),
			);
		}

		$min_purchase_amount = isset( $coupon['min_purchase_amount'] ) ? (float) $coupon['min_purchase_amount'] : 0.0;

		if ( $min_purchase_amount > 0 ) {
			$personal_data = array();

			if ( $user_id ) {
				$personal_data = get_user_meta( $user_id, 'masterstudy_personal_data', true );
				if ( ! is_array( $personal_data ) ) {
					$personal_data = array();
				}
			}

			$taxes_display = \STM_LMS_Helpers::taxes_display();
			$tax_enabled   = ! empty( $taxes_display['enabled'] );
			$tax_included  = $tax_enabled && ! empty( $taxes_display['included'] );

			$tax_rate = 0.0;
			if ( $tax_enabled && ! empty( $personal_data['country'] ) ) {
				$tax_rate = (float) \STM_LMS_Helpers::get_tax_rate_for_personal_data( $personal_data );
			}

			$cart_total_for_min = $cart_subtotal;

			if ( $tax_enabled && $tax_rate > 0 ) {
				if ( $tax_included ) {
					$cart_total_for_min = $cart_subtotal;
				} else {
					$cart_total_for_min = $cart_subtotal * ( 1.0 + ( $tax_rate / 100.0 ) );
				}
			}

			if ( $cart_total_for_min + 0.000001 < $min_purchase_amount ) {
				return array(
					'status'  => 'error',
					'message' => sprintf(
						/* translators: %s: minimum order total */
						esc_html__( 'This coupon requires a minimum order amount of %s.', 'masterstudy-lms-learning-management-system-pro' ),
						esc_html( \STM_LMS_Helpers::display_price( $min_purchase_amount ) )
					),
				);
			}
		}

		$discount      = isset( $coupon['discount'] ) ? (float) $coupon['discount'] : 0.0;
		$discount_type = isset( $coupon['discount_type'] ) ? (string) $coupon['discount_type'] : 'percent';
		$discount_type = strtolower( $discount_type );

		if ( $eligible_subtotal <= 0.0 ) {
			return array(
				'status'  => 'error',
				'message' => esc_html__( 'This coupon cannot be used for the selected item(s).', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$discount_text = '';

		if ( $discount > 0 ) {
			if ( 'percent' === $discount_type ) {
				$discount_text = sprintf(
					/* translators: %s: discount percent (e.g. "15%") */
					esc_html__( '%s discount', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( $discount . '%' )
				);
			} elseif ( 'amount' === $discount_type ) {
				$discount_text = sprintf(
					/* translators: %s: discount amount with currency (e.g. "$10") */
					esc_html__( '%s discount', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( \STM_LMS_Helpers::display_price( $discount ) )
				);
			}
		}

		if ( ! empty( $discount_text ) ) {
			$coupon_message = sprintf(
				/* translators: %s: discount description (e.g. "15% discount" or "$10 discount") */
				esc_html__( 'Coupon applied: %s', 'masterstudy-lms-learning-management-system-pro' ),
				$discount_text
			);
		} else {
			$coupon_message = esc_html__( 'Coupon applied', 'masterstudy-lms-learning-management-system-pro' );
		}

		$cookie_expires = time() + DAY_IN_SECONDS * 7;
		$cookie_expires = apply_filters( 'masterstudy_lms_coupon_cookie_expires', $cookie_expires, $code );

		if ( ! headers_sent() ) {
			setcookie(
				'masterstudy_cart_coupon',
				rawurlencode( $code ),
				array(
					'expires'  => $cookie_expires,
					'path'     => '/',
					'secure'   => is_ssl(),
					'httponly' => false,
					'samesite' => 'Lax',
				)
			);
		}

		$applied_items = array_map(
			static function ( array $item_row ) {
				$main_id = $item_row['bundle_id'] ? (int) $item_row['bundle_id'] : (int) $item_row['item_id'];

				return array(
					'id'              => $main_id,
					'item_id'         => (int) $item_row['item_id'],
					'bundle_id'       => (int) $item_row['bundle_id'],
					'is_subscription' => (bool) $item_row['is_subscription'],
					'price'           => (float) $item_row['price'],
					'quantity'        => (int) $item_row['quantity'],
					'line_subtotal'   => (float) $item_row['line_subtotal'],
				);
			},
			$eligible_items
		);

		return array(
			'status'           => 'success',
			'message'          => $coupon_message,
			'coupon'           => $coupon ? ( new CouponSerializer() )->toArray( $coupon ) : array(),
			'applied_item_ids' => $eligible_item_ids,
			'applied_items'    => $applied_items,
			'applied_subtotal' => (float) $eligible_subtotal,
		);
	}

	public function evaluate_cart_for_coupon( array $coupon, array $items ): array {
		$cart_subtotal     = 0.0;
		$cart_qty          = 0;
		$eligible_subtotal = 0.0;
		$eligible_qty      = 0;
		$eligible_item_ids = array();
		$eligible_items    = array();

		$product_type = isset( $coupon['product_type'] ) ? strtolower( (string) $coupon['product_type'] ) : '';

		$coupon_item_ids = array();
		if ( ! empty( $coupon['items'] ) ) {
			if ( is_string( $coupon['items'] ) ) {
				$decoded = json_decode( $coupon['items'], true );
				if ( is_array( $decoded ) ) {
					$coupon_item_ids = array_map( 'intval', $decoded );
				}
			} elseif ( is_array( $coupon['items'] ) ) {
				$coupon_item_ids = array_map( 'intval', $coupon['items'] );
			}
		}

		foreach ( $items as $item ) {
			$price = isset( $item['price'] ) ? (float) $item['price'] : 0.0;
			$qty   = isset( $item['quantity'] ) ? (int) $item['quantity'] : 1;

			if ( $qty < 1 ) {
				$qty = 1;
			}

			$item_id         = isset( $item['item_id'] ) ? (int) $item['item_id'] : 0;
			$bundle_id       = ! empty( $item['bundle'] ) ? (int) $item['bundle'] : 0;
			$is_subscription = ! empty( $item['is_subscription'] );
			$is_bundle       = ! empty( $item['bundle'] );
			$is_course       = ! $is_subscription && ! $is_bundle;

			$line_total = $price * $qty;

			$cart_subtotal += $line_total;
			$cart_qty      += $qty;

			$is_eligible = false;
			if ( is_ms_lms_addon_enabled( 'subscriptions' ) && $is_subscription ) {
				$subscription_plan = ( new SubscriptionPlanRepository() )->get( $item_id );
			}

			$plan_type = ! empty( $subscription_plan ) ? ( $subscription_plan['type'] ) : null;

			switch ( $product_type ) {
				case 'full-site':
					$is_eligible = true;
					break;

				case 'all-courses':
					$is_eligible = $is_course;

					if ( ! $is_eligible ) {
						if ( 'course' === $plan_type ) {
							$is_eligible = true;
						}
					}

					break;

				case 'all-bundles':
					$is_eligible = $is_bundle;
					break;

				case 'all-membership-plans':
					if ( is_ms_lms_addon_enabled( 'subscriptions' ) && $is_subscription ) {
						if ( ! empty( $subscription_plan ) ) {
							$is_eligible = SubscriptionPlanType::FULL_SITE === $subscription_plan['type'] || SubscriptionPlanType::CATEGORY === $subscription_plan['type'];
						}
					}
					break;

				case 'specific-membership-plans':
					if ( is_ms_lms_addon_enabled( 'subscriptions' ) && $is_subscription ) {
						if ( ! empty( $subscription_plan ) ) {
							$is_membership = SubscriptionPlanType::FULL_SITE === $subscription_plan['type'] || SubscriptionPlanType::CATEGORY === $subscription_plan['type'];
							$in_items      = in_array( $item_id, $coupon_item_ids, true );
							$is_eligible   = $is_membership && $in_items;
						}
					}
					break;

				case 'all-courses-and-bundles':
					$is_eligible = ( $is_course || $is_bundle );
					break;

				case 'specific-courses':
					if ( $is_course && ! empty( $coupon_item_ids ) ) {
						if ( in_array( $item_id, $coupon_item_ids, true ) ) {
							$is_eligible = true;
						}
					}

					if ( $is_subscription && ! empty( $coupon_item_ids ) ) {
						$subs_repo = new SubscriptionPlanRepository();

						foreach ( $coupon_item_ids as $course_id ) {
							$course_plans = $subs_repo->get_course_plans( (int) $course_id );

							if ( empty( $course_plans['plans'] ) || ! is_array( $course_plans['plans'] ) ) {
								continue;
							}

							foreach ( $course_plans['plans'] as $plan ) {
								if ( isset( $plan['id'] ) && (int) $plan['id'] === (int) $item_id ) {
									$is_eligible = true;
									break 2;
								}
							}
						}
					}

					break;

				case 'specific-categories':
					if ( $is_course && $item_id > 0 && ! empty( $coupon_item_ids ) ) {
						$terms = wp_get_post_terms(
							$item_id,
							'stm_lms_course_taxonomy',
							array( 'fields' => 'ids' )
						);

						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
							$course_cat_ids = array_map( 'intval', $terms );
							$intersect      = array_intersect( $coupon_item_ids, $course_cat_ids );

							if ( ! empty( $intersect ) ) {
								$is_eligible = true;
							}
						}
					}

					if ( ! $is_eligible && $is_subscription && ! empty( $coupon_item_ids ) ) {
						$course_ids = array();

						if ( function_exists( 'stm_lms_subscription_plan_items_table_name' ) ) {
							$table = stm_lms_subscription_plan_items_table_name( $this->db );

							$course_ids = $this->db->get_col(
								$this->db->prepare(
									"SELECT object_id FROM {$table} WHERE plan_id = %d",
									(int) $item_id
								)
							);

							if ( ! empty( $course_ids ) ) {
								$course_ids = array_values(
									array_filter(
										array_unique(
											array_map( 'intval', $course_ids )
										)
									)
								);
							}
						}

						foreach ( $course_ids as $course_id ) {
							$terms = wp_get_post_terms(
								$course_id,
								'stm_lms_course_taxonomy',
								array( 'fields' => 'ids' )
							);

							if ( is_wp_error( $terms ) || empty( $terms ) ) {
								continue;
							}

							$course_cat_ids = array_map( 'intval', $terms );

							if ( ! empty( array_intersect( $coupon_item_ids, $course_cat_ids ) ) ) {
								$is_eligible = true;
								break;
							}
						}
					}

					break;

				case 'specific-bundles':
					if ( $is_bundle && ! empty( $coupon_item_ids ) ) {
						$check_id = $bundle_id ? $bundle_id : $item_id;
						if ( in_array( $check_id, $coupon_item_ids, true ) ) {
							$is_eligible = true;
						}
					}
					break;

				default:
					$is_eligible = true;
					break;
			}

			if ( $is_eligible ) {
				$eligible_subtotal += $line_total;
				$eligible_qty      += $qty;

				if ( $is_bundle && $bundle_id ) {
					$eligible_item_ids[] = $bundle_id;
				} else {
					if ( $item_id ) {
						$eligible_item_ids[] = $item_id;
					}
				}

				$eligible_items[] = array(
					'item_id'         => $item_id,
					'bundle_id'       => $bundle_id,
					'is_subscription' => (bool) $is_subscription,
					'price'           => $price,
					'quantity'        => $qty,
					'line_subtotal'   => $line_total,
				);
			}
		}

		$eligible_item_ids = array_values( array_unique( $eligible_item_ids ) );

		return array(
			'cart_subtotal'     => (float) $cart_subtotal,
			'cart_qty'          => (int) $cart_qty,
			'eligible_subtotal' => (float) $eligible_subtotal,
			'eligible_qty'      => (int) $eligible_qty,
			'eligible_item_ids' => $eligible_item_ids,
			'eligible_items'    => $eligible_items,
		);
	}

	public function remove_cart() {
		if ( ! headers_sent() ) {
			setcookie(
				'masterstudy_cart_coupon',
				'',
				array(
					'expires'  => time() - DAY_IN_SECONDS,
					'path'     => '/',
					'secure'   => is_ssl(),
					'httponly' => false,
					'samesite' => 'Lax',
				)
			);
		}

		return array(
			'status'  => 'success',
			'message' => esc_html__( 'Coupon removed', 'masterstudy-lms-learning-management-system-pro' ),
		);
	}

	private function bulk_update_status( array $request = array(), $status = '' ) {
		$coupons     = $request['coupons'] ?? array();
		$errors      = array();
		$success_ids = array();

		$allowed_statuses = array( 'active', 'inactive', 'trash' );

		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			return array(
				'success_ids' => array(),
				'failed'      => array(
					array(
						'id'      => 0,
						'message' => esc_html__( 'Invalid status value', 'masterstudy-lms-learning-management-system-pro' ),
					),
				),
			);
		}

		foreach ( $coupons as $coupon ) {
			$coupon_id = isset( $coupon['id'] ) ? (int) $coupon['id'] : 0;

			if ( $coupon_id <= 0 ) {
				$errors[] = array(
					'id'      => $coupon_id,
					'message' => esc_html__( 'Invalid coupon ID', 'masterstudy-lms-learning-management-system-pro' ),
				);
				continue;
			}

			$coupon_data = $this->get( $coupon_id );

			if ( ! $coupon_data ) {
				$errors[] = array(
					'id'      => $coupon_id,
					'message' => esc_html__( 'Coupon not found', 'masterstudy-lms-learning-management-system-pro' ),
				);
				continue;
			}

			$update_data = array(
				'coupon_status' => $status,
			);

			if ( isset( $coupon_data['updated_at'] ) ) {
				$update_data['updated_at'] = current_time( 'mysql' );
			}

			$updated = $this->update( $coupon_id, $update_data );

			if ( ! $updated ) {
				$errors[] = array(
					'id'      => $coupon_id,
					'message' => esc_html__( 'Failed to update coupon status', 'masterstudy-lms-learning-management-system-pro' ),
				);
			} else {
				$success_ids[] = $coupon_id;
			}
		}

		return array(
			'success_ids' => $success_ids,
			'failed'      => $errors,
		);
	}

	protected function prepare_fields( array $data, bool $is_create = false ): array {
		$fields = array();

		foreach ( $data as $key => $value ) {
			switch ( $key ) {
				case 'user_usage_limit':
					$fields['user_usage_limit'] = ( '' === $value || null === $value )
						? null
						: (int) $value;
					break;

				case 'start_date':
					if ( ! empty( $value ) ) {
						$start_time = isset( $data['start_time'] ) && is_string( $data['start_time'] )
							? $data['start_time']
							: null;

						$fields['start_at'] = $this->build_datetime( (int) $value, $start_time );
					} else {
						$fields['start_at'] = null;
					}
					break;

				case 'end_date':
					if ( ! empty( $value ) ) {
						$end_time = isset( $data['end_time'] ) && is_string( $data['end_time'] )
							? $data['end_time']
							: null;

						$fields['end_at'] = $this->build_datetime( (int) $value, $end_time );
					} else {
						$fields['end_at'] = null;
					}
					break;

				case 'start_time':
				case 'end_time':
					break;

				case 'items':
					$fields['items'] = is_array( $value )
						? wp_json_encode( $value )
						: $value;
					break;

				default:
					$fields[ $key ] = $value;
					break;
			}
		}

		$now = current_time( 'mysql' );

		if ( $is_create ) {
			$fields['created_at'] = $now;
		}

		$fields['updated_at'] = $now;

		return $fields;
	}

	protected function get_fields_format( array $fields ): array {
		$map = array(
			'title'               => '%s',
			'coupon_status'       => '%s',
			'code'                => '%s',
			'discount_type'       => '%s',
			'discount'            => '%f',
			'product_type'        => '%s',
			'usage_limit'         => '%d',
			'used_count'          => '%d',
			'user_usage_limit'    => '%d',
			'min_purchase_amount' => '%f',
			'min_course_quantity' => '%d',
			'start_at'            => '%s',
			'end_at'              => '%s',
			'items'               => '%s',
			'created_at'          => '%s',
			'updated_at'          => '%s',
		);

		$formats = array();

		foreach ( array_keys( $fields ) as $key ) {
			$formats[] = $map[ $key ] ?? '%s';
		}

		return $formats;
	}

	protected function build_datetime( int $date_timestamp, ?string $time ): string {
		$date_timestamp = (int) floor( $date_timestamp / 1000 );
		$dt             = date_create( '@' . $date_timestamp );

		if ( ! $dt ) {
			return '';
		}

		$timezone = function_exists( 'wp_timezone' )
			? wp_timezone()
			: new \DateTimeZone( wp_timezone_string() );

		$dt->setTimezone( $timezone );

		if ( $time && preg_match( '/^(\d{1,2}):(\d{2})$/', $time, $m ) ) {
			$dt->setTime( (int) $m[1], (int) $m[2], 0 );
		} else {
			$dt->setTime( 0, 0, 0 );
		}

		return $dt->format( 'Y-m-d H:i:s' );
	}
}
