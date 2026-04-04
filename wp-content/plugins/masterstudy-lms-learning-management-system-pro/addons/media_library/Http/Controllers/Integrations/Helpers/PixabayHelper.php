<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Helpers;

use MasterStudy\Lms\Http\WpResponseFactory;
use MasterStudy\Lms\Pro\addons\media_library\Enums\OrientationType;
use MasterStudy\Lms\Validation\Validator;
use WP_REST_Request;

class PixabayHelper {

	/**
	 * @param WP_REST_Request $request
	 * @param $api_key
	 * @param $per_page
	 *
	 * @return mixed
	 */
	public static function validate_params( WP_REST_Request $request, $api_key, $per_page ) {
		$validator = new Validator(
			array(
				'page'        => $request->get_param( 'page' ) ?? 1,
				'q'           => $request->get_param( 'query' ) ?? '',
				'per_page'    => $request->get_param( 'per_page' ) ?? $per_page,
				'orientation' => $request->get_param( 'orientation' ) ?? '',
			),
			array(
				'page'        => 'integer|nullable',
				'q'           => 'string|nullable',
				'per_page'    => 'integer|nullable',
				'orientation' => 'nullable|contains_list,' . implode( ';', OrientationType::cases() ),
			)
		);

		if ( $validator->fails() ) {
			return WpResponseFactory::validation_failed( $validator->get_errors_array() );
		}

		$validated_params = $validator->get_validated();

		$validated_params['key'] = $api_key ?? '';

		if ( ! empty( $validated_params['orientation'] ) ) {
			$validated_params['orientation'] = self::get_orientation_type( $validated_params['orientation'] );
		}

		return $validated_params;
	}

	/**
	 * Converts request orientation type to type supported by pixabay
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_orientation_type( $type ) {
		switch ( $type ) {
			case OrientationType::LANDSCAPE:
				return 'horizontal';
			case OrientationType::PORTRAIT:
				return 'vertical';
			default:
				return 'all';
		}
	}
}
