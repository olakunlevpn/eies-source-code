<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Unsplash;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class PhotoSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'        => $data['id'],
			'type'      => 'unsplash',
			'file_type' => 'image',
			'photos'    => array(
				'sm'       => $data['urls']['small'],
				'sd'       => $data['urls']['regular'],
				'hd'       => $data['urls']['full'],
				'original' => $data['urls']['raw'],
			),
		);
	}
}
