<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Coupons\CouponSerializer;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Response;

final class ListController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validation = $this->validate_datatable( $request );

		if ( $validation instanceof WP_REST_Response ) {
			return $validation;
		}

		$validated_data = $this->get_validated_data();
		$date_from      = $this->get_date_from();
		$date_to        = $this->get_date_to();
		$search         = $validated_data['search']['value'] ?? null;
		$per_page       = $validated_data['length'] ?? 10;
		$coupon_status  = $validated_data['status'] ?? null;
		$page           = ( $validated_data['start'] ?? 0 ) / $per_page + 1;
		$order          = array_key_exists( 'order', $validated_data ) ? $validated_data['order'] : null;
		$columns        = $validated_data['columns'] ?? array();
		$filters        = array();

		if ( $search ) {
			$filters['search'] = $search;
		}

		if ( $coupon_status ) {
			$filters['status'] = $coupon_status;
		}

		if ( ! empty( $columns ) && ! empty( $order ) ) {
			$order  = reset( $order );
			$column = $order['column'] ?? 0;
			$dir    = $order['dir'] ?? 'asc';

			if ( ! empty( $columns[ $column ]['data'] ) && 'number' !== $columns[ $column ]['data'] ) {
				$filters['sort'] = $columns[ $column ]['data'] . ' ' . $dir;
			}
		}

		$coupons = ( new CouponRepository() )->list(
			$filters,
			$date_from,
			$date_to,
			$page,
			$per_page
		);

		return new WP_REST_Response(
			array(
				'data'            => ( new CouponSerializer() )->collectionToArray( $coupons['coupons'] ),
				'recordsTotal'    => $coupons['total'],
				'recordsFiltered' => $coupons['total'],
			)
		);
	}
}
