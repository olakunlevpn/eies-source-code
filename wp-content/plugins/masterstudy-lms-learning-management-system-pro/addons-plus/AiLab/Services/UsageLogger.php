<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\ImageQuery;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\TextQuery;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Response;

final class UsageLogger {
	public static string $option_name = 'stm_lms_openai_usage';

	public static function log( Response $response ) {
		$usage = get_option( self::$option_name, array() );
		$month = gmdate( 'Y-m' );
		$query = $response->query;
		$model = $query->model;

		if ( $query instanceof TextQuery ) {
			if ( ! isset( $usage[ $month ][ $model ] ) ) {
				$usage[ $month ][ $model ] = array(
					'prompt_tokens'     => 0,
					'completion_tokens' => 0,
					'total_tokens'      => 0,
				);
			}

			$usage[ $month ][ $model ]['prompt_tokens']     += $response->raw['usage']['prompt_tokens'];
			$usage[ $month ][ $model ]['completion_tokens'] += $response->raw['usage']['completion_tokens'];
			$usage[ $month ][ $model ]['total_tokens']      += $response->raw['usage']['total_tokens'];

			update_option( self::$option_name, $usage );

			return array(
				'completion_tokens' => $response->raw['usage']['completion_tokens'],
				'prompt_tokens'     => $response->raw['usage']['prompt_tokens'],
				'total_tokens'      => $response->raw['usage']['total_tokens'],
			);
		}

		if ( $query instanceof ImageQuery ) {
			$images = count( $response->results );
			$size   = $query->size;

			if ( ! isset( $usage[ $month ][ $model ] ) ) {
				$usage[ $month ][ $model ] = array(
					$size   => 0,
					'total' => 0,
				);
			}

			if ( ! isset( $usage[ $month ][ $model ][ $size ] ) ) {
				$usage[ $month ][ $model ][ $size ] = 0;
			}

			$usage[ $month ][ $model ][ $size ] += $images;
			$usage[ $month ][ $model ]['total'] += $images;

			update_option( self::$option_name, $usage );

			return array(
				'size'  => $size,
				'total' => $images,
			);
		}

		throw new \RuntimeException( 'Unknown query type' );
	}
}
