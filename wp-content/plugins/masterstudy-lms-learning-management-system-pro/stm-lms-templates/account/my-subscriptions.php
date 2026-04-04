<?php
$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

wp_enqueue_style( 'masterstudy-account-main' );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_style( 'masterstudy-tabs' );
wp_enqueue_style( 'masterstudy-my-subscriptions-page' );
wp_enqueue_script( 'masterstudy-my-subscriptions-page' );

$taxes_display         = masterstudy_lms_taxes_display();
$subscriptions_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'number',
	),
	array(
		'title' => esc_html__( 'Plan Title', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'plan',
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
	'title' => esc_html__( 'Date of Issue', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'date',
);
$subscriptions_columns[] = array(
	'title' => esc_html__( 'Next Renewal', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'autoRenew',
);
$subscriptions_columns[] = array(
	'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'status',
);
$subscriptions_columns[] = array(
	'title' => '',
	'data'  => 'actions',
);

$memberships_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'number',
	),
	array(
		'title' => esc_html__( 'Plan Title', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'plan',
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
	'title' => esc_html__( 'Date of Issue', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'date',
);
$memberships_columns[] = array(
	'title' => esc_html__( 'Next Renewal', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'autoRenew',
);
$memberships_columns[] = array(
	'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'status',
);
$memberships_columns[] = array(
	'title' => '',
	'data'  => 'actions',
);

$membership_mode = STM_LMS_Options::get_option( 'membership_mode', false );

wp_localize_script(
	'masterstudy-my-subscriptions-page',
	'subscriptions_page_data',
	array(
		'rest_url'          => esc_url_raw( rest_url( 'masterstudy-lms/v2/' ) ),
		'nonce'             => wp_create_nonce( 'wp_rest' ),
		'my-subscription'   => $subscriptions_columns,
		'my-membership'     => $memberships_columns,
		'plan_title'        => __( 'Plan', 'masterstudy-lms-learning-management-system-pro' ),
		'details_title'     => __( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
		'cancel_title'      => __( 'Cancel Subscription', 'masterstudy-lms-learning-management-system-pro' ),
		'resubscribe_title' => __( 'Resubscribe', 'masterstudy-lms-learning-management-system-pro' ),
		'statuses'          => masterstudy_lms_get_subscription_status_labels(),
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
		<div class="masterstudy-subscriptions-page">
			<div class="masterstudy-subscriptions-page__header">
				<h1 class="masterstudy-subscriptions-page__title">
					<?php echo esc_html__( 'My Subscriptions', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</h1>
				<?php if ( ! $membership_mode ) { ?>
					<div class="masterstudy-subscriptions-page__tabs">
						<ul class="masterstudy-tabs masterstudy-tabs_style-nav-sm">
							<li class="masterstudy-tabs__item masterstudy-tabs__item_active" data-tab="my-membership">
								<?php echo esc_html__( 'Memberships', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</li>
							<li class="masterstudy-tabs__item" data-tab="my-subscription">
								<?php echo esc_html__( 'Course-based', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</li>
						</ul>
					</div>
				<?php } ?>
			</div>
			<div class="masterstudy-subscriptions-page-table" data-chart-id="my-subscriptions-table">
				<div class="masterstudy-subscriptions-page-table__header">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/search',
						array(
							'search_name'  => 'table-search',
							'is_queryable' => false,
							'placeholder'  => esc_html__( 'Search', 'masterstudy-lms-learning-management-system-pro' ),
						)
					);
					STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' );
					?>
				</div>
				<div class="masterstudy-subscriptions-page-table__wrapper">
					<?php
					STM_LMS_Templates::show_lms_template( 'components/skeleton-loader', array( 'loader_type' => 'table-loader' ) );
					STM_LMS_Templates::show_lms_template(
						'components/analytics/datatable',
						array(
							'id'      => 'my-subscriptions',
							'columns' => $memberships_columns,
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
		'id' => 'my-subscriptions',
	)
);

STM_LMS_Templates::show_lms_template(
	'components/alert',
	array(
		'id'                  => 'cancel_subscription_alert',
		'title'               => esc_html__( 'You Are Cancelling The Subscription!', 'masterstudy-lms-learning-management-system-pro' ),
		'text'                => esc_html__( 'After this, you will lose access to all courses and materials included.', 'masterstudy-lms-learning-management-system-pro' ),
		'submit_button_text'  => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system-pro' ),
		'cancel_button_text'  => esc_html__( 'Close', 'masterstudy-lms-learning-management-system-pro' ),
		'submit_button_style' => 'danger',
		'cancel_button_style' => 'tertiary',
		'dark_mode'           => false,
	)
);

do_action( 'masterstudy_after_account', $lms_current_user );
