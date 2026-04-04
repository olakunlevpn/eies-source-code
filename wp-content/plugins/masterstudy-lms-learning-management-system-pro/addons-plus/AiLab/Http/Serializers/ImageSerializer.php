<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class ImageSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'id'       => $data['id'],
			'title'    => $data['title'],
			'url'      => $data['url'],
			'type'     => $data['type'],
			'date'     => $data['date'],
			'modified' => $data['modified'],
			'size'     => $data['size'],
		);
	}
}
