<?php
// phpcs:ignoreFile

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Helpers\DateTimeHelper;
use MasterStudy\Lms\Pro\RestApi\Repositories\Instructor\InstructorRepository;
use STM_LMS_Helpers;

class SubscriptionRepository extends AbstractRepository {
	public function table_name(): string {
		return stm_lms_subscriptions_table_name( $this->db );
	}

	public function get_fields_format(): array {
		return array(
			'%d', // user_id
			'%d', // plan_id
			'%d', // first_order_id
			'%d', // active_order_id
			'%s', // status
			'%d', // is_trial_used
			'%s', // trial_end_date
			'%s', // start_date
			'%s', // end_date
			'%s', // next_payment_date
			'%s', // note
			'%s', // created_at
			'%s', // updated_at
		);
	}

	public function get_fields_values( array $data ): array {
		return array(
			'user_id'           => $data['user_id'],
			'plan_id'           => $data['plan_id'],
			'first_order_id'    => $data['first_order_id'],
			'active_order_id'   => $data['active_order_id'],
			'status'            => $data['status'],
			'is_trial_used'     => $data['is_trial_used'] ?? 0,
			'trial_end_date'    => $data['trial_end_date'] ?? null,
			'start_date'        => $data['start_date'] ?? current_time( 'mysql' ),
			'end_date'          => $data['end_date'] ?? null,
			'next_payment_date' => $data['next_payment_date'],
			'note'              => $data['note'] ?? null,
			'created_at'        => $data['created_at'] ?? current_time( 'mysql' ),
			'updated_at'        => $data['updated_at'] ?? current_time( 'mysql' ),
		);
	}

	public function create( array $data ): ?int {
		$result = $this->db->insert(
			$this->table_name(),
			$this->get_fields_values( $data ),
			$this->get_fields_format()
		);

		if ( false === $result ) {
			return null;
		}

		return $this->db->insert_id;
	}

	public function update( int $subscription_id, array $data ): bool {
		$data['updated_at'] = current_time( 'mysql' );

		return $this->db->update(
			$this->table_name(),
			$this->get_fields_values( $data ),
			array( 'id' => $subscription_id ),
			$this->get_fields_format()
		);
	}

	public function get( int $subscription_id ): ?array {
		$subscription = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE id = %d",
				$subscription_id
			),
			ARRAY_A
		);

		if ( ! $subscription ) {
			return null;
		}

		$subscription['meta'] = ( new SubscriptionMetaRepository() )->get_all( $subscription_id );

		return $subscription;
	}

	public function get_all_subscriptions_by_user( int $user_id ): ?array {
		return $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE user_id = %d ORDER BY created_at DESC",
				$user_id
			),
			ARRAY_A
		);
	}

	public function get_active_subscriptions_by_user( int $user_id ): ?array {
		$subscriptions = $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE user_id = %d
				AND ( (status = 'trialing' AND trial_end_date > NOW()) OR (status = 'active' AND (end_date IS NULL OR end_date > NOW())) )
				ORDER BY created_at DESC",
				$user_id
			),
			ARRAY_A
		);

		return $subscriptions;
	}

	public function get_by_gateway_subscription_id( $gateway_subscription_id ): ?array {
		$subscription_meta_table = stm_lms_subscription_meta_table_name( $this->db );

		$subscription = $this->db->get_row(
			$this->db->prepare(
				"SELECT s.* FROM {$this->table_name()} s 
				INNER JOIN {$subscription_meta_table} sm ON s.id = sm.subscription_id 
				WHERE sm.meta_key = 'gateway_subscription_id' AND sm.meta_value = %s LIMIT 1",
				$gateway_subscription_id
			),
			ARRAY_A
		);

		if ( ! $subscription ) {
			return null;
		}

		$subscription['meta'] = ( new SubscriptionMetaRepository() )->get_all( $subscription['id'] );

		return $subscription;
	}

	public function get_by_plan_id( int $plan_id ): ?array {
		return $this->db->get_row(
			$this->db->prepare( "SELECT * FROM {$this->table_name()} WHERE plan_id = %d", $plan_id ),
			ARRAY_A
		);
	}

	/**
	 * Check if a subscription plan is being used by any students
	 */
	public function is_plan_being_used( int $plan_id ): bool {
		$count = $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM {$this->table_name()} WHERE plan_id = %d",
				$plan_id
			)
		);

		return (int) $count > 0;
	}

	/**
	 * Get count of active subscriptions using a specific plan
	 */
	public function get_active_subscriptions_count_by_plan( int $plan_id ): int {
		$count = $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM {$this->table_name()} 
				WHERE plan_id = %d 
				AND status IN ('active', 'trialing') 
				AND (end_date IS NULL OR end_date > NOW())",
				$plan_id
			)
		);

		return (int) $count;
	}

	public function update_column( $subscription_id, $column, $value ): bool {
		do_action( 'masterstudy_lms_before_subscription_column_updated', $subscription_id, $column, $value );

		return $this->db->update(
			$this->table_name(),
			array(
				$column      => $value,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => $subscription_id ),
			array( '%s', '%s' )
		);
	}

	public function update_status( int $subscription_id, string $status ): bool {
		do_action( 'masterstudy_lms_before_subscription_status_updated', $subscription_id, $status );

		return $this->update_column(
			$subscription_id,
			'status',
			$status
		);
	}

	/**
	 * Delete a subscription
	 */
	public function delete( int $subscription_id ): bool {
		$result = $this->db->delete(
			$this->table_name(),
			array( 'id' => $subscription_id ),
			array( '%d' )
		);

		return false !== $result;
	}

	public function masterstudy_lms_bulk_delete_subscriptions( array $request = array() ) {
		$memberships = $request['memberships'];
		$errors      = array();

		foreach ( $memberships as $membership ) {
			$subscription_id = intval( $membership['id'] );

			// Get subscription details
			$subscription = $this->get( $subscription_id );
			if ( ! $subscription ) {
				$errors[] = array(
					'id'      => $subscription_id,
					'message' => 'Subscription not found',
				);
				continue;
			}

			if ( ! empty( $subscription['meta']['gateway'] ) && ! empty( $subscription['meta']['gateway_subscription_id'] ) && 'cancelled' !== $subscription['status'] ) {
				$payment_gateway_class = \MasterStudy\Lms\Ecommerce\Ecommerce::get_payment_gateway_class( $subscription['meta']['gateway'] );
				if ( $payment_gateway_class ) {
					try {
						( new $payment_gateway_class() )->cancel_subscription( $subscription_id );
					} catch ( \Exception $e ) {
						error_log( $e );
					}
				}
			}

			if ( 'cancelled' !== $subscription['status'] ) {
				$this->update_status( $subscription_id, 'cancelled' );
			}

			$deleted = $this->delete( $subscription_id );

			if ( ! $deleted ) {
				$errors[] = array(
					'id'      => $subscription_id,
					'message' => 'Failed to delete subscription',
				);
			}
		}

		return empty( $errors ) ? true : $errors[0];
	}

	public function update_end_date( int $subscription_id, ?string $end_date = null, bool $allow_null = true ): bool {
		if ( null === $end_date && ! $allow_null ) {
			$end_date = current_time( 'mysql' );
		}

		// wpdb handles PHP null as SQL NULL (unquoted).
		return $this->db->update(
			$this->table_name(),
			array(
				'end_date'   => $end_date, // can be NULL
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => $subscription_id ),
			array( '%s', '%s' )
		);
	}

	public function column_exists( string $table, string $column ): bool {
		$table  = esc_sql( $table );
		$column = esc_sql( $column );

		$sql = "SHOW COLUMNS FROM {$table} LIKE %s";

		return (bool) $this->db->get_var( $this->db->prepare( $sql, $column ) );
	}

	private function normalize_status( $status ): string {
		$status = strtolower( (string) $status );

		return ( 'trialing' === $status ) ? 'trial' : $status;
	}

	public function get_all(
		array $filters = array(),
		string $date_from = null,
		string $date_to = null,
		int $page = 1,
		int $per_page = 20
	): array {
		$where_conditions = array();
		$params           = array();
		$subs_table       = stm_lms_subscriptions_table_name( $this->db );
		$plans_table      = stm_lms_subscription_plans_table_name( $this->db );
		$plan_items_table = stm_lms_subscription_plan_items_table_name( $this->db );

		if ( $date_from || $date_to ) {
			$date_from          = $date_from ? ( $date_from . ' 00:00:00' ) : DateTimeHelper::now()->sub( 30, 'day' )->to_date_string() . ' 00:00:00';
			$date_to            = $date_to ? ( $date_to . ' 23:59:59' ) : DateTimeHelper::now()->to_date_string() . ' 23:59:59';
			$where_conditions[] = 's.start_date BETWEEN %s AND %s';
			$params[]           = $date_from;
			$params[]           = $date_to;
		}

		$requested_types = array();
		if ( isset( $filters['subscription_type'] ) ) {
			$requested_types = array_merge( $requested_types, (array) $filters['subscription_type'] );
		}
		if ( isset( $filters['plan_type'] ) ) {
			$requested_types = array_merge( $requested_types, (array) $filters['plan_type'] );
		}
		$requested_types = array_values( array_filter( array_map( 'sanitize_text_field', $requested_types ) ) );

		$has_course        = in_array( 'course', $requested_types, true );
		$wants_course_mode = $has_course || empty( $requested_types );
		$need_spi_join     = false;

		if ( $this->is_current_user_instructor() && $wants_course_mode && empty( $filters['is_admin'] ) ) {
			$course_ids = ( new InstructorRepository( $this->current_instructor_id, $date_from, $date_to ) )->get_instructor_course_ids();
			if ( empty( $course_ids ) ) {
				return array(
					'data'  => array(),
					'total' => 0,
				);
			}
			$placeholders       = implode( ',', array_fill( 0, count( $course_ids ), '%d' ) );
			$where_conditions[] = "spi.object_id IN ($placeholders)";
			$params             = array_merge( $params, $course_ids );
			$need_spi_join      = true;
		}

		if ( $this->is_current_user_student() ) {
			$where_conditions[] = 's.user_id = %d';
			$params[]           = $this->current_student_id;
		}

		if ( ! empty( $filters['status'] ) ) {
			$where_conditions[] = 's.status = %s';
			$params[]           = $filters['status'];
		}
		if ( ! empty( $filters['plan_type'] ) ) {
			$where_conditions[] = 'sp.type = %s';
			$params[]           = $filters['plan_type'];
		} elseif ( ! empty( $filters['subscription_type'] ) ) {
			$types = (array) $filters['subscription_type'];
			$types = array_values( array_filter( array_map( 'sanitize_text_field', $types ) ) );
			if ( ! empty( $types ) ) {
				$placeholders       = implode( ',', array_fill( 0, count( $types ), '%s' ) );
				$where_conditions[] = "sp.type IN ($placeholders)";
				$params             = array_merge( $params, $types );
			}
		}
		if ( ! empty( $filters['user_id'] ) ) {
			$where_conditions[] = 's.user_id = %d';
			$params[]           = (int) $filters['user_id'];
		}
		if ( ! empty( $filters['subscription_id'] ) ) {
			$where_conditions[] = 's.id = %d';
			$params[]           = (int) $filters['subscription_id'];
		}
		if ( ! empty( $filters['search'] ) ) {
			$search_term        = '%' . $this->db->esc_like( $filters['search'] ) . '%';
			$where_conditions[] = '(sp.name LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s)';
			$params[]           = $search_term;
			$params[]           = $search_term;
			$params[]           = $search_term;
		}

		$order_by  = 's.start_date';
		$order_dir = 'DESC';

// Accept: "-status", "status", "status asc", "status desc"
		if ( ! empty( $filters['sort'] ) && is_string( $filters['sort'] ) ) {
			$sort_raw = strtolower( trim( $filters['sort'] ) );

			$allowed = array(
				'id'     => 's.id',
				'status' => 's.status',
				'date'   => 's.start_date',
			);

			if ( preg_match( '/^\s*(-?)(id|status|date)\s*(asc|desc)?\s*$/', $sort_raw, $m ) ) {
				$minus     = ! empty( $m[1] );
				$sort_key  = $m[2];
				$dir_token = isset( $m[3] ) ? $m[3] : '';

				$order_by  = $allowed[ $sort_key ];
				$order_dir = ( $minus || 'desc' === $dir_token ) ? 'DESC' : 'ASC';
			}
		}

		$where_clause = ! empty( $where_conditions ) ? 'WHERE ' . implode( ' AND ', $where_conditions ) : '';

		$from_sql = "FROM $subs_table s
			INNER JOIN $plans_table sp ON s.plan_id = sp.id
			LEFT JOIN {$this->db->users} u ON s.user_id = u.ID";

		if ( $need_spi_join ) {
			$from_sql .= " LEFT JOIN $plan_items_table spi ON sp.id = spi.plan_id AND spi.object_type = 'course'";
		}

		$count_sql   = "SELECT COUNT(DISTINCT s.id) AS total $from_sql $where_clause";
		$count_query = ! empty( $params ) ? $this->db->prepare( $count_sql, $params ) : $count_sql;
		$total_count = (int) $this->db->get_var( $count_query );

		if ( 0 === $total_count ) {
			return array(
				'data'  => array(),
				'total' => 0,
			);
		}

		$per_page = max( 1, (int) $per_page );
		$page     = max( 1, (int) $page );
		$offset   = ( $page - 1 ) * $per_page;

		$ids_sql = "SELECT DISTINCT s.id $from_sql $where_clause
	ORDER BY $order_by $order_dir, s.id DESC
	LIMIT %d OFFSET %d";
		$ids_params = array_merge( $params, array( $per_page, $offset ) );
		$ids_query  = $this->db->prepare( $ids_sql, $ids_params );
		$ids        = $this->db->get_col( $ids_query );

		if ( empty( $ids ) ) {
			return array(
				'data'  => array(),
				'total' => $total_count,
			);
		}

		$id_placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );

		$details_sql = "SELECT
	s.id, s.user_id AS user_id, sp.id AS plan_id, sp.name AS plan, sp.price AS amount,
	sp.sale_price,
	sp.sale_price_from,
	sp.sale_price_to,
	sp.recurring_interval AS recurring_interval, sp.is_enabled AS is_enabled, sp.type,
	u.display_name AS user, u.user_email AS email,
	UNIX_TIMESTAMP(s.start_date) AS date,
	UNIX_TIMESTAMP(s.next_payment_date) AS autoRenew,
	UNIX_TIMESTAMP(s.end_date) AS end_datee,
	s.status
	$from_sql
	WHERE s.id IN ( $id_placeholders )
	ORDER BY $order_by $order_dir, s.id DESC";

		$details_params = $ids;
		$details_query  = $this->db->prepare( $details_sql, $details_params );
		$rows           = $this->db->get_results( $details_query, ARRAY_A );

		$data = array();

		if ( is_array( $rows ) && ! empty( $rows ) ) {
			$latest_by_plan = array();

			foreach ( $rows as $r ) {
				$pid = (int) $r['plan_id'];
				$dt  = (int) $r['date'];
				$rid = (int) $r['id'];

				if ( ! isset( $latest_by_plan[ $pid ] ) ) {
					$latest_by_plan[ $pid ] = array(
						'id'   => $rid,
						'date' => $dt,
					);
					continue;
				}

				$best = $latest_by_plan[ $pid ];
				if ( $dt > $best['date'] || ( $dt === $best['date'] && $rid > $best['id'] ) ) {
					$latest_by_plan[ $pid ] = array(
						'id'   => $rid,
						'date' => $dt,
					);
				}
			}

			foreach ( $rows as $row ) {
				$subscription  = $row;
				$plan_type_raw = isset( $row['type'] ) ? $row['type'] : '';

				$plan_id                   = (int) $row['plan_id'];
				$row_id                    = (int) $row['id'];
				$subscription['is_latest'] = ( $latest_by_plan[ $plan_id ]['id'] === $row_id ) ? true : false;

				$subscription['status'] = $this->normalize_status( $row['status'] ?? '' );

				if ( 'course' === $plan_type_raw ) {
					$course_id = (int) $this->db->get_var(
						$this->db->prepare(
							"SELECT object_id
							FROM {$plan_items_table}
							WHERE plan_id = %d AND object_type = %s
							ORDER BY id ASC
							LIMIT 1",
							(int) $row['plan_id'],
							'course'
						)
					);

					if ( $course_id ) {
						$course_title = get_the_title( $course_id );
						$course_url   = get_permalink( $course_id );

						if ( $course_title ) {
							$subscription['course'] = array(
								'id'    => $course_id,
								'title' => $course_title,
								'url'   => $course_url,
							);
						}

						$subscription['subs_for_course_enabled'] = (bool) get_post_meta( $course_id, 'subscriptions', true );
					}
				}

				if ( ! empty( $subscription['sale_price'] ) ) {
					if ( ! empty( $subscription['sale_price_from'] ) && ! empty( $subscription['sale_price_to'] ) ) {
						$sale_price_from = strtotime( $subscription['sale_price_from'] );
						$sale_price_to   = strtotime( $subscription['sale_price_to'] );
						$offset          = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
						$current_time    = time() + $offset;

						if ( $current_time >= $sale_price_from && $current_time <= $sale_price_to ) {
							$subscription['amount'] = (float) $subscription['sale_price'];
						}
					} else {
						$subscription['amount'] = (float) $subscription['sale_price'];
					}
				}
				$data[] = $subscription;
			}
		} else {
			$data = array();
		}

		return array(
			'data'  => $data,
			'total' => $total_count,
		);
	}

	public function get_recurring_interval_by_subscription_id( int $subscription_id ): ?string {
		$subs_table = stm_lms_subscriptions_table_name( $this->db );
		$plans_table = stm_lms_subscription_plans_table_name( $this->db );

		$sql = "SELECT sp.recurring_interval 
            FROM {$subs_table} s
            INNER JOIN {$plans_table} sp ON s.plan_id = sp.id
            WHERE s.id = %d
            LIMIT 1";

		return $this->db->get_var( $this->db->prepare( $sql, $subscription_id ) );
	}

	/**
	 * Details for single subscription
	 */
	public function get_subscription_details( int $subscription_id ): ?array {
		$subs_table       = stm_lms_subscriptions_table_name( $this->db );
		$subs_meta_table  = stm_lms_subscription_meta_table_name( $this->db );
		$plans_table      = stm_lms_subscription_plans_table_name( $this->db );
		$plan_items_table = stm_lms_subscription_plan_items_table_name( $this->db );

		$has_first_order  = $this->column_exists( $subs_table, 'first_order_id' );
		$has_active_order = $this->column_exists( $subs_table, 'active_order_id' );

		$first_order_select  = $has_first_order ? 's.first_order_id' : 'NULL AS first_order_id';
		$active_order_select = $has_active_order ? 's.active_order_id' : 'NULL AS active_order_id';

		$sql = " SELECT s.id AS subscription_id, s.user_id, s.plan_id, s.status, s.note, s.start_date, s.end_date, s.next_payment_date, {$first_order_select},
				{$active_order_select}, sp.name AS plan_name, sp.type AS plan_type, sp.price AS plan_price, sp.sale_price, sp.sale_price_from, sp.sale_price_to, sp.recurring_interval AS plan_interval, sp.recurring_value AS plan_recurring_value, sp.is_enabled AS is_enabled, sp.billing_cycles as plan_billing_cycles, u.display_name AS user_name, u.user_email AS user_email
			FROM {$subs_table} s
			INNER JOIN {$plans_table} sp
				ON s.plan_id = sp.id
			LEFT JOIN {$this->db->users} u
				ON s.user_id = u.ID
			WHERE s.id = %d
			LIMIT 1 ";

		$row = $this->db->get_row( $this->db->prepare( $sql, $subscription_id ), ARRAY_A );
		if ( empty( $row ) ) {
			return null;
		}

		$coupon = $this->db->get_var(
			$this->db->prepare(
				" SELECT sm.meta_value FROM $subs_meta_table sm WHERE sm.subscription_id = %d AND sm.meta_key IN ('coupon', 'coupon_code', 'applied_coupon') ORDER BY sm.id DESC LIMIT 1 ",
				$subscription_id
			)
		);

		$trial_end_meta = $this->db->get_var(
			$this->db->prepare(
				" SELECT sm.meta_value FROM $subs_meta_table sm WHERE sm.subscription_id = %d AND sm.meta_key IN ('trial_end_date','trial_end','trial_until') ORDER BY sm.id DESC LIMIT 1 ",
				$subscription_id
			)
		);

		$trial_end_date = $trial_end_meta ?? ( ( 'trial' === $row['status'] || 'trialing' === $row['status'] ) ? $row['next_payment_date'] : null );
		$uid            = (int) $row['user_id'];
		$personal_data  = get_user_meta( $uid, 'masterstudy_personal_data', true );
		$personal_data  = is_array( $personal_data ) ? $personal_data : array();

		$plan_type_raw    = (string) ( $row['plan_type'] ?? '' );
		$plan_type_labels = masterstudy_lms_get_subscription_types_labels();

		$student = array(
			'user_id'      => $uid,
			'name'         => (string) ( $row['user_name'] ?? '' ),
			'email'        => (string) ( $row['user_email'] ?? '' ),
			'country'      => (string) ( $personal_data['country'] ?? get_user_meta( $uid, 'billing_country', true ) ),
			'postcode'     => (string) ( $personal_data['post_code'] ?? get_user_meta( $uid, 'billing_postcode', true ) ),
			'state'        => (string) ( $personal_data['state'] ?? get_user_meta( $uid, 'billing_state', true ) ),
			'city'         => (string) ( $personal_data['city'] ?? get_user_meta( $uid, 'billing_city', true ) ),
			'company'      => (string) ( $personal_data['company'] ?? get_user_meta( $uid, 'billing_company', true ) ),
			'phone_number' => (string) ( $personal_data['phone'] ?? get_user_meta( $uid, 'billing_phone', true ) ),
		);

		$subscription = array(
			'subscription_id'         => (string) $row['subscription_id'],
			'user_id'                 => $uid,
			'plan_id'                 => (int) $row['plan_id'] ?? 0,
			'is_enabled'              => (bool) $row['is_enabled'] ?? 0,
			'type'                    => $plan_type_labels[ $plan_type_raw ] ?? ucwords( str_replace( '_', ' ', $plan_type_raw ) ),
			'renew'                   => masterstudy_lms_display_price_with_taxes( (float) $row['plan_price'], $uid ) . '/' . $row['plan_interval'],
			'sale_price'              => $row['sale_price'] ?? null,
			'sale_price_from'         => $row['sale_price_from'] ?? null,
			'sale_price_to'           => $row['sale_price_to'] ?? null,
			'payment'                 => 'recurring',
			'coupon'                  => $coupon ?? null,
			'start_date'              => $row['start_date'] ?? null,
			'trial_end_date'          => $trial_end_date ?? null,
			'next_payment_date'       => $row['next_payment_date'] ?? null,
			'end_date'                => $row['end_date'] ?? null,
			'status'                  => $row['status'],
			'text'                    => $row['note'],
			'plan_name'               => $row['plan_name'],
			'subs_for_course_enabled' => true,
			'plan_recurring_value'    => $row['plan_recurring_value'],
			'plan_billing_cycles'     => $row['plan_billing_cycles'],
		);

		if ( ! empty( $subscription['sale_price'] ) ) {
			if ( ! empty( $subscription['sale_price_from'] ) && ! empty( $subscription['sale_price_to'] ) ) {
				$sale_price_from = strtotime( $subscription['sale_price_from'] );
				$sale_price_to   = strtotime( $subscription['sale_price_to'] );
				$offset          = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
				$current_time    = time() + $offset;

				if ( $current_time >= $sale_price_from && $current_time <= $sale_price_to ) {
					$subscription['renew'] = masterstudy_lms_display_price_with_taxes( (float) $subscription['sale_price'], $uid ) . '/' . $row['plan_interval'];
				}
			} else {
				$subscription['renew'] = masterstudy_lms_display_price_with_taxes( (float) $subscription['sale_price'], $uid ) . '/' . $row['plan_interval'];
			}
		}

		// If subscription is trialing and coupon exists on first order, show discounted renew.
		if ( 'trialing' === strtolower( (string) $row['status'] ) ) {
			$first_order_id  = isset( $row['first_order_id'] ) ? (int) $row['first_order_id'] : 0;
			$active_order_id = isset( $row['active_order_id'] ) ? (int) $row['active_order_id'] : 0;

			$order_id_for_coupon = $first_order_id > 0 ? $first_order_id : $active_order_id;

			if ( $order_id_for_coupon > 0 ) {
				$coupon_id    = get_post_meta( $order_id_for_coupon, 'coupon_id', true );
				$coupon_type  = get_post_meta( $order_id_for_coupon, 'coupon_type', true );
				$coupon_value = get_post_meta( $order_id_for_coupon, 'coupon_value', true );

				$has_coupon = ( '' !== $coupon_id && null !== $coupon_id );

				if ( $has_coupon ) {
					$plan_price = (float) ( $row['plan_price'] ?? 0 );
					$interval   = (string) ( $row['plan_interval'] ?? '' );

					// Apply sale price first (if it was active) as the base for coupon.
					if ( ! empty( $subscription['sale_price'] ) ) {
						$plan_price = (float) $subscription['sale_price'];
					}

					if ( $plan_price > 0 ) {
						$coupon_type  = strtolower( (string) $coupon_type );
						$coupon_value = (float) $coupon_value;

						$discounted_price = $plan_price;

						if ( 'percent' === $coupon_type ) {
							$discounted_price = $plan_price * ( 1 - ( $coupon_value / 100 ) );
						} elseif ( 'amount' === $coupon_type ) {
							$discounted_price = $plan_price - $coupon_value;
						}

						if ( $discounted_price < 0 ) {
							$discounted_price = 0;
						}

						$subscription['renew'] = masterstudy_lms_display_price_with_taxes( $discounted_price, $uid );

						if ( ! empty( $interval ) ) {
							$subscription['renew'] .= '/' . $interval;
						}

						// Optional: expose coupon details for UI.
						$subscription['coupon_details'] = array(
							'id'    => (int) $coupon_id,
							'type'  => $coupon_type,
							'value' => $coupon_value,
						);
					}
				}
			}
		}

		if ( 'course' === $plan_type_raw ) {
			$course_id = (int) $this->db->get_var(
				$this->db->prepare(
					"SELECT object_id
					FROM {$plan_items_table}
					WHERE plan_id = %d AND object_type = %s
					ORDER BY id ASC
					LIMIT 1",
					(int) $row['plan_id'],
					'course'
				)
			);

			if ( $course_id ) {
				$course_title = get_the_title( $course_id );
				$course_url   = get_permalink( $course_id );

				if ( $course_title ) {
					$subscription['course'] = array(
						'id'    => $course_id,
						'title' => $course_title,
						'url'   => $course_url,
					);
				}

				$subscription['subs_for_course_enabled'] = (bool) get_post_meta( $course_id, 'subscriptions', true );
			}
		}

		return array(
			'subscription' => $subscription,
			'student'      => $student,
		);
	}

	/**
	 * Payment history for one subscription
	 */
	public function get_payment_history( int $subscription_id ): array {
		$subscription = $this->get( $subscription_id );
		if ( empty( $subscription ) ) {
			return array();
		}

		$payments = array();

		if ( 'trial' === $subscription['status'] ) {
			$payments[] = array(
				'id'             => '#' . (string) $subscription_id,
				'amount'         => 0.0,
				'payment_method' => 'Free Trial',
				'date'           => $subscription['start_date'],
				'status'         => 'TRIAL',
				'order_id'       => null,
			);
		}

		// Fetch ALL subscription orders (not only first and active) to build a complete payment history
		$orders_query = new \WP_Query(
			array(
				'post_type'      => 'stm-orders',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'is_subscription',
						'value'   => '1',
						'compare' => '=',
					),
					array(
						'key'     => 'subscription_id',
						'value'   => (int) $subscription_id,
						'compare' => '=',
					),
				),
				'orderby'        => 'post_date',
				'order'          => 'ASC',
			)
		);

		if ( $orders_query->have_posts() ) {
			foreach ( $orders_query->posts as $order_post ) {
				$order_id = $order_post->ID;

				$order_total               = get_post_meta( $order_id, '_order_total', true );
				$order_subtotal            = get_post_meta( $order_id, '_order_subtotal', true );
				$order_taxes               = get_post_meta( $order_id, '_order_taxes', true );
				$order_status              = get_post_meta( $order_id, 'status', true );
				$payment_code              = get_post_meta( $order_id, 'payment_code', true );
				$order_date                = get_post_meta( $order_id, 'date', true );
				$subscription_order_number = get_post_meta( $order_id, 'subscription_order_number', true );
				$gateway_invoice_id        = get_post_meta( $order_id, 'gateway_invoice_id', true );
				$coupon_id                 = get_post_meta( $order_id, 'coupon_id', true );
				$coupon_type               = get_post_meta( $order_id, 'coupon_type', true );
				$coupon_value              = get_post_meta( $order_id, 'coupon_value', true );

				$coupon = null;

				if ( '' !== $coupon_id && null !== $coupon_id ) {
					$coupon = array(
						'id'    => (int) $coupon_id,
						'type'  => ! empty( $coupon_type ) ? strtolower( (string) $coupon_type ) : '',
						'value' => (float) $coupon_value,
						'formatted_value' => '-' . ( 'amount' === $coupon_type ? STM_LMS_Helpers::display_price( (float) $coupon_value ) : $coupon_value . '%' ),
					);
				}

				$date = null;
				if ( ! empty( $order_date ) ) {
					if ( is_numeric( $order_date ) ) {
						$date = date( 'Y-m-d H:i:s', $order_date );
					} else {
						$date = $order_date;
					}
				}


				if ( empty( $date ) ) {
					$date = $order_post->post_date;
				}

				$payments[] = array(
					'id'                 => '#' . $order_id,
					'total'              => (float) $order_total,
					'subtotal'           => (float) $order_subtotal,
					'taxes'              => (float) $order_taxes,
					'payment_method'     => ! empty( $payment_code ) ? $payment_code : 'Unknown',
					'date'               => $date,
					'status'             => ! empty( $order_status ) ? strtoupper( $order_status ) : 'PAID',
					'order_id'           => $order_id,
					'order_number'       => ! empty( $subscription_order_number ) ? (int) $subscription_order_number : null,
					'gateway_invoice_id' => $gateway_invoice_id,
					'coupon'             => $coupon,
				);
			}
		}

		$data = array_map(
			static function ( $p ) {
				$id = 0;
				if ( isset( $p['order_id'] ) ) {
					$id = (int) $p['order_id'];
				} elseif ( isset( $p['id'] ) ) {
					$id = (int) preg_replace( '/[^0-9]/', '', (string) $p['id'] );
				}

				$method = isset( $p['payment_method'] ) ? strtolower( (string) $p['payment_method'] ) : '';

				$ts = 0;
				if ( ! empty( $p['date'] ) ) {
					$ts = (int) strtotime( (string) $p['date'] );
					if ( $ts < 0 ) {
						$ts = 0;
					}
				}

				$status = isset( $p['status'] ) ? strtolower( (string) $p['status'] ) : '';

				return array(
					'id'                 => $id,
					'total'              => $p['total'] ?? 0.0,
					'subtotal'           => $p['subtotal'] ?? 0.0,
					'taxes'              => $p['taxes'] ?? 0.0,
					'payment_method'     => $method,
					'date'               => $ts,
					'status'             => $status,
					'order_id'           => $p['order_id'],
					'order_number'       => $p['order_number'] ?? null,
					'gateway_invoice_id' => $p['gateway_invoice_id'] ?? null,
					'coupon'             => $p['coupon'] ?? null,
				);
			},
			is_array( $payments ) ? $payments : array()
		);

		// If coupon is attached to a $0 "trial/zero" order, move it to the next paid order.
		$pending_coupon = null;

		foreach ( $data as $i => $row ) {
			$has_coupon = ! empty( $row['coupon'] ) && ! empty( $row['coupon']['id'] );
			$is_zero    = ( (float) $row['total'] <= 0 ) && ( (float) $row['subtotal'] <= 0 );

			if ( $has_coupon && $is_zero ) {
				$pending_coupon       = $row['coupon'];
				$data[ $i ]['coupon'] = null;
				continue;
			}

			if ( $pending_coupon && (float) $row['total'] > 0 && empty( $row['coupon'] ) ) {
				$data[ $i ]['coupon'] = $pending_coupon;
				$pending_coupon       = null;
				break;
			}
		}

		return $data;
	}

	public function get_subscription_order_query_by_gateway_invoice_id( string $gateway_invoice_id, string $fields = 'ids', int $limit = 1 ): \WP_Query {
		return new \WP_Query(
			array(
				'post_type'  => PostType::ORDER,
				'meta_query' => array(
					array(
						'key'     => 'gateway_invoice_id',
						'value'   => $gateway_invoice_id,
						'compare' => '=',
					),
				),
				'fields'     => $fields,
				'per_page'   => $limit,
			)
		);
	}

	public function get_subscription_orders_query( int $subscription_id, string $fields = 'ids', int $limit = 1, string $status = '' ): \WP_Query {
		$args = array(
			'post_type'  => PostType::ORDER,
			'meta_query' => array(
				array(
					'key'     => 'subscription_id',
					'value'   => $subscription_id,
					'compare' => '=',
				),
			),
			'fields'     => $fields,
			'per_page'   => $limit,
		);

		if ( ! empty( $status ) ) {
			$args['meta_query'][] = array(
				'key'     => 'status',
				'value'   => $status,
				'compare' => '=',
			);
		}

		return new \WP_Query( $args );
	}

	public function update_related_order_status_by_gateway_invoice_id( string $gateway_invoice_id, string $status ): void {
		$order = $this->get_subscription_order_query_by_gateway_invoice_id( $gateway_invoice_id );
		if ( ! $order->have_posts() ) {
			return;
		}

		$order_id = $order->posts[0] ?? 0;

		update_post_meta( $order_id, 'status', $status );
	}

	public function get_user_subscriptions_with_plans( int $user_id ): array {
		$plans_table = stm_lms_subscription_plans_table_name( $this->db );

		$sql = "SELECT s.*, p.type AS plan_type, p.name AS plan_name
			FROM {$this->table_name()} s
			INNER JOIN {$plans_table} p ON p.id = s.plan_id
			WHERE s.user_id = %d";

		$results = $this->db->get_results( $this->db->prepare( $sql, $user_id ), ARRAY_A );

		return is_array( $results ) ? $results : array();
	}

	/**
	 * Get subscriptions that should be expired by date
	 */
	public function get_subscriptions_to_expire(): array {
		$sql = "SELECT * FROM {$this->table_name()} 
			WHERE status IN ('active', 'trialing') 
			AND end_date IS NOT NULL 
			AND end_date <= NOW()";

		$results = $this->db->get_results( $sql, ARRAY_A );

		return is_array( $results ) ? $results : array();
	}

	/**
	 * Get subscriptions that expire soon (within 7 days)
	 */
	public function get_subscriptions_expiring_soon(): array {
		$sql = "SELECT * FROM {$this->table_name()} 
			WHERE status IN ('active', 'trialing') 
			AND end_date IS NOT NULL 
			AND end_date > NOW() 
			AND end_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)";

		$results = $this->db->get_results( $sql, ARRAY_A );

		return is_array( $results ) ? $results : array();
	}
}
