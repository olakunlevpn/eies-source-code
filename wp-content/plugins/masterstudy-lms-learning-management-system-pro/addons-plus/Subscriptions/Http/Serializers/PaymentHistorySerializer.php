<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class PaymentHistorySerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		$order_id = (int) ( $data['order_id'] ?? 0 );
		$total    = (float) ( $data['total'] ?? 0 );
		$subtotal = (float) ( $data['subtotal'] ?? 0 );
		$taxes    = (float) ( $data['taxes'] ?? 0 );

		if ( $order_id && $taxes <= 0 ) {
			$this->maybe_recalculate_taxes_from_first_order( $order_id, $total, $subtotal, $taxes );
		}

		return array(
			'id'             => (int) ( $data['id'] ?? 0 ),
			'total'          => \STM_LMS_Helpers::display_price( $total ),
			'subtotal'       => \STM_LMS_Helpers::display_price( $subtotal ),
			'taxes'          => \STM_LMS_Helpers::display_price( $taxes ),
			'payment_method' => strtolower( (string) ( $data['payment_method'] ?? '' ) ),
			'date'           => ! empty( $data['date'] ) ? wp_date( 'Y-m-d H:i:s', (int) $data['date'] ) : null,
			'status'         => strtolower( (string) ( $data['status'] ?? '' ) ),
			'order_id'       => $order_id,
			'coupon'         => $data['coupon'] ?? null,
		);
	}

	private function maybe_recalculate_taxes_from_first_order( int $order_id, float &$total, float &$subtotal, float &$taxes ): void {
		static $rate_cache = array();

		$subscription_id = (int) get_post_meta( $order_id, 'subscription_id', true );
		if ( $subscription_id <= 0 ) {
			return;
		}

		if ( ! isset( $rate_cache[ $subscription_id ] ) ) {
			$rate_cache[ $subscription_id ] = 0.0;

			$repo  = new \MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository();
			$sub   = $repo->get( $subscription_id );
			$first = isset( $sub['first_order_id'] ) ? (int) $sub['first_order_id'] : 0;

			if ( $first > 0 ) {
				$first_total  = (float) get_post_meta( $first, '_order_total', true );
				$first_taxes  = (float) get_post_meta( $first, '_order_taxes', true );
				$first_subtot = (float) get_post_meta( $first, '_order_subtotal', true );

				$taxable_subtotal = 0.0;

				if ( $first_total > 0 && $first_taxes > 0 ) {
					$taxable_subtotal = $first_total - $first_taxes;
				}

				if ( $taxable_subtotal <= 0 && $first_subtot > 0 && $first_taxes > 0 ) {
					$taxable_subtotal = $first_subtot;
				}

				if ( $taxable_subtotal > 0 && $first_taxes > 0 ) {
					$rate_cache[ $subscription_id ] = $first_taxes / $taxable_subtotal;
				}
			}
		}

		$rate = (float) $rate_cache[ $subscription_id ];
		if ( $rate <= 0 ) {
			return;
		}

		$dec = (int) \STM_LMS_Options::get_option( 'decimals_num', 2 );

		if ( $total > 0 ) {
			$subtotal = round( $total / ( 1 + $rate ), $dec );
			$taxes    = round( $total - $subtotal, $dec );
		} elseif ( $subtotal > 0 ) {
			$taxes = round( $subtotal * $rate, $dec );
			$total = round( $subtotal + $taxes, $dec );
		}
	}

}
