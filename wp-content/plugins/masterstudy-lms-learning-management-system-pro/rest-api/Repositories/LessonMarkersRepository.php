<?php // phpcs:ignoreFile

namespace MasterStudy\Lms\Pro\RestApi\Repositories;

use MasterStudy\Lms\Utility\Traits\VideoTrait;

final class LessonMarkersRepository {
	use VideoTrait;

	protected $wpdb;
	protected $table_name;

	public function __construct() {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->table_name = stm_lms_lesson_marker_questions_name( $wpdb );
	}

	public function marker_time_exists( int $lesson_id, int $time ): bool {
		$count = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE lesson_id = %d AND marker = %d",
				$lesson_id,
				$time
			)
		);

		return $count > 0;
	}

	public function marker_id_exists( int $current_marker_id, int $time, int $lesson_id ): bool {
		$count = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"
            SELECT COUNT(*) 
              FROM {$this->table_name} 
             WHERE lesson_id = %d
               AND marker    = %d
               AND id       != %d
            ",
				$lesson_id,
				$time,
				$current_marker_id
			)
		);

		return $count > 0;
	}

	public function create( int $lesson_id, int $time, string $caption ) {
		$this->wpdb->insert(
			$this->table_name,
			array(
				'lesson_id' => $lesson_id,
				'marker'    => $time,
				'caption'   => $caption,
			),
			array(
				'%d',
				'%d',
				'%s',
			)
		);

		return $this->wpdb->insert_id;
	}

	public function create_question( int $lesson_id, array $new_marker ) {
		$this->wpdb->insert(
			$this->table_name,
			array(
				'lesson_id' => $lesson_id,
				'marker'    => $new_marker['time'],
				'caption'   => $new_marker['caption'],
				'rewatch'   => $new_marker['rewatch'],
				'content'   => $new_marker['content'],
				'type'      => $new_marker['type'],
				'answers'   => maybe_serialize( $new_marker['answers'] ),
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		return $this->wpdb->insert_id;
	}

	public function update( int $marker_id, array $new_marker ) {
		$result = $this->wpdb->update(
			$this->table_name,
			array(
				'marker'  => $new_marker['time'],
				'content' => $new_marker['content'],
				'type'    => $new_marker['type'],
				'answers' => maybe_serialize( $new_marker['answers'] ),
				'caption' => $new_marker['caption'],
				'rewatch' => $new_marker['rewatch'],
			),
			array(
				'id' => $marker_id,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			),
			array(
				'%d',
			)
		);

		return $result;
	}

	public function get_markers( int $lesson_id ): array {
		$markers = $this->wpdb->get_results(
			$this->wpdb->prepare( //phpcs:ignore
				"SELECT * FROM {$this->table_name} WHERE lesson_id = %d", //phpcs:ignore
				$lesson_id //phpcs:ignore
			),
			ARRAY_A
		);

		foreach ( $markers as &$marker ) {
			if ( isset( $marker['answers'] ) && ! empty( $marker['answers'] ) ) {
				if ( is_string( $marker['answers'] ) ) {
					$decoded = maybe_unserialize( $marker['answers'] );
					if ( false !== $decoded ) {
						$marker['answers'] = json_encode( $decoded );
					}
				} else {
					$marker['answers'] = json_encode( $marker['answers'] );
				}
				if ( ! empty( $marker['rewatch'] ) ) {

					if ( $marker['rewatch'] < 0 ) {
						$marker['rewatch'] = '-1';
					} else {
						$marker['rewatch'] = masterstudy_lms_timecode_to_string( $marker['rewatch'] );
					}
				}
			}
		}

		return $markers;
	}

	public function get_video_lesson_utils( int $lesson_id ): array {
		$settings     = get_option( 'stm_lms_settings' );
		$plyr_vimeo   = $settings['course_player_vimeo_video_player'] ?? false;
		$plyr_youtube = $settings['course_player_youtube_video_player'] ?? false;

		$video_type  = get_post_meta( $lesson_id, 'video_type', true );
		$lesson_meta = array();

		foreach ( $this->get_video_fields_mapping( $video_type ) as $key ) {
			$meta_value          = get_post_meta( $lesson_id, $key, true );
			$lesson_meta[ $key ] = ( 'lesson_video' === $key )
				? wp_get_attachment_url( (int) $meta_value )
				: $meta_value;
		}
		$should_render_pro_tip     = false;
		$lesson_meta['video_type'] = $video_type;

		if ( 'youtube' === $video_type || 'vimeo' === $video_type ) {
			$should_render_pro_tip = ( ! $plyr_vimeo || ! $plyr_youtube );
		}

		$current_user = wp_get_current_user();
		if ( in_array( 'stm_lms_instructor', (array) $current_user->roles, true ) && ! in_array( 'administrator', (array) $current_user->roles, true ) ) {
			$should_render_pro_tip = false;
		}

		return array(
			'shouldRenderTip'       => $should_render_pro_tip,
			'lesson_metas'          => $lesson_meta,
			'questions_lock_switch' => get_post_meta( $lesson_id, 'video_marker_questions_locked', true ),
		);
	}

	public function delete( int $lesson_id, int $marker_id ) {
		return $this->wpdb->delete(
			$this->table_name,
			array(
				'lesson_id' => $lesson_id,
				'id'        => $marker_id,
			),
			array(
				'%d',
				'%d',
			)
		);
	}
}
