<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Controllers;

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Http\Serializers\GradeSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Repositories\GradesRepository;
use MasterStudy\Lms\Pro\RestApi\Http\Controllers\Analytics\Controller;
use WP_REST_Request;
use WP_REST_Response;

class GetGradesController extends Controller {
	public function __invoke( WP_REST_Request $request ): WP_REST_Response {
		$validation = $this->validate_datatable(
			$request
		);

		if ( $validation instanceof WP_REST_Response ) {
			return $validation;
		}

		$validated_data = $this->get_validated_data();

		$grades_repository = new GradesRepository(
			$this->get_date_from(),
			$this->get_date_to(),
			$validated_data['start'] ?? 1,
			$validated_data['length'] ?? 10,
			$validated_data['search']['value'] ?? array()
		);

		$grades       = $grades_repository->get_grades(
			$validated_data['columns'] ?? array(),
			$validated_data['order'] ?? array()
		);
		$grades_count = $grades_repository->get_total_grades();

		return new WP_REST_Response(
			array(
				'recordsTotal'    => $grades_count,
				'recordsFiltered' => $grades_count,
				'data'            => ( new GradeSerializer() )->collectionToArray( $grades ),
			)
		);
	}
}
