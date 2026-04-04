<?php
$is_user_account = ! is_admin();

if ( $is_user_account ) {
	$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

	wp_enqueue_style( 'masterstudy-account-main' );

	do_action( 'stm_lms_template_main' );
	do_action( 'masterstudy_before_account', $lms_current_user );
}

wp_enqueue_style( 'masterstudy-analytics-bundle-page' );
wp_enqueue_style( 'masterstudy-analytics-components' );
wp_enqueue_script( 'masterstudy-analytics-bundle-page' );

$courses_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'number',
	),
	array(
		'title' => esc_html__( 'Course name', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'course_name',
	),
	array(
		'title' => esc_html__( 'Enrollments', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'enrollments',
	),
	array(
		'title' => esc_html__( 'Course creation date', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'date_created',
	),
	array(
		'title' => '',
		'data'  => 'course_id',
	),
);

wp_localize_script(
	'masterstudy-analytics-bundle-page',
	'bundle_page_data',
	array(
		'courses'          => $courses_columns,
		'user_account_url' => STM_LMS_User::login_page_url() . 'analytics/',
		'titles'           => array(
			'revenue_chart' => esc_html__( 'Revenue', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);

$stats_types = array(
	'revenue',
	'orders',
);

$tables_data = array(
	array(
		'title' => esc_html__( 'Bundle contents', 'masterstudy-lms-learning-management-system-pro' ),
		'id'    => 'courses-table',
	),
);

$bundle_title = isset( $_GET['bundle_id'] ) ? esc_html( get_the_title( intval( $_GET['bundle_id'] ) ) ) : esc_html__( 'Bundle', 'masterstudy-lms-learning-management-system-pro' );

if ( $is_user_account ) {
	$previous_page = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : STM_LMS_User::login_page_url() . 'analytics/';
	$current_url   = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : STM_LMS_User::login_page_url() . 'analytics/';
	$url_parts     = explode( '/', trim( $current_url, '/' ) );
	$bundle_key    = array_search( 'bundle', $url_parts, true );
	if ( false !== $bundle_key && isset( $url_parts[ $bundle_key + 1 ] ) ) {
		$bundle_id    = intval( $url_parts[ $bundle_key + 1 ] );
		$bundle_title = get_the_title( intval( $bundle_id ) );
	}
	?>
	<div class="masterstudy-account">
		<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
		<div class="masterstudy-account-sidebar">
			<div class="masterstudy-account-sidebar__wrapper">
				<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
			</div>
		</div>
		<div class="masterstudy-account-container">
<?php } ?>
			<div class="masterstudy-analytics-bundle-page">
				<div class="masterstudy-analytics-bundle-page__header">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/back-link',
						array(
							'id'  => 'bundle',
							'url' => $is_user_account ? $previous_page : masterstudy_get_current_url( array( 'bundle_id' ) ),
						)
					);
					?>
					<h1 class="masterstudy-analytics-bundle-page__title">
						<?php echo esc_html( $bundle_title ); ?>
					</h1>
					<?php STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' ); ?>
				</div>
				<?php if ( $is_user_account ) { ?>
					<div class="masterstudy-analytics-bundle-page__separator">
						<span class="masterstudy-analytics-bundle-page__separator-short"></span>
						<span class="masterstudy-analytics-bundle-page__separator-long"></span>
					</div>
					<?php
				}
				STM_LMS_Templates::show_lms_template(
					'components/analytics/datepicker-modal',
					array(
						'id' => 'bundle',
					)
				);
				?>
				<div class="masterstudy-analytics-bundle-page-stats">
					<div class="masterstudy-analytics-bundle-page-stats__wrapper">
						<?php foreach ( $stats_types as $item ) { ?>
							<div class="masterstudy-analytics-bundle-page-stats__block" data-id="<?php echo esc_attr( $item ); ?>">
								<?php
								STM_LMS_Templates::show_lms_template( 'components/analytics/loader', array( 'loader_type' => 'data-loader' ) );
								STM_LMS_Templates::show_lms_template(
									'components/analytics/stats-block',
									array(
										'type' => $item,
									)
								);
								?>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="masterstudy-analytics-bundle-page__row">
					<div class="masterstudy-analytics-bundle-page-line" data-chart-id="revenue-chart">
						<div class="masterstudy-analytics-bundle-page-line__wrapper">
							<div class="masterstudy-analytics-bundle-page-line__content">
								<?php STM_LMS_Templates::show_lms_template( 'components/analytics/loader', array( 'loader_type' => 'line-chart-loader' ) ); ?>
								<div class="masterstudy-analytics-bundle-page-line__header">
									<h2 class="masterstudy-analytics-bundle-page-line__title">
										<?php echo esc_html__( 'Revenue', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</h2>
									<div id="revenue-total" class="masterstudy-analytics-bundle-page-line__single-total"></div>
								</div>
								<div class="masterstudy-analytics-bundle-page-line__chart">
									<?php
									STM_LMS_Templates::show_lms_template(
										'components/analytics/line-chart',
										array(
											'id' => 'revenue',
										)
									);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="masterstudy-analytics-bundle-page-table" data-chart-id="courses-table">
					<?php STM_LMS_Templates::show_lms_template( 'components/analytics/loader', array( 'loader_type' => 'table-loader' ) ); ?>
					<div class="masterstudy-analytics-bundle-page-table__wrapper">
						<div class="masterstudy-analytics-bundle-page-table__header">
							<div class="masterstudy-analytics-bundle-page-table__title">
								<?php echo esc_html__( 'Bundle contents', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
							<input type="text" id="table-courses-search" class="masterstudy-analytics-bundle-page-table__search" placeholder="<?php echo esc_html__( 'Search', 'masterstudy-lms-learning-management-system-pro' ); ?>">
						</div>
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/analytics/datatable',
							array(
								'id'      => 'courses',
								'columns' => $courses_columns,
							)
						);
						?>
					</div>
				</div>
			</div>
<?php
if ( $is_user_account ) {
	?>
		</div>
	</div>
	<?php
	do_action( 'masterstudy_after_account', $lms_current_user );
}
