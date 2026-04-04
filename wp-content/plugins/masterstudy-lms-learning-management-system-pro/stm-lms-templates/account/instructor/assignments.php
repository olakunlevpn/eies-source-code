<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	STM_LMS_User::js_redirect( STM_LMS_User::login_page_url() );
}

$lms_current_user = (array) STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_style( 'masterstudy-account-instructor-assignments-table' );
wp_enqueue_script( 'masterstudy-account-instructor-assignments-table' );
wp_enqueue_style( 'masterstudy-loader' );

$theads = array(
	'title'      => array(
		'title'    => __( 'Assignment', 'masterstudy-lms-learning-management-system-pro' ),
		'position' => 'start',
		'grow'     => 'masterstudy-tcell_is-grow-md',
		'hidden'   => false,
	),
	'total'      => array(
		'title'    => __( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
		'position' => 'center',
		'sort'     => 'total',
		'hidden'   => false,
	),
	'passed'     => array(
		'title'    => __( 'Passed', 'masterstudy-lms-learning-management-system-pro' ),
		'position' => 'center',
		'sort'     => 'passed',
		'hidden'   => false,
	),
	'not_passed' => array(
		'title'    => __( 'Non passed', 'masterstudy-lms-learning-management-system-pro' ),
		'position' => 'center',
		'sort'     => 'not_passed',
		'hidden'   => false,
	),
	'pending'    => array(
		'title'    => __( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
		'position' => 'center',
		'sort'     => 'pending',
		'hidden'   => false,
	),
);
?>

<div class="masterstudy-account">
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
		<div class="masterstudy-account-instructor-assignments">
			<div class="masterstudy-table">
				<div class="masterstudy-table__toolbar">
					<div class="masterstudy-table__toolbar-header">
						<h3 class="masterstudy-table__title">
							<?php echo esc_html__( 'Student Assignments', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</h3>
					</div>

					<div class="masterstudy-table__filters">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/search',
							array(
								'select_name'  => 's',
								'is_queryable' => false,
								'placeholder'  => esc_html__( 'Search assignment...', 'masterstudy-lms-learning-management-system-pro' ),
							)
						);
						STM_LMS_Templates::show_lms_template(
							'components/selects/instructor-courses-select',
							array(
								'select_id'      => 'masterstudy-account-instructor-assignments__course-select-input',
								'select_options' => array(
									'placeholder' => esc_html__( 'Select course', 'masterstudy-lms-learning-management-system-pro' ),
									'clearable'   => true,
								),
							)
						);
						STM_LMS_Templates::show_lms_template(
							'components/select',
							array(
								'select_name'  => 'status',
								'placeholder'  => esc_html__( 'Status: Show All', 'masterstudy-lms-learning-management-system-pro' ),
								'select_width' => '160px',
								'is_queryable' => false,
								'options'      => array(
									'pending'    => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
									'passed'     => esc_html__( 'Passed', 'masterstudy-lms-learning-management-system-pro' ),
									'not_passed' => esc_html__( 'Non-passed', 'masterstudy-lms-learning-management-system-pro' ),
								),
							)
						);
						?>
					</div>
				</div>

				<div class="masterstudy-table__wrapper">
					<div class="masterstudy-thead">
						<?php foreach ( $theads as $thead ) : ?>
							<?php
							if ( isset( $thead['hidden'] ) && $thead['hidden'] ) {
								continue;
							}
							?>
							<div class="masterstudy-tcell masterstudy-tcell_is-<?php echo esc_attr( ( $thead['position'] ?? 'center' ) . ' ' . ( $thead['grow'] ?? '' ) ); ?>">
								<div class="masterstudy-tcell__header" data-sort="<?php echo esc_attr( $thead['sort'] ?? 'none' ); ?>">
									<span class="masterstudy-tcell__title"><?php echo esc_html( $thead['title'] ?? '' ); ?></span>
									<?php
									if ( isset( $thead['sort'] ) ) {
										STM_LMS_Templates::show_lms_template( 'components/sort-indicator' );
									}
									?>
								</div>
							</div>
						<?php endforeach; ?>
						<div class="masterstudy-tcell masterstudy-tcell_is-center masterstudy-tcell_is-hidden-md"></div>
					</div>

					<div class="masterstudy-tbody">
						<div class="masterstudy-table__item masterstudy-table__item--hidden masterstudy-table__item--clone">
							<div class="masterstudy-tcell masterstudy-tcell_is-grow-md" data-th="<?php echo esc_html__( 'Assigment', 'masterstudy-lms-learning-management-system-pro' ); ?>:" data-th-inlined="false">
								<div class="masterstudy-tcell__title masterstudy-tcell__data" data-key="title" data-value="">
									<span class="masterstudy-tcell__title-value"></span>
									<ul class="masterstudy-table__list">
										<li class="masterstudy-table__list-no-course">
											<?php echo esc_html__( 'No linked courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
										</li>
									</ul>
								</div>
							</div>
							<div class="masterstudy-tcell masterstudy-tcell__courses masterstudy-tcell_is-grow" data-th="<?php echo esc_html__( 'In course', 'masterstudy-lms-learning-management-system-pro' ); ?>:" data-th-inlined="false">
								<ul class="masterstudy-table__list  masterstudy-tcell__data" data-key="courses" data-value="">
									<li class="masterstudy-table__list-no-course">
										<?php echo esc_html__( 'No linked courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</li>
								</ul>
							</div>
							<div class="masterstudy-tcell masterstudy-tcell_is-center masterstudy-tcell_is-sm-space-between masterstudy-tcell_is-sm-border-bottom" data-th="<?php echo esc_html( $theads['total']['title'] ?? '' ); ?>:"  data-th-inlined="true">
								<span class="masterstudy-tcell__cell-title"><?php echo esc_html( $theads['total']['title'] ?? '' ); ?>:&nbsp;</span>
								<span class="masterstudy-tcell__data masterstudy-tcell__cell-value" data-key="total" data-value=""></span>
							</div>
							<div class="masterstudy-tcell masterstudy-tcell_is-center masterstudy-tcell_is-sm-space-between masterstudy-tcell_is-sm-border-bottom" data-th="<?php echo esc_html( $theads['passed']['title'] ?? '' ); ?>:"   data-th-inlined="true">
								<div class="masterstudy-tcell__icon-title">
									<span class="stmlms-check"></span>
									<span class="masterstudy-tcell__cell-title"><?php echo esc_html( $theads['passed']['title'] ?? '' ); ?>:&nbsp;</span>
								</div>
								<span class="masterstudy-tcell__data masterstudy-tcell__cell-value" data-key="passed"  data-value=""></span>
							</div>
							<div class="masterstudy-tcell masterstudy-tcell_is-center masterstudy-tcell_is-sm-space-between masterstudy-tcell_is-sm-border-bottom" data-th="<?php echo esc_html( $theads['not_passed']['title'] ?? '' ); ?>:" data-th-inlined="true">
								<div class="masterstudy-tcell__icon-title">
									<span class="stmlms-cancel"></span>
									<span class="masterstudy-tcell__cell-title"><?php echo esc_html( $theads['not_passed']['title'] ?? '' ); ?>:&nbsp;</span>
								</div>
								<span class="masterstudy-tcell__data masterstudy-tcell__cell-value" data-key="not_passed" data-value=""></span>
							</div>
							<div class="masterstudy-tcell masterstudy-tcell_is-center masterstudy-tcell_is-sm-space-between" data-th="<?php echo esc_html( $theads['pending']['title'] ?? '' ); ?>:" data-th-inlined="true">
								<div class="masterstudy-tcell__icon-title">
									<span class="stmlms-clock-filled"></span>
									<span class="masterstudy-tcell__cell-title"><?php echo esc_html( $theads['pending']['title'] ?? '' ); ?>:&nbsp;</span>
								</div>
								<span class="masterstudy-tcell__data masterstudy-tcell__cell-value" data-key="pending" data-value=""></span>
							</div>
							<div class="masterstudy-tcell masterstudy-tcell_is-hidden-md">
								<span class="masterstudy-table__component masterstudy-tcell__data" data-key="more_link">
								<?php
									STM_LMS_Templates::show_lms_template(
										'components/button',
										array(
											'title'     => esc_html__( 'View', 'masterstudy-lms-learning-management-system-pro' ),
											'style'     => 'secondary',
											'size'      => 'sm',
											'link'      => '',
											'id'        => 'more-link',
											'icon_position' => '',
											'icon_name' => '',
										)
									);
									?>
								</span>
							</div>
						</div>
						<div class="masterstudy-table__item masterstudy-table__item--hidden masterstudy-table__item--clone">
							<div class="masterstudy-tcell masterstudy-tcell_is-empty">
								<?php echo esc_html__( 'No Assignments found.', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
						</div>
					</div>
					<div class="masterstudy-tfooter masterstudy-tfooter--hidden">
						<div class="masterstudy-tcell masterstudy-tcell_is-space-between">
							<span>
								<?php
									STM_LMS_Templates::show_lms_template(
										'components/pagination',
										array(
											'max_visible_pages' => 3,
											'total_pages'  => 1,
											'dark_mode'    => false,
											'current_page' => 1,
											'is_queryable' => false,
											'done_indicator' => false,
											'is_ajax'      => true,
										)
									);
									?>
							</span>
						</div>
						<div class="masterstudy-tcell masterstudy-tcell_is-space-between">
							<span>
								<?php
								STM_LMS_Templates::show_lms_template(
									'components/select',
									array(
										'select_id'    => 'assignments-per-page',
										'select_width' => '170px',
										'select_name'  => 'per_page',
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
							</span>
						</div>
					</div>
				</div>
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
		</div>
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/no-records',
			array(
				'title_items'     => esc_html__( 'No submitted assignments yet', 'masterstudy-lms-learning-management-system-pro' ),
				'container_class' => 'masterstudy-account-instructor-assignments-no-found__info masterstudy-account-utility_hidden',
				'icon'            => 'stmlms-assignment-not-found',
			)
		);
		?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
