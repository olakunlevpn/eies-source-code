<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class SubscriptionDetailsSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return array();
		}

		$status = isset( $data['subscription']['status'] ) ? (string) $data['subscription']['status'] : '';

		// Normalize status
		if ( 'trialing' === strtolower( $status ) ) {
			$status = 'trial';
		}

		$array = array(
			'id'                  => (string) $data['subscription']['subscription_id'] ?? '',
			'membership_access'   => (string) $data['subscription']['type'] ?? '',
			'renew'               => (string) $data['subscription']['renew'] ?? '',
			'payment_type'        => (string) $data['subscription']['payment'] ?? '',
			'coupon'              => $data['subscription']['coupon'] ?? null,
			'trial_end_date'      => is_string( $data['subscription']['trial_end_date'] ) ? strtotime( $data['subscription']['trial_end_date'] ) : $data['subscription']['trial_end_date'],
			'start_date'          => strtotime( $data['subscription']['start_date'] ) ?? null,
			'status'              => $status,
			'note'                => (string) $data['subscription']['text'] ?? '',
			'plan_name'           => (string) $data['subscription']['plan_name'] ?? '',
			'plan_billing_cycles' => (string) $data['subscription']['plan_billing_cycles'] ?? '',
			'student'             => $this->serialize_student(
				$data['student'] ?? array()
			),
		);

		if ( 'active' == strtolower( $status ) ) {
			$array['next_payment'] = strtotime( $data['subscription']['next_payment_date'] );
		}

		if ( 'active' !== strtolower( $status ) && 'trial' !== strtolower( $status ) ) {
			$array['end_date'] = strtotime( $data['subscription']['end_date'] );
		}

		return $array;
	}

	private function serialize_student( array $student ): array {
		return array(
			'name'         => (string) $student['name'] ?? '',
			'email'        => (string) $student['email'] ?? '',
			'country'      => (string) $student['country'] ?? '',
			'postcode'     => (string) $student['postcode'] ?? '',
			'state'        => (string) $student['state'] ?? '',
			'city'         => (string) $student['city'] ?? '',
			'company'      => (string) $student['company'] ?? '',
			'phone_number' => (string) $student['phone_number'] ?? '',
		);
	}
}
