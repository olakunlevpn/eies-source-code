<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories;

class SubscriptionPlanItemRepository extends AbstractRepository {
	public function table_name(): string {
		return stm_lms_subscription_plan_items_table_name( $this->db );
	}

	public function get_fields_format(): array {
		return array(
			'%d', // plan_id
			'%s', // object_type
			'%d', // object_id
		);
	}

	public function get_fields_values( array $data ): array {
		return array(
			'plan_id'     => $data['plan_id'],
			'object_type' => $data['object_type'],
			'object_id'   => $data['object_id'],
		);
	}

	public function create( int $plan_id, array $items ): bool {
		foreach ( $items as $item ) {
			$item['plan_id'] = $plan_id;
			$result          = $this->db->insert(
				$this->table_name(),
				$this->get_fields_values( $item ),
				$this->get_fields_format()
			);

			if ( false === $result ) {
				return false;
			}
		}

		return true;
	}

	public function update_plan_items( int $plan_id, array $items ): bool {
		$this->delete_by_plan_id( $plan_id );

		foreach ( $items as $item ) {
			$item['plan_id'] = $plan_id;
			$result          = $this->db->insert(
				$this->table_name(),
				$this->get_fields_values( $item ),
				$this->get_fields_format()
			);

			if ( false === $result ) {
				return false;
			}
		}

		return true;
	}

	public function get_by_plan_id( int $plan_id ): ?array {
		return $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM {$this->table_name()} WHERE plan_id = %d",
				$plan_id
			),
			ARRAY_A
		);
	}

	public function delete_by_plan_id( int $plan_id ): bool {
		return $this->db->delete(
			$this->table_name(),
			array( 'plan_id' => $plan_id ),
			array( '%d' )
		);
	}

	public function get_by_plan_ids( array $plan_ids ): array {
		if ( empty( $plan_ids ) ) {
			return array();
		}

		$placeholders = implode( ',', array_fill( 0, count( $plan_ids ), '%d' ) );
		$sql          = "SELECT plan_id, object_type, object_id FROM {$this->table_name()} WHERE plan_id IN ($placeholders)";
		$rows         = $this->db->get_results( $this->db->prepare( $sql, ...$plan_ids ), ARRAY_A );
		$result       = array();

		foreach ( $rows as $row ) {
			$pid              = (int) $row['plan_id'];
			$result[ $pid ][] = array(
				'object_type' => $row['object_type'],
				'object_id'   => (int) $row['object_id'],
			);
		}

		return $result;
	}
}
