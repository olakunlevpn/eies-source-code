<?php
$header_search_categories = stm_option( 'header_search_categories_switch', true );
$is_mobile                = wp_is_mobile() && stm_get_layout_is_mobile();

if ( stm_option( 'online_show_search', true ) ) : ?>
	<div class="stm_courses_search">
		<?php
		if ( ! empty( $header_search_categories ) && $is_mobile ) {
			get_template_part( 'partials/headers/parts/categories' );
		} elseif ( ! $is_mobile ) {
			get_template_part( 'partials/headers/parts/categories' );
		}
		?>
		<?php get_template_part( 'partials/headers/parts/courses-search' ); ?>
	</div>
<?php endif; ?>

<?php if ( stm_option( 'online_show_links', true ) ) : ?>
	<div class="stm_header_links">
		<?php get_template_part( 'partials/headers/parts/links' ); ?>
	</div>
<?php endif; ?>
