<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries;

final class QueryFactory {
	public static function text( array $messages, array $params ): Query {
		$query = new TextQuery( $messages );
		self::apply_params( $query, $params );

		return $query;
	}

	public static function image( string $prompt, array $params ): Query {
		$query = new ImageQuery( $prompt );
		self::apply_params( $query, $params );

		return $query;
	}

	public static function format_prompt_to_message( $prompt ): array {
		return array(
			array(
				'role'    => 'user',
				'content' => $prompt,
			),
		);
	}

	private static function apply_params( Query $query, array $params ): void {
		foreach ( $params as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$method = 'set_' . $key;

			if ( method_exists( $query, $method ) ) {
				$query->$method( $value );
			}
		}
	}
}
