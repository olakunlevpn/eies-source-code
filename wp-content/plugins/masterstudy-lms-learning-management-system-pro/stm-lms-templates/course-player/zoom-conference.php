<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var array $data
 */

wp_enqueue_style( 'masterstudy-course-player-lesson-zoom' );
wp_enqueue_style( 'masterstudy-course-player-lesson-zoom-fonts' );

if ( empty( $data['theme_fonts'] ) ) {
	wp_enqueue_style( 'masterstudy-course-player-lesson-zoom-fonts' );
}

$meeting_id = get_post_meta( $item_id, 'meeting_created', true );

if ( empty( $meeting_id ) ) {
	$mslms_meeting_id = get_post_meta( $item_id, 'mslms_zoom_meeting_id', true );

	if ( ! empty( $mslms_meeting_id ) ) {
		// Find the stm-zoom post that has this meeting ID
		$zoom_posts = get_posts(
			array(
				'post_type'   => 'ms-zoom',
				'meta_key'    => 'stm_zoom_data',
				'meta_query'  => array(
					array(
						'key'     => 'stm_zoom_data',
						'value'   => '"id";i:' . $mslms_meeting_id,
						'compare' => 'LIKE',
					),
				),
				'numberposts' => 1,
			)
		);

		if ( ! empty( $zoom_posts ) ) {
			$meeting_id = $zoom_posts[0]->ID;
			update_post_meta( $item_id, 'meeting_created', $meeting_id );
		}
	}
}
$content = masterstudy_course_player_get_content( $item_id, true );
?>
<div class="masterstudy-course-player-lesson-zoom">
	<?php
	if ( ! empty( $meeting_id ) ) {
		echo do_shortcode( '[stm_zoom_conference post_id="' . $meeting_id . '"]' );
	}
	if ( ! empty( $content ) ) {
		?>
		<div class="masterstudy-course-player-lesson-zoom__content">
			<?php echo wp_kses( htmlspecialchars_decode( $content ), stm_lms_allowed_html() ); ?>
		</div>
	<?php } ?>
</div>
