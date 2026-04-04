<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class AiAssignmentSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'title'         => $data['title'],
			'content'       => $data['content'],
			'image_prompts' => $data['image_prompts'] ?? array(),
		);
	}
}
