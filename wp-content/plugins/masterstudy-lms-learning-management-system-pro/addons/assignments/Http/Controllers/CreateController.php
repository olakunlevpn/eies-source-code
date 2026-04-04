<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Http\Controllers;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\FileMaterialRepository;
use MasterStudy\Lms\Validation\Validator;

final class CreateController {

	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'attempts'           => 'nullable|integer',
				'passing_grade'      => 'nullable|integer',
				'content'            => 'required|string',
				'title'              => 'required|string',
				'files'              => 'array',
				'time_limit_unit'    => 'nullable|string',
				'time_limit'         => 'nullable|float',
				'retake_limit_reset' => 'nullable|boolean',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$repo = new AssignmentRepository();
		$id   = $repo->create( $validator->get_validated() );

		if ( ! empty( $id ) ) {
			( new FileMaterialRepository() )->save_files( $validator->get_validated()['files'] ?? array(), $id, PostType::ASSIGNMENT );
		}

		do_action( 'masterstudy_lms_save_assignment', $id );

		return WpResponseFactory::created( array( 'id' => $id ) );
	}
}
