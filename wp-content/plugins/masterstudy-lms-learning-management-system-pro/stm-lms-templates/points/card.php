<?php
/**
 * @var $point
 */
?>

<div class="masterstudy-points__card">
	<div class="masterstudy-points__card-block">
		<?php echo esc_html( $point['data']['label'] ); ?>
	</div>
	<div class="masterstudy-points__card-block">
		<?php echo esc_html( $point['title'] ); ?>
	</div>
	<div class="masterstudy-points__card-block">
		<?php echo esc_html( $point['timestamp'] ); ?>
	</div>
	<div class="masterstudy-points__card-block">
		<?php echo esc_html( $point['score'] ); ?>
	</div>
</div>
