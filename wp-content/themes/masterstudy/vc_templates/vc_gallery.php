<?php
/**
 * Shortcode attributes
 * @var $atts
*/

$output = '';

extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	shortcode_atts(
		array(
			'title'               => '',
			'type'                => 'image_grid',
			'onclick'             => 'link_image',
			'custom_links'        => '',
			'custom_links_target' => '',
			'img_size'            => 'thumbnail',
			'thumbnail_size'      => 'thumb-176x104',
			'images'              => '',
			'el_class'            => '',
			'interval'            => '5',
			'css'                 => '',
		),
		$atts
	)
);
$gal_images        = '';
$gal_images_nav    = '';
$link_start        = '';
$link_end          = '';
$el_start          = '';
$el_end            = '';
$slides_wrap_start = '';
$slides_wrap_end   = '';
$css_class         = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );
$el_class          = $this->getExtraClass( $el_class );
if ( 'nivo' === $type ) {
	$type = ' wpb_slider_nivo theme-default';
	wp_enqueue_script( 'nivo-slider' );
	wp_enqueue_style( 'nivo-slider-css' );
	wp_enqueue_style( 'nivo-slider-theme' );

	$slides_wrap_start = '<div class="nivoSlider">';
	$slides_wrap_end   = '</div>';
} elseif ( 'flexslider' === $type || 'flexslider_fade' === $type || 'flexslider_slide' === $type || 'fading' === $type ) {
	$el_start          = '<li>';
	$el_end            = '</li>';
	$slides_wrap_start = '<ul class="slides">';
	$slides_wrap_end   = '</ul>';
	wp_enqueue_style( 'flexslider' );
	wp_enqueue_script( 'flexslider' );
} elseif ( 'image_grid' === $type ) {
	wp_enqueue_script( 'isotope' );

	$el_start          = '<li class="isotope-item">';
	$el_end            = '</li>';
	$slides_wrap_start = '<ul class="wpb_image_grid_ul">';
	$slides_wrap_end   = '</ul>';
} elseif ( 'slick_slider' === $type || 'slick_slider_2' === $type ) {
	wp_enqueue_script( 'slick' );
	wp_enqueue_style( 'slick' );
}

if ( 'link_image' === $onclick ) {
	wp_enqueue_script( 'prettyphoto' );
	wp_enqueue_style( 'prettyphoto' );
}

$flex_fx = '';
if ( 'flexslider' === $type || 'flexslider_fade' === $type || 'fading' === $type ) {
	$type    = ' wpb_flexslider flexslider_fade flexslider';
	$flex_fx = ' data-flex_fx="fade"';
} elseif ( 'flexslider_slide' === $type ) {
	$type    = ' wpb_flexslider flexslider_slide flexslider';
	$flex_fx = ' data-flex_fx="slide"';
} elseif ( 'image_grid' === $type ) {
	$type = ' wpb_image_grid';
}

if ( empty( $images ) ) {
	$images = '-1,-2,-3';
}

$rand_id           = wp_rand();
$pretty_rel_random = ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . $rand_id . ']"';

if ( 'custom_link' === $onclick ) {
	$custom_links = explode( ',', $custom_links );
}
$images       = explode( ',', $images );
$i            = - 1;
$images_count = 0;
foreach ( $images as $attach_id ) {
	$i ++;
	$image_post = '';
	if ( $attach_id > 0 ) {
		$post_thumbnail     = wpb_getImageBySize(
			array(
				'attach_id'  => $attach_id,
				'thumb_size' => $img_size,
			)
		);
		$post_thumbnail_nav = wpb_getImageBySize(
			array(
				'attach_id'  => $attach_id,
				'thumb_size' => $thumbnail_size,
			)
		);
		$image_post         = get_post( $attach_id );
		$images_count++;
	} else {
		$post_thumbnail_nav               = array();
		$post_thumbnail['thumbnail']      = '<img src="' . vc_asset_url( 'vc/no_image.png' ) . '" />';
		$post_thumbnail['p_img_large'][0] = vc_asset_url( 'vc/no_image.png' );
	}

	$thumbnail   = $post_thumbnail['thumbnail'];
	$p_img_large = $post_thumbnail['p_img_large'];

	$link_start = '<div class="item">';
	$link_end   = '</div>';

	if ( 'link_image' === $onclick ) {
		$link_start = '<a class="prettyphoto" href="' . esc_url( $p_img_large[0] ) . '"' . $pretty_rel_random . '>';
		$link_end   = '</a>';
	} elseif ( 'custom_link' === $onclick && ! empty( $custom_links[ $i ] ) ) {
		$link_start = '<a href="' . $custom_links[ $i ] . '"' . ( ! empty( $custom_links_target ) ? ' target="' . $custom_links_target . '"' : '' ) . '>';
		$link_end   = '</a>';
	}

	if ( 'slick_slider_2' === $type && $image_post ) {
		$link_start .= '<span class="image_title">' . get_the_title( $image_post->ID ) . '</span>';
	}

	$gal_images     .= $el_start . $link_start . $thumbnail . $link_end . $el_end;
	$gal_images_nav .= $el_start . '<div><div class="slick-slide-wr">' . $post_thumbnail_nav['thumbnail'] . '</div></div>' . $el_end;
}

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_gallery wpb_content_element' . $el_class . ' ' . $css_class . ' vc_clearfix', $this->settings['base'], $atts );
$output   .= "\n\t" . '<div class="' . $css_class . '">';
$output   .= "\n\t\t" . '<div class="wpb_wrapper">';
if ( $title ) {
	$output .= '<h5>' . $title . '</h5>';
}
if ( 'slick_slider' === $type || 'slick_slider_2' === $type ) {
	$output .= '<div id="image_carousel-' . $rand_id . '" class="wpb_gallery_slides' . $type . ' slider_main">' . $slides_wrap_start . $gal_images . $slides_wrap_end . '</div>';
} else {
	$output .= '<div class="wpb_gallery_slides' . $type . '" data-interval="' . $interval . '"' . $flex_fx . '>' . $slides_wrap_start . $gal_images . $slides_wrap_end . '</div>';
}
if ( 'slick_slider_2' === $type ) {
	$output .= '<div id="image_carousel-nav-' . $rand_id . '" class="wpb_gallery_slides_nav' . $type . ' slider_nav">' . $slides_wrap_start . $gal_images_nav . $slides_wrap_end . '</div>';
}
if ( 'slick_slider' === $type ) {
	ob_start();
	?>
	<script type="text/javascript">
		jQuery( document ).ready(function($) {
			"use strict";
			let $element  = $( "#image_carousel-<?php echo esc_js( $rand_id ); ?>" );
			$element.slick({
				infinite: true,
				adaptiveHeight: true,
				prevArrow: "<div class=\"slick_prev\"><i class=\"fa fa-chevron-left\"></i></div>",
				nextArrow: "<div class=\"slick_next\"><i class=\"fa fa-chevron-right\"></i></div>",
				cssEase: "cubic-bezier(0.455, 0.030, 0.515, 0.955)"
			});
		});
	</script>
	<?php
	$output .= ob_get_clean();
}

if ( 'slick_slider_2' === $type ) {
	ob_start();
	?>
	<script type="text/javascript">
		jQuery( document ).ready(function($) {
			"use strict";
			let $element = $("#image_carousel-<?php echo esc_js( $rand_id ); ?>"),
				slick_nav = $("#image_carousel-nav-<?php echo esc_js( $rand_id ); ?>"),
				slick_current_slide = 1;

			$element.on("init", function(){
				$element.append("<div class=\'slider_info\'><span>" + slick_current_slide + "</span> / <em><?php echo esc_js( $images_count ); ?></em></div>");
			});

			$element.on("afterChange", function(){
				slick_current_slide = $(this).slick("slickCurrentSlide") + 1;
				$element.find(".slider_info span").text( slick_current_slide );
				slick_nav.find(".slick-slide.slick-active:first").addClass("stm-slick-active");
			});

			$element.on("beforeChange", function(){
				slick_nav.find(".slick-slide.stm-slick-active").removeClass("stm-slick-active");
			});

			$element.slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				adaptiveHeight: true,
				prevArrow: "<div class=\"slick_prev\"><i class=\"fa fa-chevron-left\"></i></div>",
				nextArrow: "<div class=\"slick_next\"><i class=\"fa fa-chevron-right\"></i></div>",
				asNavFor: "#image_carousel-nav-<?php echo esc_js( $rand_id ); ?>",
				fade: true
			});

			slick_nav.slick({
				slidesToShow: 6,
				asNavFor: "#image_carousel-<?php echo esc_js( $rand_id ); ?>",
				focusOnSelect: true,
				dots: false,
				arrows: false
			});

		});
	</script>
	<?php
	$output .= ob_get_clean();
}

$output .= "\n\t\t" . '</div> ';
$output .= "\n\t" . '</div> ';

echo masterstudy_filtered_output( $output ); // phpcs:ignore
