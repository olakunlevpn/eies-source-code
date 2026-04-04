<?php
/**
 * @var $assignment_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

$status_options = array(
	'pending'    => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
	'not_passed' => esc_html__( 'Declined', 'masterstudy-lms-learning-management-system-pro' ),
	'passed'     => esc_html__( 'Approved', 'masterstudy-lms-learning-management-system-pro' ),
);

wp_enqueue_style( 'masterstudy-account-main' );
wp_enqueue_style( 'masterstudy-account-enrolled-assignments' );
wp_enqueue_style( 'masterstudy-pagination' );
wp_enqueue_style( 'masterstudy-loader' );

wp_enqueue_script( 'masterstudy-pagination-utils' );
wp_enqueue_script( 'masterstudy-account-enrolled-assignments' );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-enrolled-assignments">
			<div class="masterstudy-account-enrolled-assignments__header">
				<span class="masterstudy-account-enrolled-assignments__header-title"><?php esc_html_e( 'Assignments', 'masterstudy-lms-learning-management-system-pro' ); ?></span>

				<div class="masterstudy-account-enrolled-assignments__header-filters">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/search',
						array(
							'search_name' => 'masterstudy-account-enrolled-assignments__header-search-input',
						)
					);
					?>

					<div class="masterstudy-account-enrolled-assignments__header-status-select-container">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/select',
							array(
								'select_name' => 'masterstudy-account-enrolled-assignments__header-status-select',
								'options'     => $status_options,
								'placeholder' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
							)
						);
						?>
					</div>
				</div>
			</div>
			<template id="masterstudy-account-enrolled-assignments__item">
				<a href="#" class="masterstudy-account-enrolled-assignments__item">
					<span class="masterstudy-account-enrolled-assignments__item-title"></span>

					<div class="masterstudy-account-enrolled-assignments__item-content">
						<div class="masterstudy-account-enrolled-assignments__item-course">
							<span class="masterstudy-account-enrolled-assignments__item-course-title"><?php echo esc_html__( 'Course', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
							<span class="masterstudy-account-enrolled-assignments__item-course-value"></span>
						</div>

						<div class="masterstudy-account-enrolled-assignments__item-teacher">
							<img class="masterstudy-account-enrolled-assignments__item-teacher-img" src="#"
								alt="teacher"/>
							<div class="masterstudy-account-enrolled-assignments__item-teacher-info">
								<span class="masterstudy-account-enrolled-assignments__item-teacher-info-title"><?php echo esc_html__( 'Teacher', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
								<span class="masterstudy-account-enrolled-assignments__item-teacher-info-value"></span>
							</div>
						</div>

						<div class="masterstudy-account-enrolled-assignments__item-last-update">
							<span class="masterstudy-account-enrolled-assignments__item-last-update-title"><?php echo esc_html__( 'Last update', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
							<span class="masterstudy-account-enrolled-assignments__item-last-update-value"></span>
						</div>

						<div class="masterstudy-account-enrolled-assignments__item-status">
							<span class="masterstudy-account-enrolled-assignments__item-status-title"><?php echo esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
							<span class="masterstudy-account-enrolled-assignments__item-status-value"></span>
						</div>

						<?php if ( is_ms_lms_addon_enabled( 'grades' ) ) : ?>
							<div class="masterstudy-account-enrolled-assignments__item-grade">
								<span class="masterstudy-account-enrolled-assignments__item-grade-score"></span>
								<div class="masterstudy-account-enrolled-assignments__item-grade-container">
									<span class="masterstudy-account-enrolled-assignments__item-grade-value"></span>
									<span class="masterstudy-account-enrolled-assignments__item-grade-percent-value"></span>
								</div>
							</div>
						<?php endif ?>
					</div>
				</a>
			</template>

			<div class="masterstudy-account-enrolled-assignments__items">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/loader',
					array(
						'dark_mode' => false,
						'is_local'  => true,
					)
				);
				?>
			</div>

			<div class="masterstudy-account-enrolled-assignments__navigation">
				<div class="masterstudy-account-enrolled-assignments__pagination"></div>
				<div class="masterstudy-account-enrolled-assignments__per-page">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'    => 'masterstudy-account-enrolled-assignments__per-page-select',
							'select_width' => '170px',
							'select_name'  => 'masterstudy-account-enrolled-assignments__per-page-select',
							'placeholder'  => esc_html__( '10 per page', 'masterstudy-lms-learning-management-system-pro' ),
							'default'      => 10,
							'is_queryable' => false,
							'options'      => array(
								'25'  => esc_html__( '25 per page', 'masterstudy-lms-learning-management-system-pro' ),
								'50'  => esc_html__( '50 per page', 'masterstudy-lms-learning-management-system-pro' ),
								'75'  => esc_html__( '75 per page', 'masterstudy-lms-learning-management-system-pro' ),
								'100' => esc_html__( '100 per page', 'masterstudy-lms-learning-management-system-pro' ),
							),
						)
					);
					?>
				</div>
			</div>

			<template id="masterstudy-account-enrolled-assignments-no-found-template">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/no-records',
					array(
						'title_items'     => esc_html__( 'No submitted assignments yet', 'masterstudy-lms-learning-management-system-pro' ),
						'title_search'    => esc_html__( 'No assignments match your search', 'masterstudy-lms-learning-management-system-pro' ),
						'container_class' => 'masterstudy-account-enrolled-assignments-no-found__info',
						'icon'            => 'stmlms-assignment-not-found',
					)
				);
				?>
			</template>
		</div>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
