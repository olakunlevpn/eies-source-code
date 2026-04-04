<?php

namespace MasterStudy\Lms\Http\Controllers\Certificates;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\certificate_builder\CertificateRepository;

class DeleteCertificateController {
	public function __invoke( int $certificate_id ) {
		$repo = new CertificateRepository();

		if ( ! $repo->exists( $certificate_id ) ) {
			return WpResponseFactory::not_found();
		}

		$repo->delete( $certificate_id );

		return WpResponseFactory::ok();
	}
}
