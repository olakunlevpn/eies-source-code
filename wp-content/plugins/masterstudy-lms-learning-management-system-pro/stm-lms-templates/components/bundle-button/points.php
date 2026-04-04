<?php
/**
 * @var int $bundle_id
*/
$user         = STM_LMS_User::get_current_user();
$user_points  = STM_LMS_Point_System::total_points( $user['id'] );
$bundle_price = STM_LMS_Point_System::bundle_price( $bundle_id );

wp_enqueue_style( 'masterstudy-bundle-button-points' );
wp_enqueue_script( 'masterstudy-bundle-button-points' );
?>

<script>
	window.masterstudy_bundle_button_points = window.masterstudy_bundle_button_points || [];
	window.masterstudy_bundle_button_points.push({
		ajax_url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
		get_nonce: "<?php echo esc_attr( wp_create_nonce( 'stm_lms_buy_bundle_for_points' ) ); ?>",
		bundle_id: "<?php echo esc_attr( $bundle_id ); ?>",
		translate: {
			confirm:
				<?php
				// phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
				echo wp_json_encode(
					sprintf(
						/* translators: %s string, %s string */
						esc_html__( 'Do you really want to buy %1$s for %2$s?', 'masterstudy-lms-learning-management-system-pro' ),
						get_the_title( $bundle_id ),
						STM_LMS_Point_System::display_points( $bundle_price )
					),
					JSON_UNESCAPED_UNICODE
				);
				?>
		}
	});
</script>
<?php

$classes = array( 'masterstudy-points' );
if ( $user_points < $bundle_price ) {
	$classes[] = 'masterstudy-points-not-enough-points';
}

$distribution = sprintf(
	'<span class="masterstudy-points__icon" data-href="%s"><i class="stmlms-question-2-circle"></i></span>',
	esc_url( ms_plugin_user_account_url( 'points-distribution' ) )
);

if ( ! empty( $bundle_price ) ) :
	?>
	<a href="#"
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		data-bundle="<?php echo esc_attr( $bundle_id ); ?>"
	>
		<?php echo wp_kses_post( STM_LMS_Point_System::display_point_image() ); ?>
		<span class="masterstudy-points__info">
		<span class="masterstudy-points__price"><?php echo esc_html( STM_LMS_Point_System::display_points( $bundle_price ) ); ?></span>
			<span class="masterstudy-points__text">
				<?php
				if ( $user_points < $bundle_price ) {
					printf(
						/* translators:  %1$s Points %2$s Distribution */
						esc_html__( 'You need %1$s. %2$s', 'masterstudy-lms-learning-management-system-pro' ),
						wp_kses_post( STM_LMS_Point_System::display_points( $bundle_price - $user_points ) ),
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
			</span>
		</span>
	</a>
	<?php
endif;
