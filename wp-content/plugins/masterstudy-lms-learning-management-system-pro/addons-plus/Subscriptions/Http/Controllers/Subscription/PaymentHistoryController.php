<?php
/**
 * Payment history (single subscription)
 *
 * @package MasterStudy\Lms\Pro\AddonsPlus\Subscriptions
 */

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Controllers\Subscription;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Http\Serializers\PaymentHistorySerializer;

class PaymentHistoryController {

	public function __invoke( \WP_REST_Request $request ) {
		$subscription_id = (int) $request->get_param( 'subscription_id' );
		$repository      = new SubscriptionRepository();

		if ( ! $repository->find( $subscription_id ) ) {
			return WpResponseFactory::not_found();
		}

		$start      = (int) $request->get_param( 'start' ) ?? 0;
		$length     = (int) $request->get_param( 'length' ) ?? 10;
		$sort_param = $request->get_param( 'sort' ) ?? null;

		$all_payments  = $repository->get_payment_history( $subscription_id );
		$total_records = count( $all_payments );

		if ( ! empty( $sort_param ) ) {
			$sort_parts     = explode( ':', $sort_param );
			$sort_field     = $sort_parts[0] ?? 'date';
			$sort_direction = $sort_parts[1] ?? 'desc';

			usort(
				$all_payments,
				function ( $a, $b ) use ( $sort_field, $sort_direction ) {
					$value_a = $a[ $sort_field ] ?? 0;
					$value_b = $b[ $sort_field ] ?? 0;

					if ( is_numeric( $value_a ) && is_numeric( $value_b ) ) {
						$result = $value_a <=> $value_b;
					} else {
						$result = strcmp( (string) $value_a, (string) $value_b );
					}

					return 'desc' === strtolower( $sort_direction ) ? - $result : $result;
				}
			);
		}

		$paginated_payments = array_slice( $all_payments, $start, $length );

		return new \WP_REST_Response(
			array(
				'data'            => ( new PaymentHistorySerializer() )->collectionToArray( $paginated_payments ),
				'recordsTotal'    => $total_records,
				'recordsFiltered' => $total_records,
				'start'           => $start,
				'length'          => $length,
			)
		);
	}
}
