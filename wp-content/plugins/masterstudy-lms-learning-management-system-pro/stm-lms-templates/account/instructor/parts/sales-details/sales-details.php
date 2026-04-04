<?php
/**
 * @var $order_id
 * */

STM_LMS_Templates::show_lms_template( 'header' );

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

wp_enqueue_style( 'masterstudy-account-main' );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

$order_info = STM_LMS_Order::get_instructor_order_info( $order_id );

stm_lms_register_style( 'user-orders' );
wp_enqueue_style( 'masterstudy-woocommerce-orders' );
wp_enqueue_script( 'masterstudy-woocommerce-orders' );
wp_localize_script(
	'masterstudy-woocommerce-orders',
	'masterstudy_woocommerce_orders',
	array(
		'ajaxurl'          => admin_url( 'admin-ajax.php' ),
		'nonce'            => wp_create_nonce( 'ms_lms_nonce' ),
		'nonce_save_order' => wp_create_nonce( 'save_order' ),
		'order'            => array(
			'order_id' => $order_id,
			'status'   => $order_info['status'],
		),
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
		<div class="masterstudy-orders instructor-sales-details">
			<?php
			$taxes_display = masterstudy_lms_taxes_display();
			$has_coupon    = is_ms_lms_coupons_enabled() && $order_info['coupon_value'];
			$logo_url      = $settings['print_page_logo'] ?? null;

			if ( $logo_url ) {
				echo '<img src="' . esc_url( wp_get_attachment_url( $logo_url ) ) . '" style="display: none;" width="180" height="40" class="masterstudy-orders__site-logo">';
			} else {
				echo '<img src="' . esc_url( STM_LMS_PRO_URL . 'assets/img/ms-logo.png' ) . '" style="display: none;" width="180" height="40" class="masterstudy-orders__site-logo">';
			}
			?>
			<div class="masterstudy-orders-details">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => '',
						'link'          => ms_plugin_user_account_url( 'my-orders' ),
						'style'         => 'secondary',
						'size'          => 'sm',
						'icon_position' => 'left',
						'icon_name'     => 'arrow-left',
					)
				);
				?>
				<div class="masterstudy-orders-details__id">
					<span><?php echo esc_html__( 'Order:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
					<?php echo esc_html( $order_info['id'] ); ?>
				</div>
				<div class="masterstudy-orders-details__date">
					<span><?php echo esc_html__( 'Date:', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
					<?php echo esc_html( $order_info['date_formatted'] ); ?>
				</div>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title'         => esc_html__( 'Print', 'masterstudy-lms-learning-management-system-pro' ),
						'link'          => '#',
						'style'         => 'secondary',
						'size'          => 'sm',
						'id'            => 'print-button',
						'icon_position' => 'left',
						'icon_name'     => 'print',
					)
				);
				?>
			</div>
			<?php
			$wire_transfer           = get_option( 'woocommerce_bacs_accounts' );
			$woocommerce_enable_bacs = get_option( 'woocommerce_bacs_settings' );
			if ( ! empty( $wire_transfer ) && STM_LMS_Cart::woocommerce_checkout_enabled() && wc_get_order( $order_id ) && 'yes' === $woocommerce_enable_bacs['enabled'] ) :
				?>
				<div class="masterstudy-payment-methods">
					<div class="masterstudy-payment-methods__title">
						<?php echo esc_html__( 'Bank details', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
					<div class="masterstudy-payment-methods__table-woocommerce">
					<?php foreach ( $wire_transfer as $key => $account ) : ?>
						<div class="masterstudy-payment-methods__table">
							<div class="masterstudy-payment-methods__sub-title">
								<?php echo esc_html__( 'Method', 'masterstudy-lms-learning-management-system-pro' ) . ' ' . esc_html( $key + 1 ); ?>
							</div>
							<div class="masterstudy-payment-methods__table-column">
								<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Bank', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
								<div class="masterstudy-payment-methods__value"><?php echo esc_html( $account['bank_name'] ); ?></div>
							</div>
							<div class="masterstudy-payment-methods__table-column">
								<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Recipient', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
								<div class="masterstudy-payment-methods__value"><?php echo esc_html( $account['account_name'] ); ?></div>
							</div>
							<div class="masterstudy-payment-methods__table-column">
								<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Account Number', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
								<div class="masterstudy-payment-methods__value"><?php echo esc_html( $account['account_number'] ); ?></div>
							</div>
							<div class="masterstudy-payment-methods__table-column">
								<div class="masterstudy-payment-methods__name"><?php echo esc_html__( 'Amount to be paid', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
								<div class="masterstudy-payment-methods__value"><?php echo esc_attr( $order_info['total'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
					</div>
				</div>
				<?php
			endif;

			if ( isset( $order_info['items'] ) && is_array( $order_info['items'] ) && ! empty( $order_info['cart_items'] ) ) :
				?>
				<div class="masterstudy-orders-container">
					<div id="masterstudy-order-template">
						<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__details-list">
							<div class="masterstudy-orders-table__body">
								<?php foreach ( $order_info['cart_items'] as $_order ) : ?>
								<div class="masterstudy-orders-table__body-row">
									<div class="masterstudy-orders-course-info">
										<div class="masterstudy-orders-course-info__image">
											<a href="<?php echo esc_url( $_order['link'] ); ?>">
												<img width="300" height="225" src="<?php echo esc_url( wp_get_attachment_url( $_order['thumbnail_id'] ) ); ?>" class="attachment-img-300-225 size-img-300-225 wp-post-image" alt="<?php echo esc_attr( $_order['title'] ); ?>" decoding="async" loading="lazy">
											</a>
										</div>
										<div class="masterstudy-orders-course-info__common">
											<div class="masterstudy-orders-course-info__title">
												<a href="<?php echo esc_url( $_order['link'] ); ?>">
													<?php echo esc_html( $_order['title'] ); ?>
												</a>
												<?php if ( ! empty( $_order['bundle_id'] ) || ! empty( $_order['bundle_courses_count'] ) ) : ?>
													<span class="order-status"><?php echo esc_html__( 'bundle', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
												<?php endif; ?>
												<?php if ( ! empty( $_order['enterprise_id'] ) ) : ?>
													<span class="order-status"><?php echo esc_html__( 'enterprise', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
												<?php endif; ?>
											</div>
											<div class="masterstudy-orders-course-info__category">
											<?php
											if ( ! empty( $_order['enterprise_id'] ) ) {
												echo esc_html( get_the_title( $_order['enterprise_id'] ) );
											} else {
												if ( ! empty( $_order['terms'] ) ) {
													echo esc_html( implode( ' ', $_order['terms'] ) );
												}

												if ( ! empty( $_order['bundle_courses_count'] ) ) {
													echo esc_html( $_order['bundle_courses_count'] . ' ' . $order_info['i18n']['bundle'] );
												}
											}
											?>
											</div>
										</div>
										<div class="masterstudy-orders-course-info__price">
											<?php echo esc_html( $_order['price_formatted'] ); ?>
										</div>
									</div>
									<div class="masterstudy-orders-downloads">
										<?php
										if ( ! empty( $_order['downloads'] ) ) {
											foreach ( $_order['downloads'] as $download ) {
												?>
												<div class="masterstudy-orders-downloads__info">
													<div class="masterstudy-orders-downloads__info-label">
														<span><?php echo esc_html__( 'Downloads remaining', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
														<?php echo esc_html( $download['downloads_remaining'] ); ?>
													</div>
													<div class="masterstudy-orders-downloads__info-label">
														<span><?php echo esc_html__( 'Expires', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
														<?php echo esc_html( $download['access_expires'] ); ?>
													</div>
													<?php
													STM_LMS_Templates::show_lms_template(
														'components/button',
														array(
															'title' => esc_html( $download['name'] ),
															'link' => esc_url( $download['url'] ),
															'style' => 'secondary masterstudy-orders-course-info__button',
															'size' => 'sm',
														)
													);
													?>
												</div>
												<?php
											}
										}
										?>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
							<div class="masterstudy-orders-table__footer">
								<div id="total" class="masterstudy-orders-course-info">
									<?php if ( $taxes_display['enabled'] || $has_coupon ) : ?>
										<div data-id="subtotal" class="masterstudy-orders-course-info__block">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['subtotal'] ) ); ?></div>
										</div>
									<?php endif ?>
									<?php if ( $has_coupon ) : ?>
										<div data-id="coupon" class="masterstudy-orders-course-info__block">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__price"><?php echo esc_html( $order_info['coupon_value'] ); ?></div>
										</div>
									<?php endif ?>
									<?php if ( $taxes_display['enabled'] ) : ?>
										<div data-id="taxes" class="masterstudy-orders-course-info__block">
											<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Tax', 'masterstudy-lms-learning-management-system' ); ?>:</div>
											<div class="masterstudy-orders-course-info__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['taxes'] ) ); ?></div>
										</div>
									<?php endif ?>
									<div class="masterstudy-orders-course-info__block">
										<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ); ?>:</div>
										<div class="masterstudy-orders-course-info__price">
											<?php echo esc_html( STM_LMS_Cart::woocommerce_checkout_enabled() ? STM_LMS_Helpers::display_price( $order_info['totals']['grand_total'] ) : STM_LMS_Helpers::display_price( $order_info['total'] ) ); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				$template = STM_LMS_Cart::woocommerce_checkout_enabled()
					? 'account/instructor/parts/sales-details/customer-info-woocommerce'
					: 'account/instructor/parts/sales-details/customer-info-lms';

				STM_LMS_Templates::show_lms_template(
					$template,
					array(
						'order_id' => $order_id,
					)
				);
			endif;
			?>
		</div>
	</div>
</div>
<?php
do_action( 'masterstudy_after_account', $current_user );

STM_LMS_Templates::show_lms_template( 'footer' );
?>
