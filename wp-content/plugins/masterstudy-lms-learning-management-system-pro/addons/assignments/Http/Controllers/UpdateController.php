<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Http\Controllers;

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\FileMaterialRepository;
use MasterStudy\Lms\Validation\Validator;

final class UpdateController {

	public function __invoke( int $assignment_id, \WP_REST_Request $request ) {
		$repo = new AssignmentRepository();

		if ( ! $repo->exists( $assignment_id ) ) {
			return WpResponseFactory::not_found();
		}

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

		$repo->update( $assignment_id, $validator->get_validated() );

		( new FileMaterialRepository() )->save_files( $validator->get_validated()['files'] ?? array(), $assignment_id, PostType::ASSIGNMENT );

		do_action( 'masterstudy_lms_save_assignment', $assignment_id );

		return WpResponseFactory::ok();
	}
}
