<?php

namespace MasterStudy\Lms\Http\Controllers\Certificates;

use WP_REST_Request;
use MasterStudy\Lms\Pro\addons\certificate_builder\CertificateRepository;

class GetCertificatesController {
	public function __invoke( WP_REST_Request $request ): \WP_REST_Response {
		return new \WP_REST_Response(
			wp_json_encode( ( new CertificateRepository() )->get_all() )
		);
	}
}
