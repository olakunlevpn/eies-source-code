<?php
/**
 * @var $datepicker
 * @var $label_color
 * @var $css
 * @var $css_class
 */

$countdown = wp_rand( 0, 999999 );
$site_tz   = wp_timezone();
try {
	$dt    = new DateTime( $datepicker, $site_tz );
	$ts_ms = $dt->setTimezone( new DateTimeZone( 'UTC' ) )->getTimestamp() * 1000;
} catch ( Exception $e ) {
	$ts_ms = 0;
}

wp_enqueue_script( 'jquery.countdown' );
stm_module_styles( 'countdown' );
?>
	<div class="text-center <?php echo esc_attr( $css_class ); ?>">
		<div class="stm_countdown" id="countdown_<?php echo esc_attr( $countdown ); ?>"></div>
	</div>

<?php if ( ! empty( $datepicker ) ) : ?>
	<script>
		jQuery(function ($) {
			var flash = false;
			var ts = <?php echo esc_attr( $ts_ms ); ?>;
			var timeUp = '<?php echo esc_html__( 'Time is up, sorry!', 'masterstudy' ); ?>';
			if ((new Date()) < ts) {
				$('#countdown_<?php echo esc_attr( $countdown ); ?>').countdown({
					timestamp: ts,
					callback: function (days, hours, minutes, seconds) {
						var summaryTime = days + hours + minutes + seconds;
						if (summaryTime === 0) {
							$('#countdown_<?php echo esc_attr( $countdown ); ?>').html('<div class="countdown_ended h2">' + timeUp + '</div>');
						}
					}
				});
			} else {
				$('#countdown_<?php echo esc_attr( $countdown ); ?>').html('<div class="countdown_ended h2">' + timeUp + '</div>');
			}
		});
	</script>
<?php endif; ?>

<?php if ( ! empty( $label_color ) ) : ?>
	<style>
		.stm_countdown .countdown_label {
			color: <?php echo esc_attr( $label_color ); ?> !important;
		}
	</style>
<?php endif; ?>
