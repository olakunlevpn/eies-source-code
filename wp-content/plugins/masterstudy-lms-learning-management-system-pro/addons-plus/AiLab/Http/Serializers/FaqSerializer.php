<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class FaqSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'question' => $data['question'] ?? '',
			'answer'   => $data['answer'] ?? '',
		);
	}
}
