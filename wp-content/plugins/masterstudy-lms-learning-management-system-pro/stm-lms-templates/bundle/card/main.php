<?php
/**
 * @var $bundle
 * @var $courses
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;

wp_enqueue_style( 'masterstudy-bundle-card' );

$classes = array();

if ( count( $bundle['courses'] ) > 3 ) {
	$classes[] = 'masterstudy-bundle-card_overcoursed';
}

$bundle_rating       = CourseBundleRepository::get_bundle_rating( $bundle['id'] );
$bundle_course_price = CourseBundleRepository::get_bundle_courses_price( $bundle['id'] );
$reviews             = STM_LMS_Options::get_option( 'course_tab_reviews', true );
$stars               = range( 1, 5 );
?>

<div class="masterstudy-bundle-card <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="masterstudy-bundle-card__column">
		<img src="<?php echo esc_url( $bundle['image'] ); ?>" class="masterstudy-bundle-card__image">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title'  => esc_html__( 'View Bundle', 'masterstudy-lms-learning-management-system-pro' ),
				'link'   => esc_url( $bundle['url'] ),
				'style'  => 'primary',
				'size'   => 'sm',
				'id'     => 'bundle-link',
				'target' => '_blank',
			)
		);
		?>
	</div>
	<div class="masterstudy-bundle-card__column">
		<div class="masterstudy-bundle-card__header">
			<h3 class="masterstudy-bundle-card__title">
				<?php echo esc_html( $bundle['title'] ); ?>
			</h3>
			<span class="masterstudy-bundle-instructor-actions__modal-btn">
				<i class="stmlms-course-modal-menu"></i>
			</span>
			<?php
			STM_LMS_Templates::show_lms_template(
				'bundle/card/instructor-modal',
				array( 'bundle' => $bundle )
			);
			?>
		</div>
		<div class="masterstudy-bundle-card__bottom">
			<?php
			if ( ! empty( $bundle_rating ) && ! empty( $bundle_rating['count'] ) && $reviews ) :
				$average = round( $bundle_rating['average'] / $bundle_rating['count'], 2 );
				$percent = round( $bundle_rating['percent'] / $bundle_rating['count'], 2 );
				?>
				<div class="masterstudy-bundle-card__rating">
					<?php foreach ( $stars as $star ) { ?>
						<span class="masterstudy-bundle-card__rating-star <?php echo esc_attr( ( $star <= floor( $average ) ) ? 'masterstudy-bundle-card__rating-star_filled' : '' ); ?>"></span>
					<?php } ?>
					<div class="masterstudy-bundle-card__rating-count">
						<?php
						echo number_format( $average, 1, '.', '' );
						echo '(' . esc_html( $bundle_rating['count'] ) . ')';
						?>
					</div>
				</div>
			<?php endif; ?>
			<div class="masterstudy-bundle-card__price">
				<?php if ( ! empty( $bundle['price'] ) ) { ?>
					<span class="masterstudy-bundle-card__price-value"><?php echo esc_html( $bundle['price'] ); ?></span>
				<?php } elseif ( ! empty( $bundle['points_price'] ) ) { ?>
					<span class="masterstudy-bundle-card__price-points"><?php echo esc_html( $bundle['points_price'] ); ?></span>
				<?php } ?>
				<span class="masterstudy-bundle-card__price-courses">
					<?php echo esc_html( masterstudy_lms_display_price_with_taxes( $bundle_course_price ) ); ?>
				</span>
			</div>
		</div>
		<?php if ( ! empty( $bundle['courses'] ) ) : ?>
			<div class="masterstudy-bundle-card__courses">
				<?php
				foreach ( $bundle['courses'] as $course_id ) :
					if ( empty( $courses[ $course_id ] ) ) {
						continue;
					}
					$course_data = $courses[ $course_id ];
					?>
					<a class="masterstudy-bundle-card__course" href="<?php echo esc_url( $course_data['link'] ); ?>">
						<div class="masterstudy-bundle-card__course-image">
							<?php echo stm_lms_filtered_output( $course_data['image'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div class="masterstudy-bundle-card__course-title">
							<?php echo esc_html( $course_data['title'] ); ?>
						</div>
						<div class="masterstudy-bundle-card__course-price">
							<div class="masterstudy-bundle-card__course-price-value">
								<?php echo esc_html( ! empty( $course_data['sale_price'] ) ? $course_data['sale_price'] : $course_data['price'] ); ?>
							</div>
							<?php if ( ! empty( $course_data['sale_price'] ) ) : ?>
								<div class="masterstudy-bundle-card__course-price-value masterstudy-bundle-card__course-price-value_discounted">
									<?php echo esc_html( $course_data['price'] ); ?>
								</div>
							<?php endif; ?>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
