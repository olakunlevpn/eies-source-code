<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class AiLessonSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'title'         => $data['title'],
			'description'   => $data['description'],
			'content'       => $data['content'],
			'image_prompts' => $data['image_prompts'] ?? array(),
			'duration'      => $data['duration'],
		);
	}
}
