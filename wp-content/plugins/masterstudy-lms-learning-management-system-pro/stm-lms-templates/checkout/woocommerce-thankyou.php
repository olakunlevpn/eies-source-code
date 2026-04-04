<?php
/**
 * @var WC_Order $order
 *
 * The $order object is passed from the method masterstudy_create_template_thankyou_message()
 * located in the file lms/classes/woocommerce-thankyou.php.
 */
wp_enqueue_style( 'masterstudy-button' );
stm_lms_register_style( 'user-orders' );
if ( isset( $order ) && $order instanceof WC_Order ) {
	$order_info = STM_LMS_Order::get_order_info( $order->get_id() );

	?>
	<div class="stm-lms-wrapper">
		<div class="container">
			<div class="masterstudy-orders masterstudy-thank-you-page">
				<div class="masterstudy-orders-box">
					<div class="masterstudy-orders-box__title"><?php echo esc_html__( 'Thank you for your order!', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
					<div class="masterstudy-orders-box__info">
						<div class="masterstudy-orders-box__info-label"><?php echo esc_html__( 'Order ID:', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
						<div class="masterstudy-orders-box__info-value">
							<div class="masterstudy-orders-box__info-label"><?php echo esc_html( $order_info['id'] ); ?></div>
						</div>
					</div>
					<div class="masterstudy-orders-box__info">
						<div class="masterstudy-orders-box__info-label"><?php echo esc_html__( 'Date:', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
						<div class="masterstudy-orders-box__info-value"><?php echo esc_html( $order_info['date_formatted'] ); ?></div>
					</div>
				</div>
				<div class="masterstudy-orders-container <?php echo esc_attr( STM_LMS_Cart::woocommerce_checkout_enabled() ? 'masterstudy-orders-container_woocommerce' : '' ); ?>">
					<div class="masterstudy-orders-table">
						<div class="masterstudy-orders-table__header">
							<div class="masterstudy-orders-course-info">
								<?php echo esc_html__( 'Order details', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</div>
						</div>
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
										<?php
											STM_LMS_Templates::show_lms_template(
												'components/button',
												array(
													'title' => esc_html__( 'View', 'masterstudy-lms-learning-management-system-pro' ),
													'link' => esc_url( $_order['link'] ),
													'style' => 'secondary masterstudy-orders-course-info__button',
													'size' => 'sm',
												)
											);
										?>
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
							<div class="masterstudy-orders-course-info">
								<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ); ?>:</div>
								<div class="masterstudy-orders-course-info__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $order_info['totals']['grand_total'] ) ); ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="masterstudy-orders-row">
					<!-- Student info -->
					<div class="masterstudy-orders-column">
						<div class="masterstudy-orders-table">
							<div class="masterstudy-orders-table__header">
								<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Address', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
							</div>
							<?php
							$billing_info_data = array(
								__( 'Full name', 'masterstudy-lms-learning-management-system-pro' ) => $order_info['billing']['first_name'] . ' ' . $order_info['billing']['last_name'],
								__( 'Email', 'masterstudy-lms-learning-management-system-pro' )     => $order_info['billing']['email'],
								__( 'Address', 'masterstudy-lms-learning-management-system-pro' )   => $order_info['billing']['address_1'],
								__( 'Country', 'masterstudy-lms-learning-management-system-pro' )   => $order_info['billing']['country'],
								__( 'Phone', 'masterstudy-lms-learning-management-system-pro' )     => $order_info['billing']['phone'],
							);
							?>
							<div class="masterstudy-orders-table__body">
								<?php foreach ( $billing_info_data as $label => $value ) : ?>
									<div class="masterstudy-orders-table__body-row">
										<div class="masterstudy-orders-course-info">
											<div class="masterstudy-orders-course-info__label">
												<?php printf( '%s:', esc_html( $label ) ); ?>
											</div>
											<div class="masterstudy-orders-course-info__value"><?php echo esc_html( $value ); ?></div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<!-- Total fee -->
					<div class="masterstudy-orders-column">
						<div class="masterstudy-orders-table">
							<div class="masterstudy-orders-table__header">
								<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Total Billed', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
							</div>
							<?php
							$total_info_data = array(
								__( 'Payment method', 'masterstudy-lms-learning-management-system-pro' ) => $order_info['payment_code'],
								__( 'Total', 'masterstudy-lms-learning-management-system-pro' )          => STM_LMS_Helpers::display_price( $order_info['totals']['grand_total'] ),
								__( 'Status', 'masterstudy-lms-learning-management-system-pro' )         => '<span class="order-status ' . esc_attr( $order_info['status'] ) . '">' . esc_attr( $order_info['status_name'] ) . '</span>',
							);

							if ( wc_tax_enabled() ) {
								$tax_fields = array(
									__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ) => STM_LMS_Helpers::display_price( $order_info['totals']['order_subtotal_net'] ),
									__( 'Tax', 'masterstudy-lms-learning-management-system-pro' )      => STM_LMS_Helpers::display_price( $order_info['totals']['order_total_tax'] ),
								);

								$before_total    = array_slice( $total_info_data, 0, 1, true );
								$after_total     = array_slice( $total_info_data, 1, null, true );
								$total_info_data = $before_total + $tax_fields + $after_total;
							}

							if ( $order->get_transaction_id() ) {
								$total_info_data[ __( 'Transaction ID', 'masterstudy-lms-learning-management-system-pro' ) ] = $order->get_transaction_id();
							}
							?>
							<div class="masterstudy-orders-table__body">
								<?php
								foreach ( $total_info_data as $label => $value ) :
									if ( ! empty( $value ) ) :
										?>
									<div class="masterstudy-orders-table__body-row">
										<div class="masterstudy-orders-course-info">
											<div class="masterstudy-orders-course-info__label">
												<?php printf( '%s:', esc_html( $label ) ); ?>
											</div>
											<div class="masterstudy-orders-course-info__value"><?php echo wp_kses_post( $value ); ?></div>
										</div>
									</div>
										<?php
									endif;
								endforeach;
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="masterstudy-orders-button">
				<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'View all orders', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => esc_url( get_permalink( STM_LMS_Options::get_option( 'user_url' ) ) . 'my-orders/' ),
							'style' => 'secondary',
							'size'  => 'sm',
						)
					);
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
