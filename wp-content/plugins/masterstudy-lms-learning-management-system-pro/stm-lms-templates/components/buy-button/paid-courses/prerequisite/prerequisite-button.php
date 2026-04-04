<?php
/**
 * @var array $courses
*/

$settings    = get_option( 'stm_lms_settings' );
$theme_fonts = $settings['course_player_theme_fonts'] ?? false;
if ( empty( $theme_fonts ) ) {
	wp_enqueue_style( 'masterstudy-buy-button-prerequisites-fonts' );
}
wp_enqueue_style( 'masterstudy-buy-button-prerequisites' );
wp_enqueue_script( 'masterstudy-buy-button-prerequisites' );
?>
<div class="masterstudy-prerequisites">
	<a href="#" class="masterstudy-prerequisites__button">
		<span><?php echo esc_html__( 'Prerequisites', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
	</a>

	<ul class="masterstudy-prerequisites-list">
		<?php
		foreach ( $courses as $course ) {
			$course_id = $course['course_id'];
			$progress  = $course['progress_percent'];
			?>
			<li class="masterstudy-prerequisites-list__item">
				<a href="<?php the_permalink( $course_id ); ?>" class="masterstudy-prerequisites-list__title">
					<?php echo esc_html( get_the_title( $course_id ) ); ?>
				</a>
				<?php
				if ( empty( $progress ) ) {
					$price              = get_post_meta( $course_id, 'price', true );
					$sale_price         = STM_LMS_Course::get_sale_price( $course_id );
					$single_sale        = get_post_meta( $course_id, 'single_sale', true );
					$not_in_membership  = get_post_meta( $course_id, 'not_membership', true );
					$course_free_status = function_exists( 'masterstudy_lms_course_free_status' )
						? masterstudy_lms_course_free_status( $course_id, $price )
						: true;
					$has_price_info     = $single_sale && ! $course_free_status['zero_price'];

					if ( empty( $price ) && ! empty( $sale_price ) ) {
						$price      = $sale_price;
						$sale_price = '';
					}

					if ( ! empty( $price ) && ! empty( $sale_price ) ) {
						list( $price, $sale_price ) = array( $sale_price, $price );
					}

					if ( ! $single_sale && STM_LMS_Subscriptions::subscription_enabled() && ! $not_in_membership ) {
						?>
						<div class="masterstudy-prerequisites-list__progress">
							<label class="price"><?php echo esc_html__( 'Members only', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
						</div>
						<?php
					} else {
						if ( ! empty( $price ) || ! empty( $sale_price ) ) {
							?>
							<div class="masterstudy-prerequisites-list__progress">
								<?php if ( $has_price_info && ! empty( $sale_price ) ) { ?>
									<span class="masterstudy-course-sale-price"><?php echo esc_html( STM_LMS_Helpers::display_price( $sale_price ) ); ?></span>
									<?php
								}
								if ( $has_price_info && ! empty( $price ) ) {
									?>
									<span class="masterstudy-course-price"><?php echo esc_html( STM_LMS_Helpers::display_price( $price ) ); ?></span>
								<?php } ?>
							</div>
						<?php } else { ?>
							<div class="masterstudy-prerequisites-list__progress">
								<?php if ( $course_free_status['is_free'] ) { ?>
									<label class="price"><?php echo esc_html__( 'Free', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
								<?php } ?>
							</div>
							<?php
						}
					}
				} else {
					?>
					<div class="masterstudy-prerequisites-list__progress-percent">
						<div class="masterstudy-prerequisites-list__progress-percent-striped"
							role="progressbar"
							aria-valuenow="45"
							aria-valuemin="0"
							aria-valuemax="100"
							style="width: <?php echo intval( $progress ); ?>%">
						</div>
					</div>
					<span class="masterstudy-prerequisites-list__enrolled progress-started"><?php echo esc_html__( 'Enrolled', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				<?php } ?>
			</li>
		<?php } ?>
		<li class="masterstudy-prerequisites-list__explanation">
			<div class="masterstudy-prerequisites-list__explanation-title">
				<i class="stmlms-question-2-circle"></i>
				<?php echo esc_html__( 'What is Prerequisite courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-prerequisites-list__explanation-info">
				<?php echo esc_html__( 'A prerequisite is a specific course  that you must complete before you can take another course at the next grade level.', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
		</li>
	</ul>
</div>
