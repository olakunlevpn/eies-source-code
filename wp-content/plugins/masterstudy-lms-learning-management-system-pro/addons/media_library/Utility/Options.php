<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Utility;

final class Options {
	/**
	 * @return array
	 */
	public static function get_settings(): array {
		return get_option( 'stm_lms_media_library_settings', array() );
	}
}
