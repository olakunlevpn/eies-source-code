<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Http\Controllers\Integrations\Serializer\Pexels;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class VideoSerializer extends AbstractSerializer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function toArray( $data ): array {
		usort(
			$data['video_files'],
			function ( $a, $b ) {
				return $b['width'] <=> $a['width'];
			}
		);

		return array(
			'id'            => $data['id'],
			'type'          => 'pexels',
			'file_type'     => 'video',
			'video_preview' => $data['video_pictures'][0]['picture'],
			'videos'        => array(
				'sd'       => $data['video_files'][2]['link'],
				'hd'       => $data['video_files'][1]['link'],
				'original' => $data['video_files'][0]['link'],
			),
		);
	}
}
