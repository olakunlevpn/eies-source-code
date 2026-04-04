<?php
/**
 * Video Questions List component
 *
 * @var array $video_questions
 */

wp_enqueue_style( 'masterstudy-video-questions-list' );

$questions_title = STM_LMS_Options::get_option( 'video_questions_title', __( 'Video questions', 'masterstudy-lms-learning-management-system-pro' ) );
$valid_questions = array_filter(
	$video_questions,
	function( $question ) {
		return ! empty( $question['type'] );
	}
);

if ( empty( $valid_questions ) ) {
	return;
}
?>

<div class="masterstudy-lesson-video-questions-list">
	<div class="masterstudy-lesson-video-questions-list__title">
		<?php echo esc_html( $questions_title ); ?>
	</div>
	<?php
	foreach ( $valid_questions as $question ) {
		$classes = implode(
			' ',
			array_filter(
				array(
					$question['is_answered'] && $question['is_completed'] ? 'masterstudy-lesson-video-list-question_completed' : '',
					$question['is_answered'] && ! $question['is_completed'] ? 'masterstudy-lesson-video-list-question_failed' : '',
				)
			)
		);
		?>
		<div class="masterstudy-lesson-video-list-question <?php echo esc_attr( $classes ); ?>" data-marker="<?php echo esc_attr( $question['marker'] ); ?>" id="<?php echo esc_attr( $question['id'] ); ?>">
			<div class="masterstudy-lesson-video-list-question__timecode">
				<?php echo esc_html( masterstudy_lms_timecode_to_string( $question['marker'] ) ); ?>
			</div>
			<div class="masterstudy-lesson-video-list-question__content">
				<?php echo esc_html( $question['content'] ); ?>
			</div>
			<div class="masterstudy-lesson-video-list-question__complete">
				<span class="masterstudy-lesson-video-list-question__check"></span>
			</div>
		</div>
	<?php } ?>
</div>
