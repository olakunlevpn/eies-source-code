<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

wp_enqueue_style( 'masterstudy-account-main' );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-tabs' );
wp_enqueue_style( 'masterstudy-my-sales-page' );
wp_enqueue_script( 'masterstudy-my-sales-page' );

$is_admin             = current_user_can( 'manage_options' );
$taxes_display        = masterstudy_lms_taxes_display();
$subs_for_instructors = STM_LMS_Options::get_option( 'allow_instructor_subscription', false ) || current_user_can( 'manage_options' );
$coupon_enabled       = is_ms_lms_coupons_enabled();

$order_columns = array(
	array(
		'title' => esc_html__( 'ID', 'masterstudy-lms-learning-management-system-pro' ),
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
);

if ( $taxes_display['enabled'] || $coupon_enabled ) {
	$order_columns[] = array(
		'title' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'subtotal',
	);
}

if ( $coupon_enabled ) {
	$order_columns[] = array(
		'title' => esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'coupon_value',
	);
}

if ( $taxes_display['enabled'] ) {
	$order_columns[] = array(
		'title' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'taxes',
	);
}

$order_columns[] = array(
	'title' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'total_price',
);
$order_columns[] = array(
	'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'status',
);
$order_columns[] = array(
	'title' => '',
	'data'  => 'order_id',
);

$memberships_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'number',
	),
	array(
		'title' => esc_html__( 'Plan Title', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'plan_name',
	),
);

if ( $taxes_display['enabled'] ) {
	$memberships_columns[] = array(
		'title' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'subtotal',
	);
	$memberships_columns[] = array(
		'title' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'taxes',
	);
}

$memberships_columns[] = array(
	'title' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'total',
);
$memberships_columns[] = array(
	'title' => esc_html__( 'User', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'user_info',
);
$memberships_columns[] = array(
	'title' => esc_html__( 'Date of Issue', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'start_date',
);
$memberships_columns[] = array(
	'title' => esc_html__( 'Next Renewal', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'next_payment_date',
);
$memberships_columns[] = array(
	'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'status',
);
$memberships_columns[] = array(
	'title' => '',
	'data'  => 'actions',
);

$subscriptions_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'number',
	),
	array(
		'title' => esc_html__( 'Plan Title', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'plan_name',
	),
);

if ( $taxes_display['enabled'] ) {
	$subscriptions_columns[] = array(
		'title' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'subtotal',
	);
	$subscriptions_columns[] = array(
		'title' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'taxes',
	);
}

$subscriptions_columns[] = array(
	'title' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'total',
);
$subscriptions_columns[] = array(
	'title' => esc_html__( 'User', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'user_info',
);
$subscriptions_columns[] = array(
	'title' => esc_html__( 'Date of Issue', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'start_date',
);
$subscriptions_columns[] = array(
	'title' => esc_html__( 'Next Renewal', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'next_payment_date',
);
$subscriptions_columns[] = array(
	'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'status',
);
$subscriptions_columns[] = array(
	'title' => '',
	'data'  => 'actions',
);

wp_localize_script(
	'masterstudy-my-sales-page',
	'my_sales_page_data',
	array(
		'report_button_title'      => __( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
		'plan_title'               => __( 'Plan', 'masterstudy-lms-learning-management-system-pro' ),
		'details_title'            => __( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
		'taxes_enabled'            => $taxes_display['enabled'],
		'instructor-orders'        => $order_columns,
		'instructor-memberships'   => $memberships_columns,
		'instructor-subscriptions' => $subscriptions_columns,
		'statuses'                 => masterstudy_lms_get_subscription_status_labels(),
		'is_admin'                 => $is_admin,
		'is_subscriptions_enabled' => is_ms_lms_addon_enabled( 'subscriptions' ),
	)
);
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-my-sales-page">
			<div class="masterstudy-my-sales-page__header">
				<h1 class="masterstudy-my-sales-page__title">
					<?php echo esc_html__( 'My sales', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</h1>
				<?php if ( $subs_for_instructors && is_ms_lms_addon_enabled( 'subscriptions' ) ) { ?>
					<div class="masterstudy-my-sales-page__subtabs">
						<ul class="masterstudy-tabs masterstudy-tabs_style-nav-sm">
							<li class="masterstudy-tabs__item masterstudy-tabs__item_active" data-tab="instructor-orders">
								<?php echo esc_html__( 'Sales', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</li>
							<li class="masterstudy-tabs__item" data-tab="<?php echo $is_admin ? 'instructor-memberships' : 'instructor-subscriptions'; ?>">
								<?php echo esc_html__( 'Subscriptions', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</li>
						</ul>
					</div>
				<?php } ?>
			</div>

			<?php if ( $is_admin && is_ms_lms_addon_enabled( 'subscriptions' ) ) { ?>
				<div class="masterstudy-my-sales-page__tabs">
					<ul class="masterstudy-tabs masterstudy-tabs_style-default">
						<li class="masterstudy-tabs__item masterstudy-tabs__item_active" data-tab="instructor-memberships">
							<?php echo esc_html__( 'Memberships', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</li>
						<li class="masterstudy-tabs__item" data-tab="instructor-subscriptions">
							<?php echo esc_html__( 'Course-based', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</li>
					</ul>
				</div>
			<?php } ?>

			<div class="masterstudy-my-sales-page-table" data-chart-id="my-sales-table">
				<div class="masterstudy-my-sales-page-table__header">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/search',
						array(
							'search_name'  => 'table-search',
							'is_queryable' => false,
							'placeholder'  => esc_html__( 'Search', 'masterstudy-lms-learning-management-system-pro' ),
						)
					);
					if ( is_ms_lms_addon_enabled( 'subscriptions' ) ) {
						STM_LMS_Templates::show_lms_template(
							'components/select',
							array(
								'select_id'    => 'subscription_type_front',
								'select_width' => '50px',
								'select_name'  => 'subscription_type_name',
								'placeholder'  => esc_html__( 'Select type', 'masterstudy-lms-learning-management-system-pro' ),
								'is_queryable' => false,
								'options'      => array(
									'category'  => esc_html__( 'Category-Based', 'masterstudy-lms-learning-management-system-pro' ),
									'full_site' => esc_html__( 'Sitewide', 'masterstudy-lms-learning-management-system-pro' ),
								),
							)
						);
					}
					STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' );
					?>
				</div>
				<div class="masterstudy-my-sales-page-table__wrapper">
					<?php STM_LMS_Templates::show_lms_template( 'components/skeleton-loader', array( 'loader_type' => 'table-loader' ) ); ?>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/analytics/datatable',
						array(
							'id'      => 'my-sales',
							'columns' => $order_columns,
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
		'id' => 'my-sales',
	)
);

do_action( 'masterstudy_after_account', $lms_current_user );
