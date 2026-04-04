<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories;

class SubscriptionMetaRepository extends AbstractRepository {
	public function table_name(): string {
		return stm_lms_subscription_meta_table_name( $this->db );
	}

	public function get_fields_format(): array {
		return array(
			'%d', // subscription_id
			'%s', // meta_key
			'%s', // meta_value
		);
	}

	public function get_fields_values( array $data ): array {
		return array(
			'subscription_id' => $data['subscription_id'],
			'meta_key'        => $data['meta_key'],
			'meta_value'      => $data['meta_value'],
		);
	}

	public function create( int $subscription_id, string $key, $value ): bool {
		return $this->db->insert(
			$this->table_name(),
			$this->get_fields_values(
				array(
					'subscription_id' => $subscription_id,
					'meta_key'        => $key,
					'meta_value'      => $value,
				)
			),
			$this->get_fields_format()
		);
	}

	public function update( int $subscription_id, string $key, $value ): bool {
		return $this->db->update(
			$this->table_name(),
			array( 'meta_value' => $value ),
			array(
				'subscription_id' => $subscription_id,
				'meta_key'        => $key,
			),
			array( '%s' ),
			array( '%d', '%s' )
		);
	}

	public function get( int $subscription_id, string $key ) {
		return $this->db->get_var(
			$this->db->prepare(
				"SELECT meta_value FROM {$this->table_name()} WHERE subscription_id = %d AND meta_key = %s",
				$subscription_id,
				$key
			)
		);
	}

	public function get_all( int $subscription_id ): array {
		$results = $this->db->get_results(
			$this->db->prepare(
				"SELECT meta_key, meta_value FROM {$this->table_name()} WHERE subscription_id = %d",
				$subscription_id
			),
			ARRAY_A
		);

		$meta = array();
		foreach ( $results as $row ) {
			$meta[ $row['meta_key'] ] = $row['meta_value'];
		}

		return $meta;
	}

	public function delete( int $subscription_id, string $key ): bool {
		return $this->db->delete(
			$this->table_name(),
			array(
				'subscription_id' => $subscription_id,
				'meta_key'        => $key,
			),
			array( '%d', '%s' )
		);
	}

	public function delete_all( int $subscription_id ): bool {
		return $this->db->delete(
			$this->table_name(),
			array( 'subscription_id' => $subscription_id ),
			array( '%d' )
		);
	}
}
