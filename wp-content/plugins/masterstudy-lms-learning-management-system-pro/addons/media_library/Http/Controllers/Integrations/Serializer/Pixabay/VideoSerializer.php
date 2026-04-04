<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Pixabay;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class VideoSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		return array(
			'id'            => $data['id'],
			'type'          => 'pixabay',
			'file_type'     => 'video',
			'video_preview' => $data['videos']['small']['thumbnail'],
			'videos'        => array(
				'sd'       => $data['videos']['small']['url'],
				'hd'       => $data['videos']['medium']['url'],
				'original' => $data['videos']['large']['url'],
			),
		);
	}
}
