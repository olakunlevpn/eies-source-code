<?php
use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Enums\ReccuringInterval;

$currency_symbol = \STM_LMS_Options::get_option( 'currency_symbol', '$' );
?>
<div class="masterstudy-upsert-subscription masterstudy-upsert-subscription_hidden">
	<div class="masterstudy-upsert-subscription__block">
		<div class="masterstudy-upsert-subscription__header">
			<div class="masterstudy-upsert-subscription__header-title">
				<?php echo esc_html__( 'New Subscription Plan', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-upsert-subscription__header-buttons">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => __( 'Cancel', 'masterstudy-lms-learning-management-system-pro' ),
						'style' => 'primary',
						'size'  => 'sm',
						'class' => 'masterstudy-upsert-subscription__header-buttons-cancel',
					)
				);
				?>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => __( 'Save', 'masterstudy-lms-learning-management-system-pro' ),
						'style' => 'primary',
						'size'  => 'sm',
						'class' => 'masterstudy-upsert-subscription__header-buttons-save',
					)
				);
				?>
			</div>
		</div>
		<div class="masterstudy-upsert-subscription__content">
			<div class="masterstudy-upsert-subscription__content-block">
				<div class="masterstudy-upsert-subscription__content-block-title">
					<h2 for="masterstudy-subscription-title"><?php echo esc_html__( 'Plan Info', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
				</div>
				<div class="masterstudy-upsert-subscription__content-block-content">
					<div class="masterstudy-upsert-subscription__content-input">
						<label for="masterstudy-subscription-title"><?php echo esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
						<input id="masterstudy-subscription-title" placeholder="<?php echo esc_html__( 'Enter Title', 'masterstudy-lms-learning-management-system-pro' ); ?>"/>
					</div>
				</div>
			</div>

			<div class="masterstudy-upsert-subscription__content-block">
				<div class="masterstudy-upsert-subscription__content-block-title">
					<h2 for="masterstudy-subscription-title"><?php echo esc_html__( 'Pricing', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
				</div>
				<div class="masterstudy-upsert-subscription__content-block-content">
					<div class="masterstudy-upsert-subscription__content-input">
						<label for="masterstudy-subscription-price"><?php echo esc_html__( 'Price', 'masterstudy-lms-learning-management-system-pro' ) . esc_attr( "($currency_symbol)" ); ?></label>
						<div style="position: relative">
							<input step="0.01" value="1" min="1" id="masterstudy-subscription-price" type="number" placeholder="<?php echo esc_html__( '1', 'masterstudy-lms-learning-management-system-pro' ); ?>"/>
							<span class="masterstudy-pricing-item__arrow-top"></span>
							<span class="masterstudy-pricing-item__arrow-down"></span>
						</div>
					</div>
					<div class="masterstudy-upsert-subscription__content-input">
						<label for="masterstudy-subscription-billing-interval"><?php echo esc_html__( 'Billing Interval', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
						<div style="position: relative">
							<input id="masterstudy-subscription-billing-interval" type="number" value="1" min="1" placeholder="<?php echo esc_html__( '1', 'masterstudy-lms-learning-management-system-pro' ); ?>"/>
							<span class="masterstudy-pricing-item__arrow-top"></span>
							<span class="masterstudy-pricing-item__arrow-down"></span>
						</div>
					</div>
					<div class="masterstudy-upsert-subscription__content-input">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/select',
							array(
								'select_id'     => 'interval_select',
								'select_name'   => 'interval_select',
								'default'       => 'month',
								'apply_default' => true,
								'options'       => ReccuringInterval::get_translate_options(),
							)
						);
						?>
					</div>
				</div>
			</div>

			<div class="masterstudy-upsert-subscription__content-block">
				<div class="masterstudy-upsert-subscription__content-block-title">
					<h2 for="masterstudy-subscription-title"><?php echo esc_html__( 'Additional', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
				</div>
				<div class="masterstudy-upsert-subscription__content-block-content">
					<div class="masterstudy-upsert-subscription__content-toggle-block">
						<div class="masterstudy-upsert-subscription__content-toggle masterstudy-upsert-subscription__content-featured">
							<?php
							STM_LMS_Templates::show_lms_template(
								'components/switcher',
								array(
									'name' => 'mark_as_featured',
									'on'   => false,
								)
							);
							?>
							<p><?php echo esc_html__( 'Mark as featured', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
						</div>
						<input id="featured_text" class="masterstudy-upsert-subscription__content-input" type="text" placeholder="Popular" />
					</div>
				</div>
			</div>

			<div class="masterstudy-upsert-subscription__content-block">
				<div class="masterstudy-upsert-subscription__content-block-title">
					<h2 for="masterstudy-subscription-title"><?php echo esc_html__( 'Plan Summary:', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
				</div>
				<div class="masterstudy-upsert-subscription__content-block-content">
					<div class="masterstudy-upsert-subscription__content-summary">
						<div class="masterstudy-upsert-subscription__summary-featured-text"></div>
						<div class="masterstudy-upsert-subscription__summary-title"></div>
						<div>
							<span class="masterstudy-upsert-subscription__summary-price"></span>
							<span class="masterstudy-upsert-subscription__summary-interval"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
