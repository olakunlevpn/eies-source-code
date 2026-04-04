<?php

namespace MasterStudy\Lms\Pro\addons\media_library\Routing\Swagger\Fields;

use MasterStudy\Lms\Routing\Swagger\Field;

final class Photo extends Field {
	public static array $properties = array(
		'id'        => array(
			'type'        => 'string',
			'description' => 'Photo ID',
		),
		'type'      => array(
			'type'        => 'string',
			'description' => 'Integration name',
		),
		'file_type' => array(
			'type'        => 'string',
			'description' => 'File type',
		),
		'photos'    => array(
			'type'       => 'object',
			'properties' => array(
				'sm'       => array(
					'type'        => 'string',
					'description' => 'Small sized photo',
				),
				'sd'       => array(
					'type'        => 'string',
					'description' => 'Medium sized photo',
				),
				'hd'       => array(
					'type'        => 'string',
					'description' => 'Large sized photo',
				),
				'original' => array(
					'type'        => 'string',
					'description' => 'Original sized photo',
				),
			),
		),
	);
}
