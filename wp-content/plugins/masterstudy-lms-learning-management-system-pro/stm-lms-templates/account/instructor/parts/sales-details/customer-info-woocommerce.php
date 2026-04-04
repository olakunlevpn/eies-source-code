<?php
/**
 * @var $order_id - Order ID pulled from a template in the parent 'orders-details' directory.
 */

$order_info    = \STM_LMS_Order::get_instructor_order_info( $order_id );
$order_details = apply_filters( 'stm_lms_order_details', null, $order_id );
$order_note    = $order_info['order_note'] ?? '';
$note_text     = esc_html__( 'Add note', 'masterstudy-lms-learning-management-system-pro' );
?>
<div class="masterstudy-orders-row">
	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__details-column">
			<div class="masterstudy-orders-table__header">
				<div class="masterstudy-orders-course-info"><?php echo esc_html__( 'Address', 'masterstudy-lms-learning-management-system-pro' ); ?></div>
			</div>
			<div class="masterstudy-orders-table__body">
			<?php
				$order_fields = array(
					'Full name' => ! empty( $order_details['billing']['first_name'] ) || ! empty( $order_details['billing']['last_name'] )
						? $order_details['billing']['first_name'] . ' ' . $order_details['billing']['last_name']
						: '',
					'Address'   => ! empty( $order_details['billing']['address_1'] ) ? $order_details['billing']['address_1'] : '',
					'Country'   => ! empty( $order_details['billing']['country'] ) ? $order_details['billing']['country'] : '',
					'Email'     => ! empty( $order_details['billing']['email'] ) ? $order_details['billing']['email'] : '',
					'Phone'     => ! empty( $order_details['billing']['phone'] ) ? $order_details['billing']['phone'] : '',
				);

				foreach ( $order_fields as $label => $value ) :
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
				endforeach;
				?>
			</div>
		</div>
	</div>
	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details masterstudy-orders-table__details-column">
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
				'subtotal'     => array(
					'label' => esc_html__( 'Subtotal', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => isset( $order_info['totals']['order_subtotal_net'] ) ? STM_LMS_Helpers::display_price( $order_info['totals']['order_subtotal_net'] ) : '',
					'class' => '',
				),
				'tax'          => array(
					'label' => esc_html__( 'Tax', 'masterstudy-lms-learning-management-system-pro' ),
					'value' => isset( $order_info['totals']['order_total_tax'] ) ? STM_LMS_Helpers::display_price( $order_info['totals']['order_total_tax'] ) : '',
					'class' => '',
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
