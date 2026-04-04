<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-main' );
wp_enqueue_style( 'masterstudy-account-points-distribution' );

$points = stm_lms_point_system();
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-points-distribution">
			<h1 class="masterstudy-account-points-distribution__title">
				<?php
				printf(
					/* translators: %s Points Label */
					esc_html__( '%s Distribution', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( STM_LMS_Point_System::get_label() )
				);
				?>
			</h1>
			<div class="masterstudy-account-points-distribution__table">
				<div class="masterstudy-account-points-distribution__table-header">
					<div class="masterstudy-account-points-distribution__table-header-title">
						<?php echo esc_html__( 'Name', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
					<div class="masterstudy-account-points-distribution__table-header-title">
						<?php echo esc_html__( 'Description', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
					<div class="masterstudy-account-points-distribution__table-header-title">
						<?php echo esc_html( STM_LMS_Point_System::get_label() ); ?>
					</div>
				</div>
				<div class="masterstudy-account-points-distribution__table-body">
					<?php
					foreach ( $points as $point ) :
						if ( empty( $point['score'] ) ) {
							continue;
						}
						?>
						<div class="masterstudy-account-points-distribution__card">
							<div class="masterstudy-account-points-distribution__card-block">
								<?php echo esc_html( $point['label'] ); ?>
							</div>
							<div class="masterstudy-account-points-distribution__card-block">
								<?php
								if ( ! empty( $point['description'] ) ) {
									echo esc_html( $point['description'] );
								}
								?>
							</div>
							<div class="masterstudy-account-points-distribution__card-block">
								<?php echo esc_html( $point['score'] ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $current_user ); ?>
