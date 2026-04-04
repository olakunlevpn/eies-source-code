<?php
/**
 * @var $order_id - Order ID pulled from a template in the parent 'orders-details' directory.
 */

$order_info           = \STM_LMS_Order::get_instructor_order_info( $order_id );
$order_note           = $order_info['order_note'] ?? '';
$note_text            = esc_html__( 'Add note', 'masterstudy-lms-learning-management-system-pro' );
$taxes_display        = masterstudy_lms_taxes_display();
$personal_data        = isset( $order_info['personal_data'] ) && is_array( $order_info['personal_data'] ) ? $order_info['personal_data'] : array();
$personal_data_fields = function_exists( 'masterstudy_lms_personal_data_fields' ) ? masterstudy_lms_personal_data_fields() : array();
$countries            = function_exists( 'masterstudy_lms_get_countries' ) ? masterstudy_lms_get_countries( false ) : array();
$states               = function_exists( 'masterstudy_lms_get_us_states' ) ? masterstudy_lms_get_us_states( false ) : array();
?>
<div class="masterstudy-orders-row">
	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__details-column">
			<div class="masterstudy-orders-table__header">
				<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Address', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
			</div>
			<div class="masterstudy-orders-table__body">
			<?php
				$user_id   = ! empty( $order_info['user_id'] ) ? (int) $order_info['user_id'] : null;
				$user_info = $user_id ? get_userdata( $user_id ) : null;

				$author_fields = array(
					'Full name' => $user_info ? $user_info->display_name : '',
					'Email'     => $user_info ? $user_info->user_email : '',
				);

				foreach ( $author_fields as $label => $value ) {
					if ( empty( $value ) ) {
						continue;
					}
					?>
					<div class="masterstudy-orders-table__body-row">
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__label">
								<?php printf( esc_html__( '%s:', 'masterstudy-lms-learning-management-system-pro' ), esc_html( $label ) ); ?>
							</div>
							<div class="masterstudy-orders-course-info__value">
								<?php echo esc_html( $value ); ?>
							</div>
						</div>
					</div>
					<?php
				}
				if ( ! empty( $personal_data ) && is_array( $personal_data ) ) {
					foreach ( $personal_data as $field => $value ) {
						$label = isset( $personal_data_fields[ $field ] )
							? $personal_data_fields[ $field ]
							: ucfirst( str_replace( '_', ' ', $field ) );
						?>
						<div class="masterstudy-orders-table__body-row">
							<div class="masterstudy-orders-course-info">
								<div class="masterstudy-orders-course-info__label"><?php echo esc_html( $label ); ?>:</div>
								<div class="masterstudy-orders-course-info__value">
									<?php
									if ( 'country' === $field ) {
										$matched       = array_filter(
											$countries,
											function ( $country ) use ( $value ) {
												return strtoupper( $country['code'] ) === strtoupper( $value );
											}
										);
										$country_label = ! empty( $matched ) ? reset( $matched )['name'] : $value;

										echo esc_html( $country_label );
									} elseif ( 'state' === $field ) {
										$matched     = array_filter(
											$states,
											function ( $state ) use ( $value ) {
												return strtoupper( $state['code'] ) === strtoupper( $value );
											}
										);
										$state_label = ! empty( $matched ) ? reset( $matched )['name'] : $value;

										echo esc_html( $state_label );
									} else {
										echo esc_html( $value );
									}
									?>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__details-column">
			<div class="masterstudy-orders-table__header">
				<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Total Billed', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
			</div>
			<div class="masterstudy-orders-table__body">
			<?php
				$order_fields = array(
					'payment_code' => array(
						'label' => esc_html__( 'Payment method', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => $order_info['payment_code']
							? ( 'wire_transfer' === $order_info['payment_code']
								? __( 'wire transfer', 'masterstudy-lms-learning-management-system-pro' )
								: $order_info['payment_code'] )
							: '',
						'class' => 'masterstudy-payment-method',
					),
					'status'       => array(
						'label' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => $order_info['status'] ? $order_info['status'] : '',
						'class' => 'order-status ' . $order_info['status'],
					),
					'transaction'  => array(
						'label' => esc_html__( 'Transaction ID', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => $order_info['id'] ? $order_info['id'] : '',
						'class' => '',
					),
				);

				$has_coupon = is_ms_lms_coupons_enabled() && ! empty( $order_info['coupon_value'] );

				if ( $has_coupon ) {
					$coupon = array(
						'label' => esc_html__( 'Coupon', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => $order_info['coupon_value'],
						'class' => '',
					);
				}

				if ( $has_coupon || $taxes_display['enabled'] ) {
					$subtotal = array(
						'label' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => STM_LMS_Helpers::display_price( $order_info['subtotal'] ?? $order_info['total'] ),
						'class' => '',
					);
				}

				if ( $taxes_display['enabled'] ) {
					$taxes = array(
						'label' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => STM_LMS_Helpers::display_price( $order_info['taxes'] ?? 0 ),
						'class' => '',
					);
				}

				if ( ! empty( $subtotal ) ) {
					$order_fields['subtotal'] = $subtotal;
				}

				if ( ! empty( $coupon ) ) {
					$order_fields['coupon'] = $coupon;
				}

				if ( ! empty( $taxes ) ) {
					$order_fields['taxes'] = $taxes;
				}

				$order_fields['total'] = array(
					'label' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => STM_LMS_Helpers::display_price( $order_info['total'] ?? 0 ),
					'class' => '',
				);

				foreach ( $order_fields as $field ) :
					?>
					<div class="masterstudy-orders-table__body-row">
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__label">
								<?php echo esc_html( $field['label'] ); ?>:
							</div>
							<div class="masterstudy-orders-course-info__value <?php echo esc_attr( $field['class'] ); ?>">
								<?php echo esc_html( $field['value'] ); ?>
							</div>
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</div>

	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__details-column masterstudy-orders-table__notes">
			<div class="masterstudy-orders-table__header">
				<div class="masterstudy-orders-course-info">
					<?php echo esc_html__( 'Order note', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</div>
			</div>
			<div class="masterstudy-orders-table__body">
				<div class="masterstudy-orders-note-text">
					<?php echo esc_attr( empty( $order_note ) ? $note_text : $order_note ); ?>
				</div>
				<textarea class="masterstudy-orders-note-input"><?php echo esc_attr( $order_note ); ?></textarea>
			</div>
			<div class="masterstudy-orders-note__actions">
				<button class="masterstudy-orders-note-edit">
					<span class="stmlms-pencil1"></span>
				</button>
				<button class="masterstudy-orders-note-cancel">
					<span class="stmlms-close"></span>
				</button>
				<button class="masterstudy-orders-note-save">
					<span class="stmlms-check-2"></span>
				</button>
			</div>
		</div>
	</div>
</div>
