<?php
/**
 * @var int $course_id
*/

$user         = STM_LMS_User::get_current_user();
$user_points  = STM_LMS_Point_System::total_points( $user['id'] );
$course_price = STM_LMS_Point_System::course_price( $course_id );
$settings     = get_option( 'stm_lms_settings' );
$theme_fonts  = $settings['course_player_theme_fonts'] ?? false;

if ( empty( $theme_fonts ) ) {
	wp_enqueue_style( 'masterstudy-buy-button-points-fonts' );
}

wp_enqueue_style( 'masterstudy-buy-button-points' );
wp_enqueue_script( 'masterstudy-buy-button-points' );
?>

<script>
	window.masterstudy_buy_button_points = window.masterstudy_buy_button_points || [];
	window.masterstudy_buy_button_points.push({
		ajax_url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
		get_nonce: "<?php echo esc_attr( wp_create_nonce( 'stm_lms_buy_for_points' ) ); ?>",
		course_id: "<?php echo esc_attr( $course_id ); ?>",
		translate: {
			confirm:
				<?php
				// phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
				echo wp_json_encode(
					sprintf(
						/* translators: %s string, %s string */
						esc_html__( 'Do you really want to buy %1$s for %2$s?', 'masterstudy-lms-learning-management-system-pro' ),
						get_the_title( $course_id ),
						STM_LMS_Point_System::display_points( $course_price )
					),
					JSON_UNESCAPED_UNICODE
				);
				?>
		}
	});
</script>
<?php

$classes = array( 'masterstudy-points-button' );

if ( $user_points < $course_price ) {
	$classes[] = 'masterstudy-points-button_not-enough-points';
}

$distribution = sprintf(
	'<span class="masterstudy-points__icon" data-href="%s"><i class="stmlms-question-2-circle"></i></span>',
	esc_url( ms_plugin_user_account_url( 'points-distribution' ) )
);

if ( empty( $course_price ) ) {
	return;
}
?>
<div class="masterstudy-points__container">
	<div class="masterstudy-points__info">
		<?php echo wp_kses_post( STM_LMS_Point_System::display_point_image() ); ?>
		<div class="masterstudy-points__price">
			<?php echo esc_html( STM_LMS_Point_System::display_points( $course_price ) ); ?>
		</div>
	</div>
	<div class="masterstudy-points__text">
		<?php
		if ( $user_points < $course_price ) {
			printf(
				/* translators:  %1$s Points %2$s Distribution */
				esc_html__( 'You need %1$s. %2$s', 'masterstudy-lms-learning-management-system-pro' ),
				wp_kses_post( STM_LMS_Point_System::display_points( $course_price - $user_points ) ),
				wp_kses_post( $distribution )
			);
		} else {
			printf(
				/* translators:  %1$s Points %2$s Distribution */
				esc_html__( 'You have %1$s. %2$s', 'masterstudy-lms-learning-management-system-pro' ),
				wp_kses_post( STM_LMS_Point_System::display_points( $user_points ) ),
				wp_kses_post( $distribution )
			);
		}
		?>
	</div>
	<a href="#" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-course="<?php echo esc_attr( $course_id ); ?>">
		<span class="masterstudy-points-button__title">
			<?php echo esc_html__( 'Buy Course', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</span>
	</a>
</div>
