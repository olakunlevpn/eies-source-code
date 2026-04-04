<?php
wp_enqueue_script( 'moment.min', STM_LMS_URL . '/assets/vendors/moment.min.js', '', STM_LMS_PRO_VERSION, true );
wp_enqueue_script( 'chart.min', STM_LMS_URL . '/assets/vendors/chart.min.js', '', STM_LMS_PRO_VERSION, true );
wp_enqueue_script( 'masterstudy-account-payout-statistics' );

wp_enqueue_style( 'masterstudy-datepicker' );
wp_enqueue_style( 'masterstudy-account-payout-statistics' );

$orders_table_columns = array(
	array(
		'title' => '№',
		'data'  => 'number',
	),
	array(
		'title' => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'date_created',
		'name'  => 'date_created',
	),
	array(
		'title' => esc_html__( 'Payout method', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'payment_code',
		'name'  => 'payment_code',
	),
	array(
		'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'status',
		'name'  => 'status',
	),
	array(
		'title' => esc_html__( 'Amount', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'amount',
	),
);

wp_localize_script(
	'masterstudy-account-payout-statistics',
	'masterstudy_lms_statistics_data',
	array(
		'order_columns' => $orders_table_columns,
		'rest_url'      => esc_url_raw( rest_url( '' ) ),
		'stats_data'    => array(
			'custom_period' => __( 'Date range', 'masterstudy-lms-learning-management-system' ),
			'locale'        => masterstudy_get_locale_info(),
		),
	)
);

wp_localize_script(
	'masterstudy-api-provider',
	'api_data',
	array(
		'rest_url' => esc_url_raw( rest_url( '' ) ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
	)
);
?>

<div class="masterstudy-account-statistics">
	<div class="masterstudy-account-statistics__wrapper">
		<div class="masterstudy-account-statistics__header">
			<div class="masterstudy-account-statistics__message masterstudy-account-statistics__paypal-email-response masterstudy-account-utility_hidden"></div>
			<div class="masterstudy-account-statistics__header-content">
				<span class="masterstudy-account-statistics__title"><?php esc_html_e( 'Payouts', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				<div class="masterstudy-account-statistics__actions">
					<div class="masterstudy-account-statistics__input-wrapper">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/input',
							array(
								'input_class' => 'masterstudy-account-statistics__email-input',
								'placeholder' => esc_html__( 'Paypal Email', 'masterstudy-lms-learning-management-system-pro' ),
								'input_value' => get_user_meta( get_current_user_id(), 'stm_lms_paypal_email', true ),
							)
						);
						?>
					</div>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'Save', 'masterstudy-lms-learning-management-system-pro' ),
							'style' => 'primary',
							'size'  => 'sm',
							'id'    => 'save-email',
							'class' => 'masterstudy-account-statistics__save-button payout-save-email',
						)
					);
					?>
				</div>
			</div>
		</div>

		<div class="masterstudy-account-statistics__revenue">
			<div class="masterstudy-account-statistics__revenue-header">
				<span class="masterstudy-account-statistics__revenue-title">
					<?php esc_html_e( 'Revenue', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
				<div class="masterstudy-account-statistics__revenue-product-select">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/selects/instructor-courses-select',
						array(
							'select_id'      => 'masterstudy-account-statistics__revenue-select-input',
							'select_options' => array(
								'placeholder' => esc_html__( 'All products', 'masterstudy-lms-learning-management-system-pro' ),
								'clearable'   => true,
							),
						)
					);
					?>
				</div>
				<div class="masterstudy-account-statistics__revenue-date-select">
					<div class="stm-datepicker">
						<?php STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' ); ?>
						<?php STM_LMS_Templates::show_lms_template( 'components/analytics/datepicker-modal', array( 'id' => 'stm-account-statistics' ) ); ?>
					</div>
				</div>
				<span class="masterstudy-account-statistics__revenue-total-price">
				</span>
			</div>

			<div class="masterstudy-account-statistics__charts ">
				<div class="masterstudy-account-statistics__revenue-chart">
					<div class="masterstudy-account-statistics__revenue-chart-wrapper masterstudy-account-utility_hidden">
						<canvas id="line_chart_id" height="475" class="masterstudy-account-statistics__chart"></canvas>
					</div>
					<div class="masterstudy-account-statistics__revenue-loader">
						<?php STM_LMS_Templates::show_lms_template( 'components/analytics/loader', array( 'loader_type' => 'line-chart-loader' ) ); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="masterstudy-account-statistics__orders">
			<div class="masterstudy-account-statistics__orders-header">
				<div class="masterstudy-account-statistics__orders-header-title">
					<span class="masterstudy-account-statistics__orders-header-title-text"><?php esc_html_e( 'Payouts', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
					<span class="masterstudy-account-statistics__orders-header-title-total"></span>
				</div>

				<div class="masterstudy-account-statistics__orders-header-filters">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_name'  => 'status',
							'placeholder'  => esc_html__( 'Status: All', 'masterstudy-lms-learning-management-system-pro' ),
							'select_width' => '160px',
							'is_queryable' => false,
							'options'      => array(
								'pending'   => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
								'completed' => esc_html__( 'Completed', 'masterstudy-lms-learning-management-system-pro' ),
								'canceled'  => esc_html__( 'Canceled', 'masterstudy-lms-learning-management-system-pro' ),
							),
						)
					);
					?>
				</div>
			</div>
			<div class="masterstudy-account-statistics__orders-table">
				<?php
				STM_LMS_Templates::show_lms_template( 'components/skeleton-loader', array( 'loader_type' => 'table-loader' ) );
				STM_LMS_Templates::show_lms_template(
					'components/analytics/datatable',
					array(
						'id'      => 'orders',
						'columns' => $orders_table_columns,
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
