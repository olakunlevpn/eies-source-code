<?php

/**
 * @var int $max_visible_pages
 * @var int $total_pages
 * @var int $current_page
 * @var boolean $done_indicator
 * @var boolean $dark_mode
 * @var boolean $is_api
 * @var boolean $thin
 *
 * masterstudy-pagination_dark-mode - for dark mode
 * masterstudy-pagination__item_current - for current page
 */

$is_ajax = $is_ajax ?? false;
$is_api  = $is_api ?? false;
$thin    = $thin ?? false;

$item_width = $thin ? 30 : 40;

wp_enqueue_style( 'masterstudy-pagination' );
if ( $is_ajax ) {
	wp_enqueue_script( 'masterstudy-ajax-pagination' );
} elseif ( $is_api ) {
	wp_enqueue_script( 'masterstudy-api-pagination' );
} else {
	wp_enqueue_script( 'masterstudy-pagination' );
}
wp_localize_script(
	$is_api ? 'masterstudy-api-pagination' : 'masterstudy-pagination',
	'pages_data',
	array(
		'max_visible_pages' => $max_visible_pages,
		'total_pages'       => $total_pages,
		'current_page'      => $current_page,
		'is_queryable'      => $is_queryable ?? false,
		'item_width'        => $item_width,
		'is_api'            => $is_api,
	)
);
?>

<div class="masterstudy-pagination <?php echo esc_attr( $dark_mode ? 'masterstudy-pagination_dark-mode' : '' ); ?> <?php echo esc_attr( $thin ? 'masterstudy-pagination_thin' : '' ); ?>">
	<span class="masterstudy-pagination__button-prev <?php echo esc_attr( 1 === $current_page ? 'masterstudy-pagination__button_disabled' : '' ); ?>"></span>
	<div class="masterstudy-pagination__wrapper"
		data-width="<?php echo esc_attr( ( $max_visible_pages < $total_pages ? ( $item_width + 10 ) * $max_visible_pages : ( $item_width + 10 ) * $total_pages ) . 'px' ); ?>">
		<ul class="masterstudy-pagination__list">
			<?php foreach ( range( 1, $total_pages ) as $index ) { ?>
				<li class="masterstudy-pagination__item <?php echo esc_attr( ( $index ) === $current_page ? 'masterstudy-pagination__item_current' : '' ); ?>">
					<span class="masterstudy-pagination__item-block" data-id="<?php echo esc_attr( $index ); ?>">
						<?php if ( $done_indicator ) { ?>
							<span class="masterstudy-pagination__item-indicator"></span>
							<?php
						}
						echo esc_html( $index );
						?>
					</span>
				</li>
			<?php } ?>
		</ul>
	</div>
	<span class="masterstudy-pagination__button-next"></span>
</div>
