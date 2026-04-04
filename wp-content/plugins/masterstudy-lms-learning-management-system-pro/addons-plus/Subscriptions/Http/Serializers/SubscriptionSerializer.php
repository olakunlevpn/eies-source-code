<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class SubscriptionSerializer extends AbstractSerializer {
	public function toArray( $data ): array {

		$status  = (string) ( $data['status'] ?? '' );
		$auto_ts = isset( $data['autoRenew'] ) ? (int) $data['autoRenew'] : 0;
		$end_ts  = isset( $data['end_datee'] ) ? (int) $data['end_datee'] : 0;

		// Rule:
		// - expired/expiring => always show end_datee
		// - otherwise        => show autoRenew (no fallback)
		if ( in_array( $status, array( 'expired', 'expiring' ), true ) ) {
			$auto_renew_out = $end_ts ? date( 'Y-m-d H:i:s', $end_ts ) : null;
		} else {
			$auto_renew_out = $auto_ts ? date( 'Y-m-d H:i:s', $auto_ts ) : null;
		}

		return array(
			'id'                      => $data['id'] ?? 0,
			'user'                    => $data['user'] ?? '',
			'status'                  => $data['status'] ?? '',
			'total'                   => masterstudy_lms_display_price_with_taxes( (float) $data['amount'] ?? 0, $data['user_id'] ?? null ),
			'subtotal'                => \STM_LMS_Helpers::display_price( (float) $data['amount'] ?? 0 ),
			'taxes'                   => masterstudy_lms_display_taxes_amount( (float) $data['amount'] ?? 0, $data['user_id'] ?? null ),
			'plan'                    => $data['plan'] ?? '',
			'plan_id'                 => (int) $data['plan_id'] ?? 0,
			'interval'                => $data['recurring_interval'] ?? 'month',
			'type'                    => $data['type'] ?? 'subscription',
			'date'                    => (int) $data['date'] ? date( 'Y-m-d H:i:s', $data['date'] ) : null,
			'autoRenew'               => $auto_renew_out,
			'subs_for_course_enabled' => (bool) $data['subs_for_course_enabled'] ?? true,
			'is_enabled'              => (bool) $data['is_enabled'] ?? false,
			'is_latest'               => (bool) $data['is_latest'] ?? false,
			'course'                  => isset( $data['course'] ) ? $data['course'] : array(),
		);
	}
}
