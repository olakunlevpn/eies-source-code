<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Repositories\GoogleMeetRepository;

final class GetController {
	public function __invoke( int $meeting_id ) {
		$meeting = ( new GoogleMeetRepository() )->get( $meeting_id );

		if ( null === $meeting ) {
			return WpResponseFactory::not_found();
		}

		return new \WP_REST_Response(
			array(
				'meeting' => $meeting,
			)
		);
	}
}
