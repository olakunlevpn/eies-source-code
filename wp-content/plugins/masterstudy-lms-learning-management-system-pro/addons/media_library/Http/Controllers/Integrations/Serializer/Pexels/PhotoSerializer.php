<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Pexels;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class PhotoSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'        => $data['id'],
			'type'      => 'pexels',
			'file_type' => 'image',
			'photos'    => array(
				'sm'       => $data['src']['medium'],
				'sd'       => $data['src']['large'],
				'hd'       => $data['src']['large2x'],
				'original' => $data['src']['original'],
			),
		);
	}
}
