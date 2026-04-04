<?php
/**
 * @var $assignment_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;

$lms_current_user = (array) STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

if ( empty( $assignment_id ) ) {
	require_once get_404_template();
	die;
}

wp_enqueue_style( 'masterstudy-account-user-assignment' );

$assignment        = STM_LMS_User_Assignment::get_assignment( $assignment_id );
$attempt           = get_post_meta( $assignment_id, 'try_num', true );
$parent_id         = get_post_meta( $assignment_id, 'assignment_id', true );
$assignment_status = $assignment['status'] ?? '';
$badge_class       = '';
$assignment_repo   = new AssignmentStudentRepository();

if ( 'not_passed' === $assignment_status ) {
	$badge_class = ' masterstudy-user-assignment__badge_danger';
}

if ( 'passed' === $assignment_status ) {
	$badge_class = ' masterstudy-user-assignment__badge_success';
}
?>

<div class="masterstudy-account">
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
		<div class="masterstudy-user-assignment">
			<div class="masterstudy-user-assignment__header">
				<div class="masterstudy-user-assignment__header-left-container">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/back-link',
						array(
							'id'  => 'masterstudy-course-player-back',
							'url' => ms_plugin_user_account_url( "assignments/{$parent_id}" ),
						)
					);
					?>
					<div class="masterstudy-user-assignment__header-title">
						<div class="masterstudy-user-assignment__header-page-title"><?php echo esc_html__( 'Student Assignment', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
						<h2 class="masterstudy-user-assignment__header-title"><?php echo esc_html( $assignment['assignment_title'] ?? '' ); ?></h2>
					</div>
				</div>
				<div class="masterstudy-user-assignment__header-badges">
					<span class="masterstudy-user-assignment__badge">
						<?php echo esc_html__( 'Attempt', 'masterstudy-lms-learning-management-system-pro' ); ?>:
						<?php echo esc_html( $attempt ); ?>
					</span>
					<?php
					if ( is_ms_lms_addon_enabled( 'grades' ) ) {
						$grade = $assignment_repo->get_grade( $assignment_id );
						if ( ! empty( $grade ) ) :
							?>
							<span class="masterstudy-user-assignment__badge">
								<?php echo esc_html__( 'Grade', 'masterstudy-lms-learning-management-system-pro' ); ?>:
								<?php echo esc_html( $grade ); ?>
							</span>
						<?php endif ?>
					<?php } ?>
					<span class="masterstudy-user-assignment__badge<?php echo esc_attr( $badge_class ); ?>">
						<?php echo esc_html( $assignment_repo->get_status_html( $assignment_repo->get_status( $assignment_id ), false ) ); ?>
					</span>
				</div>
			</div>
			<div class="masterstudy-user-assignment__content-container">
				<h2 class="masterstudy-user-assignment__title">
					<?php echo esc_html__( 'Answered by student', 'masterstudy-lms-learning-management-system-pro' ); ?>:
					<?php echo esc_html( $assignment_repo->get_display_name( $assignment_id ) ); ?>
				</h2>
				<div class="masterstudy-user-assignment__content">
					<?php echo wp_kses_post( $assignment['content'] ); ?>
				</div>
				<div class="masterstudy-user-assignment__attachments">
					<?php
					$attachments = STM_LMS_Assignments::get_draft_attachments( $assignment_id, 'student_attachments' );
					if ( ! empty( $attachments ) ) {
						STM_LMS_Templates::show_lms_template(
							'components/file-attachment',
							array(
								'attachments' => $attachments,
								'dark_mode'   => false,
							)
						);
					}
					?>
				</div>
			</div>
			<?php
			STM_LMS_Templates::show_lms_template(
				'account/instructor/parts/assignment-review',
				compact( 'assignment_id', 'assignment_status', 'assignment' )
			);
			?>
		</div>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
