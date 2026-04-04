<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Utility;

use MasterStudy\Lms\Pro\addons\media_library\Enums\OrientationType;

final class IntegrationOptions {
	public const UNSPLASH_TYPES             = array( 'image' );
	public const UNSPLASH_ORIENTATION_TYPES = array( OrientationType::DEFAULT, OrientationType::LANDSCAPE, OrientationType::PORTRAIT, OrientationType::SQUARE );

	public const PEXELS_TYPES             = array( 'image', 'video' );
	public const PEXELS_ORIENTATION_TYPES = array( OrientationType::DEFAULT, OrientationType::LANDSCAPE, OrientationType::PORTRAIT, OrientationType::SQUARE );

	public const PIXABAY_TYPES             = array( 'image', 'video' );
	public const PIXABAY_ORIENTATION_TYPES = array( OrientationType::DEFAULT, OrientationType::LANDSCAPE, OrientationType::PORTRAIT );

	public static function get_integration_options() {
		$settings     = Options::get_settings();
		$use_unsplash = ! empty( $settings['use_unsplash'] ) && ! empty( $settings['unsplash_access_key'] );
		$use_pexels   = ! empty( $settings['use_pexels'] ) && ! empty( $settings['pexels_api_key'] );
		$use_pixabay  = ! empty( $settings['use_pixabay'] ) && ! empty( $settings['pixabay_api_key'] );

		return array(
			'unsplash' => array(
				'enabled'                 => $use_unsplash,
				'name'                    => 'Unsplash',
				'types'                   => self::UNSPLASH_TYPES,
				'orientation_types'       => self::UNSPLASH_ORIENTATION_TYPES,
				'only_search_orientation' => true,
			),
			'pexels'   => array(
				'enabled'                 => $use_pexels,
				'name'                    => 'Pexels',
				'types'                   => self::PEXELS_TYPES,
				'orientation_types'       => self::PEXELS_ORIENTATION_TYPES,
				'only_search_orientation' => true,
			),
			'pixabay'  => array(
				'enabled'                 => $use_pixabay,
				'name'                    => 'Pixabay',
				'types'                   => self::PIXABAY_TYPES,
				'orientation_types'       => self::PIXABAY_ORIENTATION_TYPES,
				'only_search_orientation' => false,
			),
		);
	}
}
