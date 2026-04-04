<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Helpers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\media_library\Enums\OrientationType;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class PexelsHelper {

	/**
	 * @param WP_REST_Request $request
	 * @param $per_page
	 *
	 * @return mixed
	 */
	public static function validate_params( WP_REST_Request $request, $per_page ) {
		$validator = new Validator(
			$request->get_params(),
			array(
				'page'        => 'integer|nullable',
				'query'       => 'string|nullable',
				'per_page'    => 'integer|nullable',
				'orientation' => 'nullable|contains_list,' . implode( ';', OrientationType::cases() ),
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_params = $validator->get_validated();

		$validated_params['per_page'] = $validated_params['per_page'] ?? $per_page;

		if ( ! empty( $validated_params['orientation'] ) ) {
			$validated_params['orientation'] = self::get_orientation_type( $validated_params['orientation'] );
		}

		return $validated_params;
	}

	/**
	 * Converts request orientation type to type supported by pixabay
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_orientation_type( $type ) {
		switch ( $type ) {
			case OrientationType::LANDSCAPE:
				return 'landscape';
			case OrientationType::PORTRAIT:
				return 'portrait';
			case OrientationType::SQUARE:
				return 'square';
			default:
				return '';
		}
	}
}
