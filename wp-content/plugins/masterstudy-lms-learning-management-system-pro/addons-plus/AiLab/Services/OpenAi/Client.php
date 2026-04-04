<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi;

use DateTimeInterface;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Utility\ArrayHelper;

class Client {
	public const OPENAI_V1 = 'https://api.openai.com/v1';

	private $api_key;

	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	public function get_files() {
		return $this->request( 'GET', '/files' );
	}

	public function get_file( $file_id ) {
		return $this->request( 'GET', "/files/$file_id" );
	}

	public function get_models() {
		return $this->request( 'GET', '/models' );
	}

	public function get_model( $model_id ) {
		return $this->request( 'GET', "/models/$model_id" );
	}

	public function upload_file( UploadFile $file, string $purpose ) {
		return $this->request(
			'POST',
			'/files',
			array(
				'purpose' => $purpose,
				'file'    => $file,
			)
		);
	}

	public function delete_file( $file_id ) {
		return $this->request( 'DELETE', "/files/$file_id" );
	}

	public function get_file_content( $file_id ) {
		return $this->request( 'GET', "/files/$file_id/content" );
	}

	/**
	 * @return array<string>
	 */
	public function prepare_upload( array $data ) {
		$boundary = wp_generate_password( 12, false );

		$body = '';
		foreach ( $data as $name => $value ) {
			$body .= "--$boundary\r\n";
			$body .= "Content-Disposition: form-data; name=\"$name\"";
			if ( $value instanceof UploadFile ) {
				$body .= "; filename=\"{$value->filename}\"\r\n";
				$body .= "Content-Type: application/json\r\n\r\n";
				$body .= $value->content . "\r\n";
			} else {
				$body .= "\r\n\r\n$value\r\n";
			}
		}
		$body .= "--$boundary--\r\n";

		return array( $boundary, $body );
	}

	public function request( $method, $endpoint, $body = null ) {
		$headers = array(
			'Authorization' => 'Bearer ' . $this->api_key,
		);

		if ( is_array( $body ) ) {
			$has_file = null !== ArrayHelper::first(
				$body,
				function ( $value ) {
					return $value instanceof UploadFile;
				}
			);

			if ( $has_file ) {
				list( $boundary, $body ) = $this->prepare_upload( $body );
				$headers['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
			} else {
				$headers['Content-Type'] = 'application/json';
				$body                    = wp_json_encode( $body );
			}
		}

		$options = array(
			'headers'   => $headers,
			'method'    => $method,
			'timeout'   => 300,
			'body'      => $body,
			'sslverify' => false,
		);

		try {
			$response = wp_remote_request( self::OPENAI_V1 . $endpoint, $options );
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $data['error'] ) ) {
				if ( ! empty( $data['error']['message'] ) ) {
					$error_message = str_replace( $this->api_key, str_repeat( '*', 6 ), $data['error']['message'] );
				} elseif ( ! empty( $response['response']['message'] ) ) {
					$error_message = 'Bad Request' === $response['response']['message']
						? esc_html__( 'Sorry, your request cannot be processed. To maintain a safe and respectful environment, we restrict content related to certain sensitive topics.', 'masterstudy-lms-learning-management-system-pro' )
						: $response['response']['message'];
				} else {
					$error_message = esc_html__( 'Unknown error.', 'masterstudy-lms-learning-management-system-pro' );
				}

				throw new Exception( $error_message );
			}

			return $data;
		} catch ( Exception $e ) {
			throw new Exception( 'OpenAI API Error: ' . $e->getMessage() );
		}
	}

	/**
	 * @throws Exception
	 * @return array<array{"title": string, "description": string, "date": string}>
	 */
	public function get_incidents( DateTimeInterface $after ): array {
		$url      = 'https://status.openai.com/history.rss';
		$response = wp_remote_get( $url, array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		$xml       = simplexml_load_string( wp_remote_retrieve_body( $response ) );
		$incidents = array();

		foreach ( $xml->channel->item as $item ) {
			$incident_time = strtotime( $item->pubDate ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			// assume that it ordered by date
			if ( $after->getTimestamp() > $incident_time ) {
				break;
			}

			$incidents[] = array(
				'title'       => (string) $item->title,
				'description' => (string) $item->description,
				'date'        => $incident_time,
			);
		}

		return $incidents;
	}

	public function create_completions( array $data ) {
		return $this->request( 'POST', '/chat/completions', $data );
	}

	public function create_images( array $data ) {
		return $this->request( 'POST', '/images/generations', $data );
	}

	private function get_model_suffix( $model ) {
		preg_match( '/:([^:]+)(?=:[^:]+$)/', $model, $matches );
		if ( count( $matches ) > 0 ) {
			return $matches[1];
		}

		return 'Unknown';
	}
}
