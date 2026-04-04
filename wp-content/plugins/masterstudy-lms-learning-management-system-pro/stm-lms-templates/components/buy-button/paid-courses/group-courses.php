<?php
/**
 * @var int $course_id
 * @var int $price
*/

$settings    = get_option( 'stm_lms_settings' );
$theme_fonts = $settings['course_player_theme_fonts'] ?? false;

if ( empty( $theme_fonts ) ) {
	wp_enqueue_style( 'masterstudy-buy-button-group-courses-fonts' );
}

wp_enqueue_style( 'masterstudy-buy-button-group-courses' );
?>

<div class="masterstudy-button-enterprise">
	<div class="masterstudy-button-enterprise__price-info">
		<div class="masterstudy-button-enterprise__price-value">
			<?php echo esc_html( masterstudy_lms_display_price_with_taxes( $price ) ); ?>
		</div>
	</div>
	<div class="masterstudy-button-enterprise__button" data-masterstudy-modal="masterstudy-group-courses-modal">
		<span><?php echo esc_html__( 'Buy for Group', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
	</div>
</div>
