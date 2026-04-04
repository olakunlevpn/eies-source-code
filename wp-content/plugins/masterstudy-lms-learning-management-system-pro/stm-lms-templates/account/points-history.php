<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-account-main' );
wp_enqueue_style( 'masterstudy-account-points' );
wp_enqueue_script( 'masterstudy-account-points' );

stm_lms_completed_points( $lms_current_user['id'] );

$points = STM_LMS_Point_History::get_user_points( $lms_current_user['id'] );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-points__header">
			<h1 class="masterstudy-points__title">
				<?php
				printf(
					/* translators: %s Points Label */
					esc_html__( 'My %s', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( STM_LMS_Point_System::get_label() )
				);
				?>
			</h1>
			<div class="masterstudy-points__banner">
				<div class="masterstudy-points__current">
					<span class="masterstudy-points__current-value">
						<?php
						echo wp_kses_post( STM_LMS_Point_System::display_point_image() );
						echo esc_html( STM_LMS_Point_System::total_points( $lms_current_user['id'] ) );
						?>
					</span>
					<span class="masterstudy-points__current-label">
						<?php
						printf(
							/* translators: %s Points Label */
							esc_html__( '%s on your balance', 'masterstudy-lms-learning-management-system-pro' ),
							esc_html( STM_LMS_Point_System::get_label() )
						);
						?>
					</span>
				</div>
				<a class="masterstudy-points__get-link" href="<?php echo esc_url( ms_plugin_user_account_url( 'points-distribution' ) ); ?>">
					<?php echo esc_html__( 'How to get more?', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</a>
			</div>
		</div>
		<div class="masterstudy-points__columns">
			<div class="masterstudy-points__columns-title">
				<?php echo esc_html__( 'Event', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-points__columns-title">
				<?php echo esc_html__( 'Origin', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-points__columns-title">
				<?php echo esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-points__columns-title">
				<?php echo esc_html( STM_LMS_Point_System::get_label() ); ?>
			</div>
		</div>
		<div class="masterstudy-points">
			<div class="masterstudy-points__list">
				<?php
				if ( ! empty( $points['posts'] ) ) :
					foreach ( $points['posts'] as $point ) :
						STM_LMS_Templates::show_lms_template( 'points/card', array( 'point' => $point ) );
					endforeach;
				endif;
				?>
			</div>

			<div class="masterstudy-points__pagination">
				<?php
				if ( ! empty( $points['posts'] ) && $points['total_pages'] > 1 ) {
					STM_LMS_Templates::show_lms_template(
						'components/pagination',
						array(
							'max_visible_pages' => 5,
							'total_pages'       => $points['total_pages'],
							'current_page'      => 1,
							'dark_mode'         => false,
							'is_queryable'      => false,
							'done_indicator'    => false,
							'is_api'            => true,
							'thin'              => true,
						)
					);
				}
				?>
			</div>

			<div class="masterstudy-points__loader">
				<div class="masterstudy-points__loader-body"></div>
			</div>

			<div class="masterstudy-points__empty <?php echo esc_attr( empty( $points['posts'] ) ? 'masterstudy-points__empty_show' : '' ); ?>">
				<div class="masterstudy-points__empty-block">
					<span class="masterstudy-points__empty-icon"></span>
					<span class="masterstudy-points__empty-text">
						<?php
						printf(
							/* translators: %s Points Label */
							esc_html__( 'No %s yet', 'masterstudy-lms-learning-management-system-pro' ),
							esc_html( STM_LMS_Point_System::get_label() )
						);
						?>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
