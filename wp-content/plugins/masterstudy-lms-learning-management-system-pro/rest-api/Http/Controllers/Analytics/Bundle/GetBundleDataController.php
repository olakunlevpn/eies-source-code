<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Bundle;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Bundle\BundleDataSerializer;
use WP_REST_Request;
use WP_REST_Response;
use STM_LMS_Cart;

final class GetBundleDataController extends Controller {
	public function __invoke( int $bundle_id, WP_REST_Request $request ): WP_REST_Response {
		if ( empty( $bundle_id ) || ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return WpResponseFactory::not_found();
		}

		$validation = $this->validate( $request );

		if ( $validation instanceof WP_REST_Response ) {
			return $validation;
		}

		$bundles_provider   = $this->get_checkout_provider()->get_provider( 'bundles' );
		$bundles_repository = ( new $bundles_provider(
			$this->get_date_from(),
			$this->get_date_to(),
			$validated_data['start'] ?? 1,
			$validated_data['length'] ?? 10,
			$validated_data['search']['value'] ?? null
		) );

		if ( STM_LMS_Cart::woocommerce_checkout_enabled() ) {
			$bundle_revenue_by_period = $bundles_repository->get_woocommerce_bundle_revenue_by_period( $bundle_id );
			$bundle_revenue_data      = $bundles_repository->get_woocommerce_bundle_revenue( $bundle_id );
		} else {
			$bundle_revenue_by_period = $bundles_repository->get_bundle_revenue_by_period( $bundle_id );
			$bundle_revenue_data      = $bundles_repository->get_bundle_revenue( $bundle_id );
		}

		return new WP_REST_Response(
			( new BundleDataSerializer() )->toArray(
				array_merge(
					$bundle_revenue_data,
					$bundle_revenue_by_period
				)
			)
		);
	}
}
