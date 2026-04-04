<?php
/**
 * Shortcode attributes
 * @var $atts
 */

extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	shortcode_atts(
		array(
			'title'        => '',
			'title_align'  => '',
			'align'        => 'align_center',
			'el_width'     => '',
			'border_width' => '',
			'style'        => '',
			'color'        => '',
			'accent_color' => '',
			'el_class'     => '',
			'layout'       => 'separator_with_text',
			'type'         => 'type_1',
			'css'          => '',
		),
		$atts
	)
);
$class = 'vc_separator wpb_content_element';

$class .= ! empty( $title_align ) ? ' vc_' . $title_align : '';
$class .= ! empty( $el_width ) ? ' vc_sep_width_' . $el_width : ' vc_sep_width_100';
$class .= ! empty( $style ) ? ' vc_sep_' . $style : '';
$class .= ! empty( $border_width ) ? ' vc_sep_border_width_' . $border_width : '';
$class .= ! empty( $align ) ? ' vc_sep_pos_' . $align : '';
$class .= ' ' . $type;
$class .= ' ' . vc_shortcode_custom_css_class( $css, ' ' );

$class .= ( 'separator_no_text' === $layout ) ? ' vc_separator_no_text' : '';
if ( ! empty( $color ) && 'custom' !== $color ) {
	$class .= ' vc_sep_color_' . $color;
}
$inline_css = 'custom' === $color && ! empty( $accent_color ) ? ' style="' . vc_get_css_color( 'border-color', $accent_color ) . '"' : '';

$class    .= $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );

if ( 'type_1' === $type ) :
	?>
	<div class="<?php echo esc_attr( trim( $css_class ) ); ?>">
		<span class="vc_sep_holder vc_sep_holder_l">
			<span<?php echo wp_kses_post( masterstudy_filtered_output( $inline_css ) ); ?> class="vc_sep_line"></span>
		</span>
		<?php if ( ! empty( $title ) ) : ?>
			<h4><?php echo esc_html( $title ); ?></h4>
		<?php endif; ?>
		<span class="vc_sep_holder vc_sep_holder_r">
			<span<?php echo wp_kses_post( masterstudy_filtered_output( $inline_css ) ); ?> class="vc_sep_line"></span>
		</span>
	</div>
<?php else : ?>
	<div class="<?php echo esc_attr( $css_class ); ?>">
		<?php if ( ! empty( $title ) ) : ?>
			<h4><?php echo esc_html( $title ); ?></h4>
		<?php endif; ?>
	</div>
<?php endif; ?>
