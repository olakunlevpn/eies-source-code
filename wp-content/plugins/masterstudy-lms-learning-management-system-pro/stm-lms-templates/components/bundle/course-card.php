<?php
/**
 * @var array $course
 * @var bool $removable
 * @var bool $selectable
 */

wp_enqueue_style( 'masterstudy-bundle-course-card' );
?>

<div id="<?php echo esc_attr( $course['id'] ); ?>" class="masterstudy-bundle-course">
	<span class="masterstudy-bundle-course__checkbox <?php echo esc_attr( $selectable ? 'masterstudy-bundle-course__checkbox_show' : '' ); ?>"></span>
	<?php echo wp_kses_post( $course['image'] ); ?>
	<div class="masterstudy-bundle-course__content">
		<span class="masterstudy-bundle-course__title">
			<?php echo esc_html( $course['title'] ); ?>
		</span>
		<div class="masterstudy-bundle-course__price">
			<?php
			if ( ! empty( $course['sale_price'] ) ) {
				?>
				<span class="masterstudy-bundle-course__sale-single"><?php echo esc_html( $course['sale_price'] ); ?></span>
				<?php
			} if ( ! empty( $course['price'] ) ) {
				?>
				<span class="masterstudy-bundle-course__price-single <?php echo esc_attr( ! empty( $course['sale_price'] ) ? 'masterstudy-bundle-course__price-single_discounted' : '' ); ?>">
					<?php echo esc_html( $course['price'] ); ?>
				</span>
			<?php } ?>
		</div>
	</div>
	<div class="masterstudy-bundle-course__trash <?php echo esc_attr( $removable ? 'masterstudy-bundle-course__trash_show' : '' ); ?>"></div>
</div>
