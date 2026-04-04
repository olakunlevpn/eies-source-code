<?php
/**
 * Shortcode attributes
 * @var $atts
 */

$output   = '';
$size     = '';
$el_class = '';
$_preview = '';

extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	shortcode_atts(
		array(
			'title'    => '',
			'link'     => 'https://vimeo.com/92033601',
			'image'    => '',
			'el_class' => '',
			'css'      => '',

		),
		$atts
	)
);
if ( empty( $link ) ) {
	return null;
}
$el_class = $this->getExtraClass( $el_class );

// Video Preview
if ( ! empty( $image ) ) {
	$_preview = wp_get_attachment_image_src( $image, 'full' );
	if ( ! empty( reset( $_preview ) ) ) {
		$_preview = reset( $_preview );
	}
	$preview_hidden = '';
} else {
	$preview_hidden = 'preview_hidden';
}

global $wp_embed;
$embed = '<iframe width="950" height="534" data-src="' . $link . '?feature=oembed" allow="autoplay" frameborder="0" allowfullscreen=""></iframe>';


$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_video_widget wpb_content_element' . $el_class . $el_class . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );

if ( ! empty( $_preview ) ) {
	$output .= "\n\t" . '<div class="stm_video_wrapper">';
	$output .= "\n\t" . '<div class="' . $css_class . '">';
	$output .= "\n\t\t" . '<div class="wpb_wrapper">';
	if ( ! empty( $title ) ) :
		$output .= "\n\t" . '<div class="stm_video_wrapper_title">';
		$output .= wpb_widget_title(
			array(
				'title'      => $title,
				'extraclass' => 'wpb_video_heading',
			)
		);
		$output .= "\n\t" . '</div> ';
	endif;
	$output .= '<div class="stm_theme_wpb_video_wrapper">';
	if ( ! empty( $_preview ) ) :
		$output .= '<div class="stm_video_preview" style="background-image:url(' . $_preview . ')"></div>';
	endif;
	$output .= '<div class="wpb_video_wrapper ' . $preview_hidden . '">' . $embed . '</div></div>';
	$output .= "\n\t\t" . '</div> ';
	$output .= "\n\t" . '</div> ';
	$output .= "\n\t" . '</div> ';
	echo masterstudy_filtered_output( $output ); // phpcs:ignore
} else { ?>
	<iframe width="100%" height="400" src="<?php echo esc_url( $link ); ?>?feature=oembed" allow="autoplay" frameborder="0" allowfullscreen=""></iframe>
	<?php
}
?>

<?php if ( ! empty( $link ) ) : ?>
	<script>
		(function($) {
			"use strict";

			$(document).ready(function ($) {
				stmPlayIframeVideo();
			});

			/* Custom func */
			function stmPlayIframeVideo() {
				$('.stm_video_preview').on('click', function(){
					$(this).addClass('video_preloader_hidden');
					var addPlay = $(this).closest('.stm_video_wrapper').find('iframe').attr('data-src');
					$(this).closest('.stm_video_wrapper').find('.wpb_video_wrapper').addClass('video_autoplay_true');
					$(this).closest('.stm_video_wrapper').find('iframe').attr('src', addPlay + '&autoplay=1');
				});
			};

		})(jQuery);
	</script>
<?php endif; ?>
