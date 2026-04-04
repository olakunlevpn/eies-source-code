<?php
wp_enqueue_style( 'masterstudy-woocommerce-orders' );
wp_enqueue_script( 'masterstudy-woocommerce-orders' );
wp_localize_script(
	'masterstudy-woocommerce-orders',
	'masterstudy_woocommerce_orders',
	array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'ms_lms_nonce' ),
	)
);
?>
<div class="masterstudy-orders student-orders">
	<h1 class="masterstudy-orders__title">
		<?php echo esc_html__( 'My Orders', 'masterstudy-lms-learning-management-system' ); ?>
	</h1>
	<div class="masterstudy-orders-container <?php echo esc_attr( STM_LMS_Cart::woocommerce_checkout_enabled() ? 'masterstudy-orders-container_woocommerce' : '' ); ?>">
		<div class="masterstudy-orders__loader">
			<div class="masterstudy-orders__loader-body"></div>
		</div>
		<template id="masterstudy-order-template">
			<div class="masterstudy-orders-table">
				<div class="masterstudy-orders-table__header">
					<div class="masterstudy-orders-course-info">
						<div class="masterstudy-orders-course-info__id" data-order-id></div>
						<div class="order-status" data-order-status></div>
					</div>
					<div class="masterstudy-orders-course-info">
						<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ); ?>:</div>
						<div class="masterstudy-orders-course-info__value" data-order-date></div>
					</div>
					<div class="masterstudy-orders-course-info">
						<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Payment Method', 'masterstudy-lms-learning-management-system-pro' ); ?>:</div>
						<div class="masterstudy-orders-course-info__value" data-order-payment></div>
					</div>
					<div class="masterstudy-orders-course-info__details">
					<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'title' => esc_html__( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
								'link'  => '#',
								'style' => 'primary',
								'size'  => 'sm',
							)
						);
						?>
					</div>
				</div>
				<div class="masterstudy-orders-table__body"></div>
				<div class="masterstudy-orders-table__footer">
					<div class="masterstudy-orders-course-info">
						<div class="masterstudy-orders-course-info__block">
							<div class="masterstudy-orders-course-info__label"><?php echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' ); ?>:</div>
							<div class="masterstudy-orders-course-info__price" data-order-total></div>
						</div>
					</div>
				</div>
			</div>
		</template>
	</div>
	<div class="masterstudy-orders-table-navigation">
		<div class="masterstudy-orders-table-navigation__pagination"></div>
	</div>
</div>
