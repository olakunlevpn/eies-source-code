<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Pixabay;

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
			'type'      => 'pixabay',
			'file_type' => 'image',
			'photos'    => array(
				'sm'       => $data['webformatURL'],
				'sd'       => $data['webformatURL'],
				'hd'       => ! empty( $data['fullHDURL'] ) ? $data['fullHDURL'] : $data['largeImageURL'],
				'original' => ! empty( $data['imageURL'] ) ? $data['imageURL'] : $data['largeImageURL'],
			),
		);
	}
}
