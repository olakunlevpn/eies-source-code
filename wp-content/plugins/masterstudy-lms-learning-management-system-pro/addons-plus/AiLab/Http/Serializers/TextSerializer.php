<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class TextSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'index' => $data['index'],
			'text'  => $data['message']['content'] ?? '',
		);
	}
}
