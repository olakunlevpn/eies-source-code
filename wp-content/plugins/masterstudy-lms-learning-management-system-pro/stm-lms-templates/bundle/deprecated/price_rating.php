<?php
/**
 * @var $bundle
 */

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;

$bundle_rating       = CourseBundleRepository::get_bundle_rating( $bundle['id'] );
$bundle_course_price = CourseBundleRepository::get_bundle_courses_price( $bundle['id'] );
$reviews             = STM_LMS_Options::get_option( 'course_tab_reviews', true );
?>

<?php
if ( ! empty( $bundle_rating ) && ! empty( $bundle_rating['count'] ) && $reviews ) :
	$average = round( $bundle_rating['average'] / $bundle_rating['count'], 2 );
	$percent = round( $bundle_rating['percent'] / $bundle_rating['count'], 2 );
	?>
	<div class="stm_lms_single_bundle_card__rating heading_font">
		<div class="average-rating-stars__top">
			<div class="star-rating">
			<span style="width:<?php echo esc_attr( $percent ); ?>%">
				<strong class="rating"><?php echo esc_attr( $average ); ?></strong>
			</span>
			</div>
			<div class="average-rating-stars__av heading_font">
				<?php echo esc_html( $average ); ?> (<?php echo esc_html( $bundle_rating['count'] ); ?>)
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="stm_lms_single_bundle_card__price heading_font">
	<?php if ( ! empty( $bundle['price'] ) ) { ?>
		<span class="bundle_price"><?php echo esc_html( $bundle['price'] ); ?></span>
	<?php } elseif ( ! empty( $bundle['points_price'] ) ) { ?>
		<span class="bundle_price"><?php echo esc_html( $bundle['points_price'] ); ?></span>
	<?php } ?>
	<span class="bundle_courses_price">
		<?php echo esc_html( masterstudy_lms_display_price_with_taxes( $bundle_course_price ) ); ?>
	</span>
</div>
