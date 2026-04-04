<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

final class Video extends Field {
	public static array $properties = array(
		'id'            => array(
			'type'        => 'string',
			'description' => 'Photo ID',
		),
		'type'          => array(
			'type'        => 'string',
			'description' => 'Integration name',
		),
		'file_type'     => array(
			'type'        => 'string',
			'description' => 'File type',
		),
		'video_preview' => array(
			'type'        => 'string',
			'description' => 'Video preview picture',
		),
		'videos'        => array(
			'type'       => 'object',
			'properties' => array(
				'sd'       => array(
					'type'        => 'string',
					'description' => 'Low quality video',
				),
				'hd'       => array(
					'type'        => 'string',
					'description' => 'Medium quality video',
				),
				'original' => array(
					'type'        => 'string',
					'description' => 'Original quality video',
				),
			),
		),
	);
}
