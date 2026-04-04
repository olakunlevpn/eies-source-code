<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var array $data
 */

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeDisplay;
use MasterStudy\Lms\Repositories\FileMaterialRepository;

$current_template    = $data['current_template'];
$display_grade       = 'pending' !== $current_template && is_ms_lms_addon_enabled( 'grades' );
$assignments_files   = ( new FileMaterialRepository() )->get_files( get_post_meta( $item_id, 'assignment_files', true ) );
$is_deadline_expired = masterstudy_lms_assignment_is_deadline_totally_expired( $post_id, $item_id );
$timer_end_ts        = 0;

if ( masterstudy_lms_assignment_has_time_limit( $item_id ) && 'draft' === $current_template ) {
	$timer = STM_LMS_Helpers::simplify_db_array(
		stm_lms_get_user_assignments_time(
			$data['user_id'],
			$data['assignment_id'],
			array( 'end_time' )
		)
	);

	if ( ! empty( $timer['end_time'] ) ) {
		$timer_end_ts = masterstudy_lms_assignment_parse_time_to_timestamp( $timer['end_time'] );
	}
}

if ( empty( $data['theme_fonts'] ) ) {
	wp_enqueue_style( 'masterstudy-course-player-assignments-fonts' );
}
wp_enqueue_style( 'masterstudy-course-player-assignments' );
wp_enqueue_script( 'masterstudy-course-player-assignments' );
wp_localize_script(
	'masterstudy-course-player-assignments',
	'assignments_data',
	array(
		'submit_nonce'  => wp_create_nonce( 'stm_lms_accept_draft_assignment' ),
		'start_nonce'   => wp_create_nonce( 'stm_lms_start_assignment_timer' ),
		'course_id'     => $post_id,
		'editor_id'     => $data['editor_id'],
		'draft_id'      => $data['assignment_id'],
		'assignment_id' => $item_id,
		'ajax_url'      => admin_url( 'admin-ajax.php' ),
		'has_timer'     => masterstudy_lms_assignment_has_time_limit( $item_id ) && 'draft' === $current_template,
		'timer_end_ts'  => $timer_end_ts,
		'server_now_ts' => time(),
		'timer_error_message' => esc_html__( 'Unable to start assignment timer. Please refresh the page and try again.', 'masterstudy-lms-learning-management-system-pro' ),
	)
);

if ( 'passed' === $current_template || 'not_passed' === $current_template ) {
	STM_LMS_Assignments::student_view_update( $data['assignment_id'] );
}

STM_LMS_Templates::show_lms_template(
	'components/alert',
	array(
		'id'                  => 'assignment_submit_alert',
		'title'               => esc_html__( 'There are no text entry or uploads', 'masterstudy-lms-learning-management-system-pro' ),
		'text'                => '',
		'cancel_button_text'  => esc_html__( 'Close', 'masterstudy-lms-learning-management-system-pro' ),
		'cancel_button_style' => 'tertiary',
		'dark_mode'           => $data['dark_mode'],
	)
);
?>
<div class="masterstudy-course-player-assignments<?php echo esc_attr( 'draft' === $current_template ? ' masterstudy-course-player-assignments_draft' : '' ); ?>">
	<?php if ( 'draft' !== $current_template ) { ?>
		<div class="masterstudy-course-player-assignments__status masterstudy-course-player-assignments__status_<?php echo esc_attr( $current_template ); ?>">
			<?php if ( 'pending' === $current_template ) { ?>
				<img src="<?php echo esc_url( STM_LMS_URL . 'assets/icons/lessons/pending.gif' ); ?>" class="masterstudy-course-player-assignments__status-image">
				<?php
			} elseif ( $data['show_emoji'] && ! empty( $data['emoji_name'] ) ) {
				?>
					<p class="masterstudy-course-player-assignments__emoji"><?php echo esc_html( $data['emoji_name'] ); ?></p>
					<?php
			} else {
				?>
					<div class="masterstudy-course-player-assignments__status-icon"></div>
				<?php
			}
			?>
			<div class="masterstudy-course-player-assignments__status-wrapper">
				<?php if ( $display_grade ) { ?>
					<div class="masterstudy-course-player-assignments__status-message masterstudy-course-player-assignments__status-grade">
						<?php
						$grade = ( new AssignmentStudentRepository() )->get_grade( $data['assignment_id'] );

						echo esc_html( GradeDisplay::get_instance()->simple_render( $grade ) );
						?>
					</div>
				<?php } ?>

				<div class="masterstudy-course-player-assignments__status-message<?php echo $display_grade ? '-grade' : ''; ?>">
					<?php echo esc_html( $data['status_message'] ); ?>
				</div>

				<div class="masterstudy-course-player-assignments__status-info">
					<?php
					if ( $display_grade ) {
						$passing_grade = AssignmentStudentRepository::get_passing_grade( $data['assignment_id'] );

						if ( ! empty( $passing_grade ) ) {
							?>
							<div class="masterstudy-course-player-assignments__status-passing-grade">
								<?php
								printf(
									/* translators: %s: Grade */
									wp_kses_post( __( 'Minimal passing grade is: <strong>%s</strong>', 'masterstudy-lms-learning-management-system-pro' ) ),
									esc_html( GradeCalculator::get_instance()->get_passing_grade( $passing_grade ) )
								);
								?>
							</div>
							<?php
						}
					}

					if ( isset( $data['retake']['total'] ) && isset( $data['retake']['attempts'] ) && 'not_passed' === $current_template && ! $is_deadline_expired ) {
						?>
						<div class="masterstudy-course-player-assignments__status-attempts">
							<?php
							if ( is_rtl() ) {
								printf(
									wp_kses_post(
										/* translators: %s: number */
										__(
											'<strong>%2$s</strong> from <strong>%1$s</strong> attempts left.',
											'masterstudy-lms-learning-management-system-pro'
										)
									),
									esc_html( $data['retake']['total'] ),
									esc_html( $data['retake']['attempts'] )
								);
							} else {
								printf(
									wp_kses_post(
										/* translators: %s: number */
										__(
											'<strong>%1$s</strong> from <strong>%2$s</strong> attempts left.',
											'masterstudy-lms-learning-management-system-pro'
										)
									),
									esc_html( $data['retake']['attempts'] ),
									esc_html( $data['retake']['total'] )
								);
							}
							?>
						</div>
					<?php } ?>
					<?php if ( $is_deadline_expired ) : ?>
					<div class="masterstudy-course-player-assignments__status-attempts">
						<?php echo esc_html__( 'You cannot complete the assignments because the deadline has already passed.', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
				<?php
				if ( $data['retake']['can_attempt'] && 'not_passed' === $current_template && ! $is_deadline_expired ) {
					$query_args = array(
						'start_assignment' => $item_id,
						'course_id'        => $post_id,
					);
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'            => 'masterstudy-course-player-assignments-retake-button',
							'title'         => __( 'Retake', 'masterstudy-lms-learning-management-system-pro' ),
							'link'          => add_query_arg( $query_args, $data['actual_link'] ),
							'style'         => 'primary',
							'size'          => 'sm',
							'icon_position' => '',
							'icon_name'     => '',
						)
					);
				}
				?>
		</div>
	<?php } ?>
	<div class="masterstudy-course-player-assignments__task">
		<span class="masterstudy-course-player-assignments__accordion-button">
			<?php esc_html_e( 'Requirements', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</span>
		<div class="masterstudy-course-player-assignments__accordion-content">
			<?php
			echo wp_kses( $data['content'], stm_lms_allowed_html() );
			if ( ! empty( $assignments_files ) ) {
				STM_LMS_Templates::show_lms_template(
					'course-player/content/lesson/materials',
					array(
						'attachments' => $assignments_files,
						'dark_mode'   => $data['dark_mode'],
					)
				);
			}
			?>
		</div>
	</div>
	<?php if ( 'draft' === $current_template ) { ?>
		<div class="masterstudy-course-player-assignments__edit" data-editor="<?php echo esc_attr( $data['editor_id'] ); ?>">
			<span class="masterstudy-course-player-assignments__edit-title">
				<?php esc_html_e( 'Assignment', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</span>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/wp-editor',
				array(
					'id'          => $data['editor_id'],
					'content'     => $data['user_assignment']->post_content ?? '',
					'settings'    => array(
						'quicktags'     => false,
						'media_buttons' => false,
						'textarea_rows' => 13,
					),
					'theme_fonts' => true,
					'dark_mode'   => $data['dark_mode'],
				)
			);
			?>
		</div>
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/attachment-media',
			array(
				'assignment_id'     => $data['assignment_id'],
				'instructor_review' => false,
				'dark_mode'         => $data['dark_mode'],
			)
		);
	} else {
		?>
		<div class="masterstudy-course-player-assignments__user-answer">
			<span class="masterstudy-course-player-assignments__accordion-button masterstudy-course-player-assignments__accordion-button_rotate">
				<?php echo esc_html__( 'Your answer:', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</span>
			<div class="masterstudy-course-player-assignments__accordion-content">
				<?php echo wp_kses_post( $data['user_assignment']->post_content ?? '' ); ?>
				<div class="masterstudy-course-player-assignments__user-answer-files">
					<?php
					if ( ! empty( $data['student_attachments'] ) ) {
						STM_LMS_Templates::show_lms_template(
							'components/file-attachment',
							array(
								'attachments' => $data['student_attachments'],
								'download'    => true,
								'deletable'   => false,
								'dark_mode'   => $data['dark_mode'],
							)
						);
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	$editor_comment = get_post_meta( $data['assignment_id'], 'editor_comment', true );

	if ( ( 'passed' === $current_template || 'not_passed' === $current_template ) && ( ! empty( $editor_comment ) || ! empty( $data['instructor_attachments'] ) ) ) {
		STM_LMS_Templates::show_lms_template(
			'course-player/assignments/instructor-comment',
			array(
				'assignment_id' => $data['assignment_id'],
				'comment'       => $editor_comment,
				'attachments'   => $data['instructor_attachments'],
				'dark_mode'     => $data['dark_mode'],
			)
		);
	}

	do_action( 'stm_lms_after_assignment' );
	?>
</div>
