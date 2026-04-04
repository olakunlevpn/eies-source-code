<?php
/**
 * Video Questions component
 *
 * @var array $video_questions
 * @var integer $total_questions
 * @var boolean $questions_must_done
 */

wp_enqueue_style( 'masterstudy-video-questions' );
wp_enqueue_script( 'masterstudy-video-questions' );

if ( empty( $video_questions ) ) {
	return;
}
?>

<div class="masterstudy-lesson-video-questions">
	<?php
	foreach ( $video_questions as $index => $question ) {
		$classes = implode(
			' ',
			array_filter(
				array(
					$question['is_answered'] ? 'masterstudy-lesson-video-question_answered' : '',
					$question['is_answered'] && $question['is_completed'] ? 'masterstudy-lesson-video-question_completed' : '',
					$question['is_answered'] && ! $question['is_completed'] ? 'masterstudy-lesson-video-question_failed' : '',
				)
			)
		);
		?>
		<div class="masterstudy-lesson-video-question <?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $question['id'] ); ?>" data-marker="<?php echo esc_attr( $question['marker'] ); ?>">
			<div class="masterstudy-lesson-video-question__quantity">
				<?php
				echo esc_html( $index + 1 );
				echo ' / ';
				echo esc_html( $total_questions );
				?>
			</div>
			<div class="masterstudy-lesson-video-question__wrapper">
				<div class="masterstudy-lesson-video-question__content">
					<?php echo esc_html( $question['content'] ); ?>
				</div>
				<div class="masterstudy-lesson-video-question__answers">
					<?php
					foreach ( $question['answers'] as $answer ) {
						$is_multi_choice = 'multi_choice' === $question['type'];
						$input_type      = $is_multi_choice ? 'checkbox' : 'radio';
						$answer_class    = implode(
							' ',
							array_filter(
								array(
									$answer['is_selected'] ? 'masterstudy-lesson-video-question__answer_selected' : '',
									$answer['is_selected'] && $answer['is_correct'] ? 'masterstudy-lesson-video-question__answer_completed' : '',
									$answer['is_selected'] && ! $answer['is_correct'] ? 'masterstudy-lesson-video-question__answer_failed' : '',
								)
							)
						);
						?>
						<div class="masterstudy-lesson-video-question__answer <?php echo esc_attr( $answer_class ); ?>">
							<div class="masterstudy-lesson-video-question__input">
								<input type="<?php echo esc_attr( $input_type ); ?>" id="<?php echo esc_attr( $answer['answer_id'] ); ?>" name="<?php echo esc_attr( $question['id'] ); ?>" />
								<span class="masterstudy-lesson-video-question__<?php echo esc_attr( $input_type ); ?> <?php echo esc_attr( $answer['is_selected'] ? "masterstudy-lesson-video-question__{$input_type}_checked" : '' ); ?>"></span>
							</div>
							<div class="masterstudy-lesson-video-question__answer-content">
								<?php echo esc_html( $answer['text'] ); ?>
							</div>
							<div class="masterstudy-lesson-video-question__complete">
								<span class="masterstudy-lesson-video-question__check"></span>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="masterstudy-lesson-video-question__action">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-video-question-submit',
						'title' => __( 'Submit', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);

				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-video-question-continue',
						'title' => __( 'Continue to video', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);

				if ( ! empty( $question['rewatch'] ) && '-1' !== $question['rewatch'] ) {
					?>
					<div class="masterstudy-lesson-video-question__rewatch" data-rewatch="<?php echo esc_attr( $question['rewatch'] ); ?>">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'id'    => 'masterstudy-video-question-rewatch',
								'title' => __( 'Rewatch', 'masterstudy-lms-learning-management-system-pro' ),
								'link'  => '#',
								'style' => 'tertiary',
								'size'  => 'sm',
							)
						);
						?>
					</div>
					<?php
				}
				?>
				<div class="masterstudy-lesson-video-question__skip">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-video-question-skip',
							'title' => __( 'Skip', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'tertiary',
							'size'  => 'sm',
						)
					);
					?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
