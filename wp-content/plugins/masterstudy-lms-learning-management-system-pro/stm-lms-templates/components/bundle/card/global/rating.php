<?php
/**
 * @var $bundle
 */

$stars        = range( 1, 5 );
$rating_sum   = isset( $bundle['rating']['average'] ) ? (float) $bundle['rating']['average'] : 0.0;
$rating_count = isset( $bundle['rating']['count'] ) ? (int) $bundle['rating']['count'] : 0;
$average      = 0.0;

if ( $rating_count > 0 ) {
	try {
		$average = round( $rating_sum / $rating_count, 2 );
	} catch ( DivisionByZeroError $e ) {
		$average = 0.0;
	}
}
?>

<div class="masterstudy-bundle-card__rating">
	<?php foreach ( $stars as $star ) { ?>
		<span class="masterstudy-bundle-card__rating-star <?php echo esc_attr( $star <= floor( $average ) ? 'masterstudy-bundle-card__rating-star_filled ' : '' ); ?>"></span>
	<?php } ?>
	<div class="masterstudy-bundle-card__rating-count">
		<?php echo number_format( $average, 1, '.', '' ); ?>
	</div>
</div>
