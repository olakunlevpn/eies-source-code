<?php
/**
 * @var $order_id - Order ID pulled from a template in the parent 'orders-details' directory.
 */

$order_info = \STM_LMS_Order::get_order_info( $order_id );
?>
<div class="masterstudy-orders-row">
	<div class="masterstudy-orders-column">
		<div class="masterstudy-orders-table masterstudy-orders-table__details">
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

				foreach ( $author_fields as $label => $value ) :
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
					'total'        => array(
						'label' => esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ),
						'value' => $order_info['total_formatted'] ? $order_info['total_formatted'] : '',
						'class' => '',
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
</div>
