<?php
/**
 * @var string $label
 * @var string $value
 * @var string $icon
 */
?>

<div class="masterstudy-course-player-assignments__meta-item">
	<span class="masterstudy-course-player-assignments__meta-item-icon <?php echo esc_attr( $icon ); ?>"></span>
	<span class="masterstudy-course-player-assignments__meta-item-title">
		<?php echo esc_html( $label ); ?>
		<span class="masterstudy-course-player-assignments__meta-item-value"><?php echo esc_html( $value ); ?></span>
	</span>
</div>
