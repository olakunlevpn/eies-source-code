<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class InstructorSubscriptionsSerializer extends AbstractSerializer {
	public function toArray( $subscription ): array {
		return array(
			'subscription_id'   => $subscription['id'] ?? 0,
			'user_info'         => $subscription['user'] ?? '-',
			'plan_name'         => $subscription['plan'] ?? '-',
			'status'            => $subscription['status'] ?? '',
			'start_date'        => $subscription['date'] ? date( 'Y-m-d H:i:s', $subscription['date'] ) : null,
			'next_payment_date' => $subscription['autoRenew'] ? date( 'Y-m-d H:i:s', $subscription['autoRenew'] ) : null,
			'total'             => masterstudy_lms_display_price_with_taxes( (float) $subscription['amount'] ?? 0, $subscription['user_id'] ?? null ),
			'subtotal'          => \STM_LMS_Helpers::display_price( (float) $subscription['amount'] ?? 0 ),
			'taxes'             => masterstudy_lms_display_taxes_amount( (float) $subscription['amount'] ?? 0, $subscription['user_id'] ?? null ),
			'plan'              => $subscription['plan'] ?? '',
			'course'            => isset( $subscription['course'] ) ? $subscription['course'] : array(),
			'type'              => $subscription['type'] ?? 'subscription',
		);
	}
}
