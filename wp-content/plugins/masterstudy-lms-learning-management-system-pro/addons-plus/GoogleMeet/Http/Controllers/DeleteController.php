<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Controllers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Repositories\GoogleMeetRepository;

final class DeleteController {
	public function __invoke( int $meeting_id ) {
		$repository = new GoogleMeetRepository();

		if ( ! $repository->exists( $meeting_id ) ) {
			return WpResponseFactory::not_found();
		}

		$repository->delete( $meeting_id );

		return WpResponseFactory::ok();
	}
}
