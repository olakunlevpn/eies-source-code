<?php
/**
 * @var int $post_id
 * @var int $item_id
 * @var array $data
 */

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;
use MasterStudy\Lms\Repositories\FileMaterialRepository;

if ( empty( $data['theme_fonts'] ) ) {
	wp_enqueue_style( 'masterstudy-course-player-assignments-fonts' );
}

$assignments_files = ( new FileMaterialRepository() )->get_files( get_post_meta( $item_id, 'assignment_files', true ) );
$assignment_data   = ( new AssignmentRepository() )->get( $item_id );
wp_enqueue_style( 'masterstudy-course-player-assignments' );
?>
<div class="masterstudy-course-player-assignments">
	<div class="masterstudy-course-player-assignments__meta">
		<?php if ( is_ms_lms_addon_enabled( 'grades' ) && ! empty( $assignment_data['passing_grade'] ) ) : ?>
			<?php
			$passing_grade_label = GradeCalculator::get_instance()->get_passing_grade( $assignment_data['passing_grade'] );

			STM_LMS_Templates::show_lms_template(
				'course-player/assignments/meta/meta-item',
				array(
					'label' => esc_html__( 'Passing grade', 'masterstudy-lms-learning-management-system-pro' ),
					'icon'  => 'stmlms-check1',
					'value' => $passing_grade_label,
				)
			);
			?>
		<?php endif; ?>
		<?php if ( ! empty( $assignment_data['time_limit'] ) && ! empty( $assignment_data['time_limit_unit'] ) ) : ?>
			<?php
			STM_LMS_Templates::show_lms_template(
				'course-player/assignments/meta/meta-item',
				array(
					'label' => esc_html__( 'Deadline', 'masterstudy-lms-learning-management-system-pro' ),
					'icon'  => 'stmlms-time',
					'value' => $assignment_data['time_limit'] . ' ' . masterstudy_lms_get_assignment_duration_translations()[ $assignment_data['time_limit_unit'] ] ?? $assignment_data['time_limit_unit'],
				)
			);
			?>
		<?php endif; ?>
		<?php if ( ! empty( $assignment_data['attempts'] ) ) : ?>
			<?php
			STM_LMS_Templates::show_lms_template(
				'course-player/assignments/meta/meta-item',
				array(
					'label' => esc_html__( 'Number of allowed attempts', 'masterstudy-lms-learning-management-system-pro' ),
					'icon'  => 'stmlms-refresh',
					'value' => $assignment_data['attempts'],
				)
			);
			?>
		<?php endif ?>
	</div>
	<div class="masterstudy-course-player-assignments__content">
		<?php echo ! empty( $data['user_id'] ) ? wp_kses( $data['content'], stm_lms_allowed_html() ) : '<p>' . esc_html__( 'To begin an assignment, you need to register or log in with an existing account', 'masterstudy-lms-learning-management-system-pro' ) . '</p>'; ?>
	</div>
	<?php
	if ( ! empty( $assignments_files ) ) {
		STM_LMS_Templates::show_lms_template(
			'course-player/content/lesson/materials',
			array(
				'attachments' => $assignments_files,
				'dark_mode'   => $data['dark_mode'],
			)
		);
	}
	if ( ! empty( $data['user_id'] ) ) {
		?>
		<div class="masterstudy-course-player-assignments__button">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'            => 'masterstudy-course-player-assignments-start-button',
					'title'         => __( 'Start Assignment', 'masterstudy-lms-learning-management-system-pro' ),
					'link'          => add_query_arg(
						array(
							'start_assignment' => $item_id,
							'course_id'        => $post_id,
						),
						$data['actual_link'] ?? '',
					),
					'style'         => 'primary',
					'size'          => 'md',
					'icon_position' => '',
					'icon_name'     => '',
				)
			);
			?>
		</div>
	<?php } ?>
</div>
