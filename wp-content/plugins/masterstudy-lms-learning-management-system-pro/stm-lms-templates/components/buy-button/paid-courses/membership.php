<?php
/**
 * @var array $plans
 * @var boolean $logged_in
 * @var boolean $guest_checkout
 * @var int $course_id
 */

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionPlanRepository;

$is_certificate_enabled = \MasterStudy\Lms\Repositories\PricingRepository::is_certificate_enabled( $course_id );
foreach ( $plans as $plan ) {
	$is_sale_active = SubscriptionPlanRepository::is_sale_active( $plan );
	$actual_price   = $is_sale_active ? $plan['sale_price'] : $plan['price'];
	?>
	<div id="<?php echo esc_attr( $plan['id'] ); ?>" class="masterstudy-membership-plan-link">
		<?php if ( ! empty( $plan['is_featured'] ) ) { ?>
			<span class="masterstudy-membership-plan__label-featured">
				<?php echo esc_html( $plan['featured_text'] ); ?>
			</span>
		<?php } ?>
		<div class="masterstudy-membership-plan__label">
			<?php echo esc_html( $plan['name'] ); ?>
		</div>
		<div class="masterstudy-membership-plan__price">
			<?php
			echo esc_html( masterstudy_lms_display_price_with_taxes( $actual_price ) );
			if ( ! empty( $plan['billing_cycles'] ) && ! empty( $plan['recurring_interval'] ) ) {
				?>
				<span class="masterstudy-membership-plan__period">
					<?php echo 'x' . esc_html( $plan['billing_cycles'] ) . ' ' . esc_html( $plan['recurring_interval'] ); ?>
				</span>
				<?php
			} elseif ( ! empty( $plan['recurring_interval'] ) ) {
				?>
				<span class="masterstudy-membership-plan__period">
					<?php echo '/' . esc_html( $plan['recurring_interval'] ); ?>
				</span>
				<?php
			}
			?>
		</div>
		<?php
		if ( $is_sale_active ) {
			?>
			<div class="masterstudy-membership-plan__old-price">
				<?php
				echo esc_html( masterstudy_lms_display_price_with_taxes( $plan['price'] ) );
				?>
			</div>
			<?php
		}
		if ( ! empty( $plan['trial_period'] ) || ! empty( $plan['is_certified'] ) ) {
			?>
			<div class="masterstudy-membership-plan__features">
				<?php
				$trial = (int) $plan['trial_period'];

				if ( $trial > 0 ) {
					?>
					<div class="masterstudy-membership-plan__features-item">
						<?php
						echo esc_html(
							sprintf(
								_n( '%s day free trial', '%s days free trial', $trial, 'masterstudy-lms-learning-management-system-pro' ),
								$trial
							)
						);
						?>
					</div>
					<?php
				} if ( ! empty( $plan['is_certified'] ) && $is_certificate_enabled ) {
					?>
					<div class="masterstudy-membership-plan__features-item">
						• <?php echo esc_html__( 'Certificate included', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
				<?php } ?>
			</div>
			<?php
		}
		?>
	</div>
<?php } ?>
<div class="masterstudy-membership-plan__button masterstudy-membership-plan__button_disabled" <?php echo ! $logged_in && ! $guest_checkout ? 'data-authorization-modal="login"' : ''; ?>>
	<span class="masterstudy-membership-plan__button-title">
		<?php echo esc_html__( 'Buy Course', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</span>
</div>
