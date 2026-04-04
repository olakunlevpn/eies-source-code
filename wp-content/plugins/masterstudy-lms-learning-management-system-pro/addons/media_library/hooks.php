<?php

use MasterStudy\Lms\Plugin\Addons;
use MasterStudy\Lms\Pro\addons\media_library\Utility\IntegrationOptions;
use MasterStudy\Lms\Pro\addons\media_library\MediaStorage;

add_filter(
	'masterstudy_lms_course_options',
	function ( $options ) {
		$options[ Addons::MEDIA_LIBRARY ] = array(
			'allowed_extensions' => MediaStorage::allowed_extensions(),
			'max_upload_size'    => MediaStorage::max_upload_size(),
		);

		if ( STM_LMS_Helpers::is_pro_plus() ) {
			$options[ Addons::MEDIA_LIBRARY ]['integrations'] = IntegrationOptions::get_integration_options();
		}

		return $options;
	}
);

add_filter(
	'wpcfto_field_media-integration-cache',
	function () {
		return STM_LMS_PRO_ADDONS . '/media_library/templates/media-integration-cache.php';
	}
);
