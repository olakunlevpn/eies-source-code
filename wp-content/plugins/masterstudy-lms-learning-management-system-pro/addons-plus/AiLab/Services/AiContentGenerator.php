<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services;

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Client;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\ImageQuery;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\Query;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Queries\TextQuery;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Response;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits\AiAssignmentGeneratorTrait;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits\AiCourseGenerator;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits\AiFaqGeneratorTrait;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits\AiLessonGeneratorTrait;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\Traits\AiQuestionsGeneratorTrait;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Utility\Options;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class AiContentGenerator {

	use AiAssignmentGeneratorTrait;
	use AiCourseGenerator;
	use AiFaqGeneratorTrait;
	use AiLessonGeneratorTrait;
	use AiQuestionsGeneratorTrait;

	private Client $client;
	public Options $options;
	private string $image_regex = '/\[(?:image|Image)\](.*?)\[\/(?:image|Image)\]/s';

	public function __construct( Client $client ) {
		$this->client  = $client;
		$this->options = new Options();
	}

	/**
	 * @return Response
	 */
	public function exec( Query $query ) {
		try {
			if ( $query instanceof TextQuery ) {
				$data     = $this->client->create_completions( $query->to_request_body() );
				$response = new Response(
					$query,
					! is_string( $data ) ? $data['choices'] : array( $data ),
					! is_string( $data ) ? $data : array( $data ),
				);
			} elseif ( $query instanceof ImageQuery ) {
				$data     = $this->client->create_images( $query->to_request_body() );
				$response = new Response(
					$query,
					! is_string( $data ) ? array_column( $data['data'], 'url' ) : array( $data ),
					! is_string( $data ) ? $data : array( $data ),
				);
			} else {
				throw new InvalidArgumentException( 'Unknown query type' );
			}

			UsageLogger::log( $response );

			return $response;
		} catch ( Exception $e ) {
			throw new RuntimeException( $e->getMessage() );
		}
	}

	// Parse content response
	public function parse_content_response( string $response ): array {
		// Extract image prompts
		preg_match_all( $this->image_regex, $response, $matches );
		$image_prompts = $matches[1] ?? array();

		return array(
			'content'       => trim( $response ),
			'image_prompts' => array_map( 'trim', $image_prompts ),
		);
	}

	// Replace image placeholders with actual images
	public function replace_image_placeholders( string $content ): string {
		$placeholder_image_url = esc_url( STM_LMS_PRO_URL . 'assets/img/ai-image-placeholder.png' );

		return preg_replace_callback(
			$this->image_regex,
			function( $matches ) use ( $placeholder_image_url ) {
				return '<p><img src="' . $placeholder_image_url . '" data-prompt="' . esc_attr( $matches[1] ) . '" alt="Placeholder Image" /></p>';
			},
			$content
		);
	}
}
