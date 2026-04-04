<?php
STM_LMS_Templates::show_lms_template( 'header' );

wp_enqueue_style( 'masterstudy-analytics-components' );
wp_enqueue_style( 'masterstudy-analytics-sales-page' );
wp_enqueue_script( 'masterstudy-analytics-sales-page' );
wp_enqueue_script( 'masterstudy-instructor-subscriptions-page' );

$order_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'order_id',
	),
	array(
		'title' => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'date',
	),
	array(
		'title' => esc_html__( 'User', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'user_info',
	),
	array(
		'title' => esc_html__( 'Order items', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'total_items',
	),
	array(
		'title' => esc_html__( 'Payment method', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'payment_code',
	),
	array(
		'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'status_name',
	),
	array(
		'title' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'total_price',
	),
	array(
		'title' => '',
		'data'  => 'order_id',
	),
);

$subscription_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'subscription_id',
	),
	array(
		'title' => esc_html__( 'Plan', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'plan_name',
	),
	array(
		'title' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'amount',
	),
	array(
		'title' => esc_html__( 'User', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'user_info',
	),
	array(
		'title' => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'start_date',
	),
	array(
		'title' => esc_html__( 'Auto Renewal', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'next_payment_date',
	),
	array(
		'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'status',
	),
	array(
		'title' => '',
		'data'  => 'subscription_id',
	),
);

wp_localize_script(
	'masterstudy-analytics-sales-page',
	'users_page_data',
	array(
		'instructor-orders'          => $order_columns,
		'instructor-subscriptions'   => $subscription_columns,
		'titles'                     => array(
			'instructors_chart' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
			'users_chart'       => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
		),
		'statuses'                   => array(
			'completed' => esc_html__( 'Completed', 'masterstudy-lms-learning-management-system-pro' ),
			'pending'   => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
			'cancelled' => esc_html__( 'Cancelled', 'masterstudy-lms-learning-management-system-pro' ),
		),
		'payment_code_wire_transfer' => esc_html__( 'Wire Transfer', 'masterstudy-lms-learning-management-system-pro' ),
		'payment_code_cash'          => esc_html__( 'Cash', 'masterstudy-lms-learning-management-system-pro' ),
	)
);

wp_localize_script(
	'masterstudy-instructor-subscriptions-page',
	'subscriptions_page_data',
	array(
		'instructor-subscriptions' => $subscription_columns,
		'statuses'                 => array(
			'active'    => esc_html__( 'Active', 'masterstudy-lms-learning-management-system-pro' ),
			'cancelled' => esc_html__( 'Cancelled', 'masterstudy-lms-learning-management-system-pro' ),
			'expired'   => esc_html__( 'Expired', 'masterstudy-lms-learning-management-system-pro' ),
			'pending'   => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);

do_action( 'stm_lms_template_main' );
?>
	<div class="stm-lms-wrapper user-account-page">
		<div class="container">
			<?php do_action( 'stm_lms_admin_after_wrapper_start', STM_LMS_User::get_current_user() ); ?>
			<div class="masterstudy-analytics-sales-page__header">
				<h1 class="masterstudy-analytics-sales-page__title">
					<?php echo esc_html__( 'My Sales', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</h1>
				<?php STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' ); ?>
			</div>

			<div class="masterstudy-analytics-table__tabs">
				<ul class="masterstudy-tabs masterstudy-tabs_style-default">
					<li class="masterstudy-tabs__item masterstudy-tabs__item_active sales-table" data-tab="sales">
						<?php echo esc_html__( 'Sales', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</li>
					<li class="masterstudy-tabs__item" data-tab="subscriptions">
						<?php echo esc_html__( 'Subscriptions', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</li>
				</ul>
			</div>


			<div class="masterstudy-analytics-tab-content active" data-tab="sales">
				<div class="masterstudy-analytics-sales-page-table" data-chart-id="instructor-orders-table">
					<?php STM_LMS_Templates::show_lms_template( 'components/analytics/loader', array( 'loader_type' => 'table-loader' ) ); ?>
					<div class="masterstudy-analytics-sales-page-table__wrapper">
						<div class="masterstudy-analytics-sales-page-table__header">
							<div class="masterstudy-analytics-sales-page-table__title">
								<?php echo esc_html__( 'Sales', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
							<div class="masterstudy-analytics-sales-page-table__top_panel">
								<div class="masterstudy-analytics-sales-page-table__search-wrapper">
									<input type="text" id="table-sales-search"
										class="masterstudy-analytics-sales-page-table__search"
										placeholder="<?php echo esc_html__( 'Search by email, student, order id', 'masterstudy-lms-learning-management-system-pro' ); ?>">
									<span class="masterstudy-analytics-sales-page-table__search-icon"></span>
								</div>
							</div>
						</div>
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/analytics/datatable',
							array(
								'id'      => 'instructor-orders',
								'columns' => $order_columns,
							)
						);
						?>
					</div>
				</div>
			</div>

			<div class="masterstudy-analytics-tab-content" data-tab="subscriptions">
				<div class="masterstudy-analytics-sales-page-table" data-chart-id="instructor-subscriptions-table">
					<?php STM_LMS_Templates::show_lms_template( 'components/analytics/loader', array( 'loader_type' => 'table-loader' ) ); ?>
					<div class="masterstudy-analytics-sales-page-table__wrapper">
						<div class="masterstudy-analytics-sales-page-table__header">
							<div class="masterstudy-analytics-sales-page-table__title">
								<?php echo esc_html__( 'Subscriptions', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
							<div class="masterstudy-analytics-sales-page-table__search-wrapper">
								<input type="text" id="table-subscriptions-search"
									class="masterstudy-analytics-sales-page-table__search"
									placeholder="<?php echo esc_html__( 'Search by email, user, subscription id', 'masterstudy-lms-learning-management-system-pro' ); ?>">
								<span class="masterstudy-analytics-sales-page-table__search-icon"></span>
							</div>
						</div>
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/analytics/datatable',
							array(
								'id'      => 'instructor-subscriptions',
								'columns' => $subscription_columns,
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
STM_LMS_Templates::show_lms_template(
	'components/analytics/datepicker-modal',
	array(
		'id' => 'global-datepicker',
	)
);
?>
<?php
STM_LMS_Templates::show_lms_template( 'footer' );
