<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\CourseBuilder;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Controllers\Controller;
use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers\ImageSerializer;
use MasterStudy\Lms\Utility\Media;
use MasterStudy\Lms\Validation\Validator;

class UploadImageController extends Controller {
	public function __invoke( \WP_REST_Request $request ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'image' => 'required',
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_data = $validator->get_validated();

		$image = Media::create_attachment_from_url( $validated_data['image'] );

		if ( isset( $image['error'] ) ) {
			return WpResponseFactory::error( $image['error'] );
		}

		return new \WP_REST_Response(
			( new ImageSerializer() )->toArray( $image )
		);
	}
}
