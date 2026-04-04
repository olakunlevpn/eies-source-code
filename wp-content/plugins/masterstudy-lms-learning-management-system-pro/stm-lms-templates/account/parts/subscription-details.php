<?php
/**
 * @var $order_id
 * */

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

wp_enqueue_style( 'masterstudy-account-main' );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

STM_LMS_Templates::show_lms_template( 'header' );

$is_instructor             = STM_LMS_Instructor::is_instructor();
$subscription_id           = $order_id;
$subscription_repository   = new SubscriptionRepository();
$subscription_data         = $subscription_repository->get_subscription_details( $subscription_id );
$payment_history           = $subscription_repository->get_payment_history( $subscription_id );
$subscription_data         = $subscription_data['subscription'];
$subscription_status_label = masterstudy_lms_get_subscription_status_labels();
$student                   = get_user_meta( $subscription_data['user_id'], 'masterstudy_personal_data', true );
$user_name                 = \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $subscription_data['user_id'] );
$user_email                = \STM_LMS_Helpers::masterstudy_lms_get_user_email_by_user_id( $subscription_data['user_id'] );
$current_url               = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
$instructor_page           = strpos( $current_url, 'instructor-subscription-details' );
$coupon                    = ! empty( $payment_history[0]['coupon'] ) || ! empty( $payment_history[1]['coupon'] );
$coupon_enabled            = is_ms_lms_coupons_enabled() && ! empty( $coupon );

if ( strpos( $current_url, 'student-subscription-details' ) !== false ) {
	$back_url = ms_plugin_user_account_url( 'my-subscriptions' );
} elseif ( false !== $instructor_page ) {
	$back_url = ms_plugin_user_account_url( 'sales' );
} else {
	$back_url = ms_plugin_user_account_url( 'sales' );
}

stm_lms_register_style( 'user-orders' );
wp_enqueue_style( 'masterstudy-woocommerce-orders' );
wp_enqueue_style( 'masterstudy-subscription-details-page' );
wp_enqueue_script( 'masterstudy-subscription-details-page' );

$taxes_display = masterstudy_lms_taxes_display();

$subscription_payments_columns = array(
	array(
		'title' => esc_html__( '№', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'number',
	),
);

if ( $taxes_display['enabled'] || $coupon_enabled ) {
	$subscription_payments_columns[] = array(
		'title' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'subtotal',
	);
}

if ( $coupon_enabled ) {
	$subscription_payments_columns[] = array(
		'title' => esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'coupon',
	);
}

if ( $taxes_display['enabled'] ) {
	$subscription_payments_columns[] = array(
		'title' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'taxes',
	);
}

$subscription_payments_columns[] = array(
	'title' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'total',
);
$subscription_payments_columns[] = array(
	'title' => esc_html__( 'Payment Method', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'payment_method',
);
$subscription_payments_columns[] = array(
	'title' => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'date',
);
$subscription_payments_columns[] = array(
	'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
	'data'  => 'status',
);

wp_localize_script(
	'masterstudy-subscription-details-page',
	'subscription_details_page_data',
	array(
		'rest_url'                      => esc_url_raw( rest_url( 'masterstudy-lms/v2/' ) ),
		'nonce'                         => wp_create_nonce( 'wp_rest' ),
		'subscription_id'               => $subscription_data['subscription_id'],
		'subscription-payments-columns' => $subscription_payments_columns,
		'is_instructor'                 => $is_instructor,
		'details_title'                 => __( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
		'statuses'                      => $subscription_status_label,
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
		<div class="masterstudy-orders subscription-details">
			<div class="masterstudy-orders-details">
				<div class="masterstudy-subscriptions-details-page__header">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title'         => '',
							'link'          => $back_url,
							'style'         => 'secondary',
							'size'          => 'sm',
							'icon_position' => 'left',
							'icon_name'     => 'arrow-left',
						)
					);
					?>
					<div class="masterstudy-subscriptions-details-page__id">
						<span><?php echo esc_html( $subscription_data['plan_name'] ); ?></span>
						<span class="masterstudy-subscriptions-details-page__status masterstudy-subscriptions-details-page__status_<?php echo esc_attr( $subscription_data['status'] ); ?>">
							<?php echo esc_html( $subscription_status_label[ $subscription_data['status'] ] ); ?>
						</span>
					</div>
				</div>
				<?php if ( 'active' === $subscription_data['status'] || 'trialing' === $subscription_data['status'] || 'completed' === $subscription_data['status'] ) { ?>
					<span data-id="<?php echo esc_attr( $subscription_data['subscription_id'] ); ?>" class="masterstudy-subscriptions-details-page__cancel">
						<?php echo esc_html__( 'Cancel Subscription', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
				<?php } elseif ( ! empty( $subscription_data['plan_id'] ) && $subscription_data['is_enabled'] && ! $instructor_page && $subscription_data['subs_for_course_enabled'] && 'trialing' !== $subscription_data['status'] ) { ?>
					<span data-id="<?php echo esc_attr( $subscription_data['plan_id'] ); ?>" class="masterstudy-subscriptions-details-page__resubscribe">
						<?php echo esc_html__( 'Resubscribe', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
				<?php } ?>
			</div>
			<div class="masterstudy-orders-row">
				<div class="masterstudy-orders-column">
					<div class="masterstudy-orders-table masterstudy-orders-table__details">
						<div class="masterstudy-orders-table__header">
							<div class="masterstudy-orders-course-info">
								<?php echo esc_html__( 'Subscription Details', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
						</div>
						<div class="masterstudy-orders-table__body">
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Subscription ID:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $subscription_data['subscription_id'] ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Membership Access:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value">
										<?php echo esc_html( $subscription_data['type'] ); ?>
									</div>
								</div>
							</div>
							<?php if ( ! empty( $subscription_data['course'] ) ) { ?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label">
											<?php echo esc_html__( 'Course', 'masterstudy-lms-learning-management-system-pro' ); ?>:
										</div>
										<div class="masterstudy-orders-course-info__value">
											<a href="<?php echo esc_url( $subscription_data['course']['url'] ); ?>" target="_blank">
												<?php echo esc_html( $subscription_data['course']['title'] ); ?>
											</a>
										</div>
									</div>
								</div>
							<?php } ?>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Timezone:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value">
										<?php echo '+00:00'; ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Renew:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $subscription_data['renew'] ); ?>
									</div>
								</div>
							</div>

							<?php
							if ( isset( $subscription_data['coupon'] ) ) {
								?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label">
											<?php echo esc_html__( 'Coupon Applied:', 'masterstudy-lms-learning-management-system-pro' ); ?>
										</div>
										<div
											class="masterstudy-orders-course-info__value masterstudy-payment-method">
											<?php echo esc_html( $subscription_data['coupon'] ); ?>
										</div>
									</div>
								</div>
								<?php
							}
							?>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Start Date:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $subscription_data['start_date'] ); ?>
									</div>
								</div>
							</div>
							<?php if ( ! empty( $subscription_data['trial_end_date'] ) ) { ?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label">
											<?php echo esc_html__( 'Trial End Date:', 'masterstudy-lms-learning-management-system-pro' ); ?>
										</div>
										<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
											<?php echo esc_html( $subscription_data['trial_end_date'] ); ?>
										</div>
									</div>
								</div>
							<?php } ?>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Billing Cycles:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value">
										<?php
										echo ! empty( $subscription_data['plan_billing_cycles'] ) ?
										esc_html( $subscription_data['plan_billing_cycles'] ) . esc_html__( ' time(s)', 'masterstudy-lms-learning-management-system-pro' )
										: esc_html__( 'Until Cancelled', 'masterstudy-lms-learning-management-system-pro' );
										?>
									</div>
								</div>
							</div>
							<?php
							if ( ! empty( $subscription_data['next_payment_date'] ) && 'cancelled' !== $subscription_data['status'] ) {
								?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label">
											<?php echo esc_html__( 'Next Payment Date:', 'masterstudy-lms-learning-management-system-pro' ); ?>
										</div>
										<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
											<?php echo esc_html( $subscription_data['next_payment_date'] ); ?>
										</div>
									</div>
								</div>
								<?php
							} if ( ! empty( $subscription_data['end_date'] ) && 'cancelled' === $subscription_data['status'] || 'expired' === $subscription_data['status'] ) {
								?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__label">
											<?php echo esc_html__( 'End Date:', 'masterstudy-lms-learning-management-system-pro' ); ?>
										</div>
										<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
											<?php echo esc_html( $subscription_data['end_date'] ); ?>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="masterstudy-orders-column">
					<div class="masterstudy-orders-table masterstudy-orders-table__details">
						<div class="masterstudy-orders-table__header">
							<div
								class="masterstudy-orders-course-info"><?php echo esc_html__( 'Student Details', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
						</div>
						<div class="masterstudy-orders-table__body">
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Full name:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value">
										<?php echo esc_html( $user_name ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Email:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value">
										<?php echo esc_html( $user_email ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Country:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $student['country'] ?? '' ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Post Code:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $student['post_code'] ?? '' ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'State:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $student['state'] ?? '' ); ?>
									</div>
								</div>
							</div>

							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Town/City:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $student['city'] ?? '' ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Company Name:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $student['company'] ?? '' ); ?>
									</div>
								</div>
							</div>
							<div class="masterstudy-orders-table__body-row">
								<div class="masterstudy-orders-course-info">
									<div class="masterstudy-orders-course-info__label">
										<?php echo esc_html__( 'Phone Number:', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
									<div class="masterstudy-orders-course-info__value masterstudy-payment-method">
										<?php echo esc_html( $student['phone'] ?? '' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if ( $is_instructor ) { ?>
					<div class="masterstudy-orders-column">
						<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__notes">
								<div class="masterstudy-orders-table__header">
									<div class="masterstudy-orders-course-info">
										<?php echo esc_html__( 'Subscription Note', 'masterstudy-lms-learning-management-system-pro' ); ?>
									</div>
								</div>
							<div class="masterstudy-orders-table__body">
								<div class="masterstudy-orders-note-text">
									<div id="masterstudy-subscription-note-text" class="masterstudy-orders-note-text">
										<?php echo esc_html( $subscription_data['text'] ?? __( 'Add note', 'masterstudy-lms-learning-management-system-pro' ) ); ?>
									</div>
									<textarea
										id="subscription-note-textarea"
										class="masterstudy-orders-note-input"
										placeholder="<?php echo esc_attr__( 'Enter note...', 'masterstudy-lms-learning-management-system-pro' ); ?>"
										data-original-text="<?php echo wp_kses_post( $subscription_data['text'] ?? '' ); ?>"
									><?php echo wp_kses_post( $subscription_data['text'] ?? '' ); ?>
									</textarea>
								</div>
							</div>
							<div class="masterstudy-orders-note__actions">
								<button id="subscription-note-edit-btn" class="masterstudy-orders-note-edit">
									<span class="stmlms-pencil1"></span>
								</button>
								<button id="subscription-note-cancel-btn" class="masterstudy-orders-note-cancel">
									<span class="stmlms-close"></span>
								</button>
								<button
									id="subscription-note-update-btn"
									data-subscription-id="<?php echo esc_attr( $subscription_data['subscription_id'] ); ?>"
									class="masterstudy-orders-note-save"
								>
									<span class="stmlms-check-2"></span>
								</button>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="masterstudy-subscriptions-details-page-table" data-chart-id="subscription-payments-table">
			<?php STM_LMS_Templates::show_lms_template( 'components/skeleton-loader', array( 'loader_type' => 'table-loader' ) ); ?>
			<div class="masterstudy-subscriptions-details-page-table__wrapper">
				<div class="masterstudy-subscriptions-details-page-table__header">
					<div class="masterstudy-subscriptions-details-page-table__title">
						<?php echo esc_html__( 'Payment History', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
				</div>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/analytics/datatable',
					array(
						'id'      => 'subscription-payments',
						'columns' => $subscription_payments_columns,
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
<?php
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

STM_LMS_Templates::show_lms_template( 'footer' );
