<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Bundle;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\Bundle\CourseBundlesSerializer;
use MasterStudy\Lms\Pro\RestApi\Repositories\BundlesRepository;
use WP_REST_Request;
use WP_REST_Response;

final class GetCourseBundlesController extends Controller {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		if ( ! is_ms_lms_addon_enabled( 'course_bundle' ) ) {
			return WpResponseFactory::not_found();
		}
		$validation = $this->validate_datatable(
			$request,
			array(
				'bundle_id' => 'integer',
			)
		);

		if ( $validation instanceof WP_REST_Response ) {
			return $validation;
		}

		$validated_data = $this->get_validated_data();

		$bundles_provider = new BundlesRepository( $this->get_date_from(), $this->get_date_to() );
		$result           = $bundles_provider->get_bundles_by_course_id( $validated_data['bundle_id'], $validated_data['columns'], $validated_data['order'], $validated_data['search']['value'] ?? null );

		return new WP_REST_Response(
			array(
				'recordsTotal'    => count( $result ),
				'recordsFiltered' => count( $result ),
				'data'            => ( new CourseBundlesSerializer() )->collectionToArray( $result ),
			)
		);
	}
}
