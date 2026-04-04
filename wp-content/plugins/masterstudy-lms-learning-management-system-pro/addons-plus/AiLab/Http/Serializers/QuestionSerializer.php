<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Http\Serializers;

use MasterStudy\Lms\Enums\QuestionType;
use MasterStudy\Lms\Http\Serializers\AbstractSerializer;

final class QuestionSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		return array(
			'question' => $data['question'],
			'type'     => $data['type'],
			'answers'  => $this->serialize_answers( $data['answers'], $data['type'] ),
		);
	}

	public function serialize_answers( $answers, $type ): array {
		return array_map(
			function( $answer ) use ( $type ) {
				$serialized = array(
					'text'   => $answer['text'],
					'isTrue' => $answer['isTrue'],
				);

				if ( in_array( $type, array( QuestionType::IMAGE_MATCH, QuestionType::ITEM_MATCH ), true ) ) {
					$serialized['question'] = $answer['question'];
				}

				return $serialized;
			},
			$answers
		);
	}
}
