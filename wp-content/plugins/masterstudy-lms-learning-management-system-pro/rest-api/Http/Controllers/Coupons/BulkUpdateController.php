<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Coupons;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Repositories\CouponRepository;
use MasterStudy\Lms\Pro\RestApi\Enums\Coupons\BulkCouponAction;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;
use WP_REST_Response;

final class BulkUpdateController {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'action'  => 'required|string|contains_list,' . implode( ';', BulkCouponAction::cases() ),
				'coupons' => 'required|array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$params     = $validator->get_validated();
		$repository = new CouponRepository();

		if ( BulkCouponAction::DELETE === $params['action'] ) {
			$result = $repository->bulk_delete_coupons( $params );
		} elseif ( BulkCouponAction::TRASH === $params['action'] ) {
			$result = $repository->bulk_status_trash( $params );
		} elseif ( BulkCouponAction::ACTIVE === $params['action'] ) {
			$result = $repository->bulk_status_active( $params );
		} elseif ( BulkCouponAction::INACTIVE === $params['action'] ) {
			$result = $repository->bulk_status_inactive( $params );
		} else {
			return WpResponseFactory::error( esc_html__( 'Only delete action is supported', 'masterstudy-lms-learning-management-system-pro' ) );
		}

		$success_ids = $result['success_ids'] ?? array();
		$failed      = $result['failed'] ?? array();

		$has_errors = ! empty( $failed );

		if ( $has_errors && empty( $success_ids ) ) {
			return WpResponseFactory::error(
				array(
					'message' => esc_html__( 'Unable to perform bulk action on selected coupons', 'masterstudy-lms-learning-management-system-pro' ),
					'failed'  => $failed,
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success'     => ! $has_errors,
				'message'     => esc_html__( 'Bulk action completed', 'masterstudy-lms-learning-management-system-pro' ),
				'success_ids' => $success_ids,
				'failed'      => $failed,
			)
		);
	}
}
