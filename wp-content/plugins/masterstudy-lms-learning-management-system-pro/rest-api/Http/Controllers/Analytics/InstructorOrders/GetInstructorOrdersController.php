<?php

namespace MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\InstructorOrders;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use MasterStudy\Lms\Pro\RestApi\Http\Serializers\InstructorOrders\InstructorOrdersSerializer;
use MasterStudy\Lms\Pro\RestApi\Repositories\Instructor\EnrollmentRepository;
use STM_LMS_Instructor;
use WP_REST_Request;
use WP_REST_Response;

final class GetInstructorOrdersController extends Controller {

	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$instructor_id = get_current_user_id();

		if ( ! STM_LMS_Instructor::is_instructor( $instructor_id ) ) {
			return WpResponseFactory::not_found();
		}

		$validation = $this->validate_datatable( $request );

		if ( $validation instanceof WP_REST_Response ) {
			return $validation;
		}

		$validated_data        = $this->get_validated_data();
		$orders_provider       = $this->get_checkout_provider()->get_provider( 'orders' );
		$enrollment_repository = new EnrollmentRepository(
			$instructor_id,
			$this->get_date_from(),
			$this->get_date_to()
		);

		$orders_result = ( new $orders_provider(
			$this->get_date_from(),
			$this->get_date_to()
		) )->get_detailed_instructor_orders(
			$enrollment_repository->get_instructor_course_ids_for_orders(),
			$validated_data['search']['value'] ?? null,
			$validated_data['length'] ?? 10,
			$validated_data['start'] ?? 0,
			$validated_data['columns'] ?? array(),
			array_key_exists( 'order', $validated_data ) ? $validated_data['order'] : null
		);

		return new WP_REST_Response(
			array(
				'recordsTotal'    => intval( $orders_result['total_orders'] ),
				'recordsFiltered' => intval( $orders_result['total_orders'] ),
				'data'            => ( new InstructorOrdersSerializer() )->collectionToArray( $orders_result['detailed_orders'] ),
			)
		);
	}
}
