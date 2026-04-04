<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Repositories\GoogleMeetRepository;
use MasterStudy\Lms\Validation\Validator;

final class CreateController {
	public function __invoke( \WP_REST_Request $request ) {
		if ( ! masterstudy_lms_google_meet_config_passed() ) {
			return WpResponseFactory::error(
				__( 'Complete the Google Meet configuration before creating the lesson', 'masterstudy-lms-learning-management-system-pro' ),
			);
		}

		$validator = new Validator(
			$request->get_params(),
			array(
				'title'      => 'required|string',
				'course_id'  => 'nullable|integer',
				'summary'    => 'string',
				'start_date' => 'required|integer',
				'start_time' => 'required|time',
				'end_date'   => 'required|integer',
				'end_time'   => 'required|time',
				'timezone'   => 'required|string',
				'attendees'  => 'boolean',
				'visibility' => 'required|string|contains_list,' . implode( ';', array_keys( masterstudy_lms_google_meet_visibility_types() ) ),
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$repository = new GoogleMeetRepository();
		$data       = $validator->get_validated();
		$meeting_id = $repository->create( $data );

		$repository->save_google_event( $meeting_id, $data );

		return WpResponseFactory::created(
			array( 'id' => $meeting_id )
		);
	}
}
