<?php
/**
 * @var $order_id - Order ID pulled from a template in the parent 'orders-details' directory.
 */

$order_info    = \STM_LMS_Order::get_order_info( $order_id );
$order_details = apply_filters( 'stm_lms_order_details', null, $order_id );
?>
<div class="masterstudy-orders-row">
	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details">
			<div class="masterstudy-orders-table__header">
				<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Address', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
			</div>
			<div class="masterstudy-orders-table__body">
			<?php
				$order_fields = array(
					__( 'Full name', 'masterstudy-lms-learning-management-system-pro' ) => ! empty( $order_details['billing']['first_name'] ) || ! empty( $order_details['billing']['last_name'] )
						? $order_details['billing']['first_name'] . ' ' . $order_details['billing']['last_name']
						: '',
					__( 'Address', 'masterstudy-lms-learning-management-system-pro' )   => ! empty( $order_details['billing']['address_1'] ) ? $order_details['billing']['address_1'] : '',
					__( 'Country', 'masterstudy-lms-learning-management-system-pro' )   => ! empty( $order_details['billing']['country'] ) ? $order_details['billing']['country'] : '',
					__( 'Email', 'masterstudy-lms-learning-management-system-pro' )     => ! empty( $order_details['billing']['email'] ) ? $order_details['billing']['email'] : '',
					__( 'Phone', 'masterstudy-lms-learning-management-system-pro' )     => ! empty( $order_details['billing']['phone'] ) ? $order_details['billing']['phone'] : '',
				);

				foreach ( $order_fields as $label => $value ) :
					if ( empty( $value ) ) {
						continue;
					}
					?>
					<div class="masterstudy-orders-table__body-row">
						<div class="masterstudy-orders-course-info">
							<div class="masterstudy-orders-course-info__label">
								<?php echo sprintf( esc_html__( '%s:', 'masterstudy-lms-learning-management-system-pro' ), esc_html( $label ) ); ?>
							</div>
							<div class="masterstudy-orders-course-info__value">
								<?php echo esc_html( $value ); ?>
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
		<div class="masterstudy-orders-table masterstudy-orders-table__details">
			<div class="masterstudy-orders-table__header">
				<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Total Billed', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
			</div>
			<?php
			$order_info_fields = array(
				'payment_code' => array(
					'label' => esc_html__( 'Payment method', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => $order_info['payment_code']
						? ( 'wire_transfer' === $order_info['payment_code']
							? __( 'wire transfer', 'masterstudy-lms-learning-management-system-pro' )
							: $order_info['payment_code'] )
						: '',
					'class' => 'masterstudy-payment-method',
				),
				'total'        => array(
					'label' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => isset( $order_info['totals']['grand_total'] ) ? STM_LMS_Helpers::display_price( $order_info['totals']['grand_total'] ) : '',
					'class' => '',
				),
				'status'       => array(
					'label' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => $order_info['status'],
					'class' => 'order-status ' . $order_info['status'],
				),
				'transaction'  => array(
					'label' => esc_html__( 'Transaction ID', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => $order_details['billing']['transaction'],
					'class' => '',
				),
			);

			if ( wc_tax_enabled() ) {
				$tax_fields = array(
					'subtotal' => array(
						'label' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => isset( $order_info['totals']['order_subtotal_net'] ) ? STM_LMS_Helpers::display_price( $order_info['totals']['order_subtotal_net'] ) : '',
						'class' => '',
					),
					'tax'      => array(
						'label' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => isset( $order_info['totals']['order_total_tax'] ) ? STM_LMS_Helpers::display_price( $order_info['totals']['order_total_tax'] ) : '',
						'class' => '',
					),
				);

				$before_total      = array_slice( $order_info_fields, 0, 1, true );
				$after_total       = array_slice( $order_info_fields, 1, null, true );
				$order_info_fields = $before_total + $tax_fields + $after_total;
			}
			?>
			<div class="masterstudy-orders-table__body">
				<?php
				foreach ( $order_info_fields as $field ) :
					if ( ! empty( $field['value'] ) ) :
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
					endif;
				endforeach;
				?>
			</div>
		</div>
	</div>
</div>
