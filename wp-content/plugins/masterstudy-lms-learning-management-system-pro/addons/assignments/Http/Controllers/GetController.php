<?php

namespace MasterStudy\Lms\Pro\addons\assignments\Http\Controllers;

use MasterStudy\Lms\Http\Serializers\CommentSerializer;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentRepository;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Repositories\FileMaterialRepository;

final class GetController {

	public function __invoke( int $assignment_id ) {
		$repo       = new AssignmentRepository();
		$assignment = $repo->get( $assignment_id );

		if ( null === $assignment ) {
			return WpResponseFactory::not_found();
		}

		$assignment_files = get_post_meta( $assignment_id, 'assignment_files', true );
		$files            = ( new FileMaterialRepository() )->get_files( $assignment_files, true );

		if ( is_array( $assignment ) ) {
			$assignment = (object) $assignment;
		}
		$assignment->files = $files;

		return new \WP_REST_Response(
			array(
				'assignment' => $assignment,
				'comments'   => ( new CommentSerializer() )->collectionToArray( get_comments( array( 'post_id' => $assignment_id ) ) ),
			)
		);
	}
}
