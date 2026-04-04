<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\ReccuringInterval;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\SubscriptionPlanType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Helpers\DateTimeHelper;

class SubscriptionPlanRepository extends AbstractRepository {
	private array $sortable = array(
		'type',
		'recurring_interval',
		'period',
		'trial_period',
		'price',
		'is_certified',
		'is_enabled',
		'plan_order',
	);

	private static array $instructor_allowed_types = array( SubscriptionPlanType::COURSE, SubscriptionPlanType::COURSE );

	private function get_listed_plan_types(): string {
		global $wpdb;

		$types = array( SubscriptionPlanType::FULL_SITE, SubscriptionPlanType::CATEGORY );

		$placeholders = implode( ',', array_fill( 0, count( $types ), '%s' ) );

		//phpcs:ignore
		return $wpdb->prepare( $placeholders, ...$types );
	}

	public static function is_instructor_not_allowed( string $type ): bool {
		return \STM_LMS_Instructor::has_instructor_role() && ! current_user_can( 'manage_options' ) && ! in_array( $type, self::$instructor_allowed_types, true );
	}

	public function table_name(): string {
		return stm_lms_subscription_plans_table_name( $this->db );
	}

	public function get_fields_format(): array {
		return array(
			'%s', // type
			'%s', // name
			'%s', // description
			'%d', // recurring_value
			'%s', // recurring_interval
			'%d', // billing_cycles
			'%f', // price
			'%f', // sale_price
			'%s', // sale_price_from
			'%s', // sale_price_to
			'%s', // plan_features
			'%f', // enrollment_fee
			'%d', // trial_period
			'%d', // is_featured
			'%s', // featured_text
			'%d', // is_certified
			'%d', // is_enabled
			'%d', // plan_order
		);
	}

	public function get_fields_values( array $data ): array {
		return array(
			'type'               => $data['type'],
			'name'               => $data['name'],
			'description'        => $data['description'] ?? null,
			'recurring_value'    => $data['recurring_value'] ?? 1,
			'recurring_interval' => $data['recurring_interval'] ?? ReccuringInterval::MONTH,
			'billing_cycles'     => $data['billing_cycles'] ?? 0,
			'price'              => $data['price'],
			'sale_price'         => $data['sale_price'] ?? null,
			'sale_price_from'    => $data['sale_price_from'] ?? null,
			'sale_price_to'      => $data['sale_price_to'] ?? null,
			'plan_features'      => $data['plan_features'] ? wp_json_encode( $data['plan_features'] ) : null,
			'enrollment_fee'     => $data['enrollment_fee'] ?? 0,
			'trial_period'       => $data['trial_period'] ?? 0,
			'is_featured'        => $data['is_featured'] ?? 0,
			'featured_text'      => $data['featured_text'] ?? null,
			'is_certified'       => $data['is_certified'] ?? 1,
			'is_enabled'         => $data['is_enabled'] ?? 1,
			'plan_order'         => $data['plan_order'] ?? 0,
		);
	}

	public function get( int $plan_id ): ?array {
		$plan = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE id = %d",
				$plan_id
			),
			ARRAY_A
		);

		if ( ! $plan ) {
			return null;
		}

		$plan['items'] = ( new SubscriptionPlanItemRepository() )->get_by_plan_id( $plan_id );

		return $plan;
	}

	public function create( array $data ): int {
		$data['plan_order'] = ! empty( $data['plan_order'] )
			? $data['plan_order']
			: $this->get_max_plan_order() + 1;
		$result             = $this->db->insert(
			$this->table_name(),
			$this->get_fields_values( $data ),
			$this->get_fields_format()
		);

		if ( false === $result ) {
			return 0;
		}

		return $this->db->insert_id;
	}

	public function update( int $plan_id, array $data ): bool {
		return $this->db->update(
			$this->table_name(),
			$this->get_fields_values( $data ),
			array(
				'id' => $plan_id,
			),
			$this->get_fields_format()
		);
	}

	public function delete( int $plan_id ): bool {
		return $this->db->delete(
			$this->table_name(),
			array( 'id' => $plan_id ),
			array( '%d' )
		);
	}

	public function list( int $page, int $per_page, string $sort, string $search ): array {
		$offset            = ( $page - 1 ) * $per_page;
		$listed_plan_types = $this->get_listed_plan_types();
		$search_sql        = '';

		if ( ! empty( $search ) ) {
			$search_sql = $this->db->prepare( 'AND p.name = %s', $search );
		}

		$total = $this->db->get_var(
			$this->db->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- No applicable variables for this query.
				"SELECT COUNT(*) FROM {$this->table_name()} as p WHERE p.type IN ($listed_plan_types) {$search_sql}",
			)
		);

		$sort_params = array(
			'key'       => 'plan_order',
			'direction' => 'ASC',
		);

		if ( ! empty( $sort ) ) {
			$params = \STM_LMS_Helpers::get_sort_params_by_string( $sort );

			if ( isset( $params['key'] ) && 'trial' === $params['key'] ) {
				$params['key'] = 'trial_period';
			}

			if ( isset( $params['key'] ) && in_array( $params['key'], $this->sortable, true ) ) {
				$sort_params = $params;
			}
		}

		$direction = ( 'DESC' === strtoupper( $sort_params['direction'] ) ) ? 'DESC' : 'ASC';
		$order_sql = 'p.plan_order ASC';

		switch ( $sort_params['key'] ) {
			case 'recurring_interval':
				$order_sql = "CASE p.recurring_interval
			WHEN 'day' THEN 1
			WHEN 'week' THEN 2
			WHEN 'month' THEN 3
			WHEN 'year' THEN 4
			ELSE 5
		END {$direction}, p.plan_order ASC";
				break;

			case 'period':
				$order_sql = "( p.recurring_value * CASE p.recurring_interval
			WHEN 'day' THEN 1
			WHEN 'week' THEN 7
			WHEN 'month' THEN 30
			WHEN 'year' THEN 365
			ELSE 1
		END ) {$direction}, p.plan_order ASC";
				break;

			case 'trial_period':
				$order_sql = "p.trial_period {$direction}, p.plan_order ASC";
				break;

			case 'price':
			case 'is_certified':
			case 'is_enabled':
			case 'type':
			case 'plan_order':
				$order_sql = 'p.' . $sort_params['key'] . " {$direction}, p.plan_order ASC";
				break;
		}

		$plans = $this->db->get_results(
			$this->db->prepare(
				"SELECT p.id, p.name, p.type, p.price, p.sale_price, p.recurring_value, p.recurring_interval, p.trial_period, p.is_featured, p.is_certified, p.is_enabled, p.plan_order
		 FROM {$this->table_name()} AS p
		 WHERE p.type IN ($listed_plan_types) {$search_sql}
		 ORDER BY {$order_sql}
		 LIMIT %d OFFSET %d",
				$per_page,
				$offset
			),
			ARRAY_A
		);

		return array(
			'plans' => $plans,
			'total' => $total ? (int) $total : 0,
		);
	}

	public function get_course_plans( int $course_id, ?int $page = null, ?int $per_page = null ): array {
		$plan_items_table = stm_lms_subscription_plan_items_table_name( $this->db );
		$per_page_sql     = '';

		if ( ! empty( $page ) && ! empty( $per_page ) ) {
			$per_page_sql = $this->db->prepare(
				'LIMIT %d OFFSET %d',
				$per_page,
				( $page - 1 ) * $per_page
			);
		}

		$plans = $this->db->get_results(
			$this->db->prepare(
				"SELECT  p.*, COUNT(*) OVER() AS total_rows
                 FROM {$this->table_name()} AS p INNER JOIN {$plan_items_table} AS pi ON pi.plan_id = p.id AND pi.object_id = %d
                 WHERE p.type = %s AND p.is_enabled = 1 ORDER BY p.plan_order ASC $per_page_sql",
				$course_id,
				SubscriptionPlanType::COURSE
			),
			ARRAY_A
		);

		return array(
			'plans' => $plans,
			'total' => $plans ? (int) $plans[0]['total_rows'] : 0,
		);
	}

	public function get_course_billing_cycled_plans( int $course_id ): array {
		$plan_items_table = stm_lms_subscription_plan_items_table_name( $this->db );
		$plans            = $this->db->get_results(
			$this->db->prepare(
				"SELECT p.* FROM {$this->table_name()} AS p INNER JOIN {$plan_items_table} AS pi ON pi.plan_id = p.id AND pi.object_id = %d
				WHERE p.type = %s AND p.is_enabled = 1 AND p.billing_cycles > 0 ORDER BY p.plan_order ASC",
				$course_id,
				SubscriptionPlanType::COURSE
			),
			ARRAY_A
		);
		return $plans;
	}

	public function list_bundle_plans( int $id ) {
		$plan_items_table = stm_lms_subscription_plan_items_table_name( $this->db );

		return $this->db->get_results(
			$this->db->prepare(
				"SELECT p.* FROM {$this->table_name()} AS p INNER JOIN {$plan_items_table} AS pi ON pi.plan_id = p.id AND pi.object_id = %d
                 WHERE p.type = %s AND p.is_enabled = 1 ORDER BY p.plan_order",
				$id,
				SubscriptionPlanType::BUNDLE
			),
			ARRAY_A
		);
	}

	public function get_enabled_plans(): ?array {
		$plans = $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE type != %s AND is_enabled = 1 ORDER BY plan_order ASC",
				SubscriptionPlanType::COURSE
			),
			ARRAY_A
		);

		foreach ( $plans as $plan ) {
			$plan['items'] = ( new SubscriptionPlanItemRepository() )->get_by_plan_id( $plan['id'] );
		}

		return $plans;
	}

	public function get_enabled_plans_for_course( int $course_id ): array {
		$plans_table      = $this->table_name();
		$plan_items_table = stm_lms_subscription_plan_items_table_name( $this->db );

		$course_cat_ids = array_map(
			'intval',
			(array) stm_lms_get_terms_array( $course_id, 'stm_lms_course_taxonomy', 'term_id' )
		);

		$from_sql = "FROM {$plans_table} AS p
			LEFT JOIN {$plan_items_table} AS pi_cat
			ON pi_cat.plan_id = p.id AND pi_cat.object_type = 'category'";

		$where_parts = array( 'p.is_enabled = 1' );
		$params      = array();

		$cond_full_site = 'p.type = %s';
		$params[]       = SubscriptionPlanType::FULL_SITE;
		$cond_category  = '';

		if ( ! empty( $course_cat_ids ) ) {
			$cat_placeholders = implode( ',', array_fill( 0, count( $course_cat_ids ), '%d' ) );
			$cond_category    = " (p.type = %s AND pi_cat.object_id IN ({$cat_placeholders})) ";
			$params[]         = SubscriptionPlanType::CATEGORY;

			foreach ( $course_cat_ids as $cid ) {
				$params[] = (int) $cid;
			}
		}

		$type_union    = $cond_full_site . ( $cond_category ? ' OR ' . $cond_category : '' );
		$where_parts[] = '( ' . $type_union . ' )';
		$where_sql     = 'WHERE ' . implode( ' AND ', $where_parts );

		$select_sql = "SELECT DISTINCT p.* {$from_sql} {$where_sql} ORDER BY p.plan_order ASC, p.id ASC";

		$query = $this->db->prepare( $select_sql, $params );
		$plans = $this->db->get_results( $query, ARRAY_A );

		if ( ! is_array( $plans ) ) {
			return array();
		}

		foreach ( $plans as &$plan ) {
			$plan_id       = (int) $plan['id'];
			$plan['items'] = ( new SubscriptionPlanItemRepository() )->get_by_plan_id( $plan_id );
		}

		return $plans;
	}

	public function reorder( array $plans ): bool {
		foreach ( $plans as $plan ) {
			$updated = $this->db->update(
				$this->table_name(),
				array( 'plan_order' => $plan['plan_order'] ),
				array( 'id' => $plan['id'] ),
				array( '%d' )
			);

			if ( false === $updated ) {
				return false;
			}
		}

		return true;
	}

	public static function is_plan_trial( array $plan ): bool {
		return ! empty( $plan['trial_period'] ) && $plan['trial_period'] > 0;
	}

	public static function get_actual_price( array $plan ) {
		$price = (float) $plan['price'];

		if ( ! empty( $plan['sale_price'] ) ) {
			if ( ! empty( $plan['sale_price_from'] ) && ! empty( $plan['sale_price_to'] ) ) {
				$sale_price_from = strtotime( $plan['sale_price_from'] );
				$sale_price_to   = strtotime( $plan['sale_price_to'] );
				$offset          = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
				$current_time    = time() + $offset;
				if ( $current_time >= $sale_price_from && $current_time <= $sale_price_to ) {
					$price = (float) $plan['sale_price'];
				}
			} else {
				$price = (float) $plan['sale_price'];
			}
		}

		$price = number_format( $price, 2, '.', '' );

		return apply_filters( 'masterstudy_lms_subscription_price', $price, $plan['id'] );
	}

	public static function get_actual_price_with_taxes( array $plan ): float {
		$price = (float) $plan['price'];

		if ( ! empty( $plan['sale_price'] ) ) {
			if ( ! empty( $plan['sale_price_from'] ) && ! empty( $plan['sale_price_to'] ) ) {
				$sale_price_from = strtotime( $plan['sale_price_from'] );
				$sale_price_to   = strtotime( $plan['sale_price_to'] );
				$offset          = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
				$current_time    = time() + $offset;

				if ( $current_time >= $sale_price_from && $current_time <= $sale_price_to ) {
					$price = (float) $plan['sale_price'];
				}
			} else {
				$price = (float) $plan['sale_price'];
			}
		}

		$price = number_format( $price, 2, '.', '' );

		$price_with_tax = masterstudy_lms_display_price_with_taxes( $price, get_current_user_id(), true );

		$price_with_tax = (float) number_format( $price_with_tax, 2, '.', '' );

		return $price_with_tax;
	}


	public static function is_sale_active( array $plan ): bool {
		if ( empty( $plan['sale_price'] ) ) {
			return false;
		}

		if ( ! empty( $plan['sale_price_from'] ) && ! empty( $plan['sale_price_to'] ) ) {
			$sale_price_from = strtotime( $plan['sale_price_from'] );
			$sale_price_to   = strtotime( $plan['sale_price_to'] );
			$current_time    = time();

			return ( $current_time >= $sale_price_from && $current_time <= $sale_price_to );
		}

		return true;
	}

	public function calculate_plan_datetimes( $plan, $is_renewal = false ): array {
		if ( is_numeric( $plan ) ) {
			$plan = $this->get( $plan );
		}

		if ( ! $plan ) {
			return array();
		}

		$start_date     = DateTimeHelper::now()->to_date_time_string();
		$trial_end_date = null;

		if ( ! $is_renewal ) {
			if ( $plan['trial_period'] > 0 ) {
				$trial_end_date    = DateTimeHelper::now()->add( $plan['trial_period'], 'days' )->to_date_time_string();
				$next_payment_date = $trial_end_date;
				$end_date          = $trial_end_date;
			} else {
				$end_date          = DateTimeHelper::create( $start_date )->add( $plan['recurring_value'], $plan['recurring_interval'] )->to_date_time_string();
				$next_payment_date = $end_date;
			}
		} else {
			$subscription = ( new SubscriptionRepository() )->get_by_plan_id( $plan['id'] );

			$trial_end_date    = $subscription['trial_end_date'] ?? null;
			$start_date        = $subscription['start_date'] ?? null;
			$end_date          = isset( $subscription['end_date_gmt'] )
				? DateTimeHelper::create( $subscription['end_date_gmt'] )->add( $plan['recurring_value'], $plan['recurring_interval'] )->to_date_time_string()
				: null;
			$next_payment_date = $subscription['next_payment_date'] ?? null;
		}

		return array(
			'trial_end_date'    => $trial_end_date,
			'start_date'        => $start_date,
			'end_date'          => $end_date,
			'next_payment_date' => $next_payment_date,
		);
	}

	public function get_max_plan_order(): int {
		return (int) $this->db->get_var(
			$this->db->prepare( "SELECT MAX(plan_order) FROM {$this->table_name()}" )
		) ?? 0;
	}

	public function toggle_enabled( int $plan_id, bool $is_enabled ): bool {
		return $this->db->update(
			$this->table_name(),
			array( 'is_enabled' => $is_enabled ),
			array( 'id' => $plan_id ),
			array( '%d' )
		);
	}

	public static function get_plan_type( string $type ): string {
		if ( in_array( $type, array( SubscriptionPlanType::COURSE ), true ) ) {
			return 'subscription';
		}

		return 'membership';
	}

	public function get_plan_name( int $plan_id ): string {
		$plan = $this->db->get_row(
			$this->db->prepare(
				"SELECT name FROM {$this->table_name()} WHERE id = %d",
				$plan_id
			),
			ARRAY_A
		);

		if ( ! $plan ) {
			return '';
		}

		return $plan['name'];
	}

	public function get_subscription_orders_with_queue( int $order_id, int $subscription_id ): int {
		$rows = $this->db->get_results(
			$this->db->prepare(
				"SELECT meta_id, post_id, meta_key, meta_value
				FROM {$this->db->postmeta}
				WHERE meta_key = %s
				AND CAST(meta_value AS UNSIGNED) = %d
				ORDER BY CAST(post_id AS UNSIGNED) ASC",
				'subscription_id',
				$subscription_id
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return 0;
		}

		foreach ( $rows as $i => &$row ) {
			$row['order_queue'] = $i + 1;

			if ( (int) $row['post_id'] === (int) $order_id ) {
				return $row['order_queue'];
			}
		}

		return 0;
	}
}
