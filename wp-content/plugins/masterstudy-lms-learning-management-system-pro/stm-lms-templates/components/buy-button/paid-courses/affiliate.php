<?php
/**
 * @var int $course_id
 */

wp_enqueue_style( 'masterstudy-buy-button-affiliate' );

$affiliate_price = get_post_meta( $course_id, 'affiliate_course_price', true );
$affiliate_text  = get_post_meta( $course_id, 'affiliate_course_text', true );
$affiliate_link  = get_post_meta( $course_id, 'affiliate_course_link', true );
$settings        = get_option( 'stm_lms_settings' );
$theme_fonts     = $settings['course_player_theme_fonts'] ?? false;
if ( empty( $theme_fonts ) ) {
	wp_enqueue_style( 'masterstudy-buy-button-affiliate-fonts' );
}
?>

<div class="masterstudy-button-affiliate">
	<a class="masterstudy-button-affiliate__link" href="<?php echo esc_url( $affiliate_link ); ?>" target="_blank">
		<span class="masterstudy-button-affiliate__title"><?php echo esc_html( sanitize_text_field( $affiliate_text ) ); ?></span>
		<span class="masterstudy-button-affiliate__separator"></span>
		<?php if ( ! empty( $affiliate_price ) ) : ?>
				<span class="masterstudy-button-affiliate__price"><?php echo esc_html( STM_LMS_Helpers::display_price( $affiliate_price ) ); ?></span>
		<?php endif; ?>
	</a>
</div>
