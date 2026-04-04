<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Serializers\MeetingsListSerializer;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Repositories\GoogleMeetRepository;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

final class ListController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		$validator = new Validator(
			$request->get_params(),
			array(
				'start'   => 'required|integer',
				'length'  => 'required|integer',
				'order'   => 'array',
				'columns' => 'array',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();
		$per_page       = $validated_data['length'] ?? 10;
		$page           = ( $validated_data['start'] ?? 0 ) / $per_page + 1;

		$meetings_result = ( new GoogleMeetRepository() )->get_list( get_current_user_id(), $page, $per_page );

		return new \WP_REST_Response(
			array(
				'data'            => ( new MeetingsListSerializer() )->collectionToArray( $meetings_result['data'] ),
				'recordsTotal'    => intval( $meetings_result['total'] ),
				'recordsFiltered' => intval( $meetings_result['total'] ),
			)
		);
	}
}
