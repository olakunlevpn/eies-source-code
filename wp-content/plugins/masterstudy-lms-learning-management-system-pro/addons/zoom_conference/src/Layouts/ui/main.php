<?php
// phpcs:ignoreFile
get_header();
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$_post_type = get_post_type();
		if ( 'ms-zoom' === $_post_type || 'stm-zoom' === $_post_type ) {
			$shortcode = '[MSLMS_ZOOM_conference post_id="' . get_the_ID() . '" hide_content_before_start=""]';
		} else {
			$shortcode = '';
		}

		if ( $shortcode ) {
			echo do_shortcode( apply_filters( 'MSLMS_ZOOM_single_zoom_template_shortcode', $shortcode, get_the_ID() ) );
		}

	endwhile;
endif;

get_footer();
