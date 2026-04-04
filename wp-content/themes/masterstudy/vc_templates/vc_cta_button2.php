<?php
/**
 * Shortcode attributes
 * @var $atts
 */

extract( // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	shortcode_atts(
		array(
			'h2'            => '',
			'h4'            => '',
			'position'      => '',
			'el_width'      => '',
			'style'         => '',
			'txt_align'     => '',
			'accent_color'  => '',
			'link'          => '',
			'title'         => __( 'Text on the button', 'masterstudy' ),
			'color'         => '',
			'icon'          => '',
			'size'          => '',
			'btn_style'     => '',
			'el_class'      => '',
			'css_animation' => '',
		),
		$atts
	)
);

$class = 'vc_call_to_action wpb_content_element';
$link  = '||' !== $link ? $link : '';

$class .= ( ! empty( $position ) ) ? ' vc_cta_btn_pos_' . $position : '';
$class .= ( ! empty( $el_width ) ) ? ' vc_el_width_' . $el_width : '';
$class .= ( ! empty( $color ) ) ? ' vc_cta_' . $color : '';
$class .= ( ! empty( $style ) ) ? ' vc_cta_' . $style : '';
$class .= ( ! empty( $txt_align ) ) ? ' vc_txt_align_' . $txt_align : '';

$inline_css = ( ! empty( $accent_color ) ) ? ' style="' . vc_get_css_color( 'background-color', $accent_color ) . vc_get_css_color( 'border-color', $accent_color ) . '"' : '';

$class     .= $this->getExtraClass( $el_class );
$css_class  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );
$css_class .= $this->getCSSAnimation( $css_animation );
?>
	<div<?php echo wp_kses_post( stm_echo_safe_output( $inline_css ) ); ?> class="<?php echo esc_attr( $css_class ); ?> clearfix">
		<?php
		if ( ! empty( $link ) && 'bottom' !== $position ) {
			echo do_shortcode( '[vc_button2 link="' . $link . '" title="' . $title . '" color="' . $color . '" icon="' . $icon . '" size="' . $size . '" style="' . $btn_style . '" el_class="vc_cta_btn"]' );
		}
		?>
		<?php if ( ! empty( $h2 ) || ! empty( $h4 ) ) : ?>
			<hgroup>
				<?php if ( ! empty( $h2 ) ) : ?>
					<h2 class="wpb_heading">
						<?php echo esc_html( $h2 ); ?>
					</h2>
				<?php endif; ?>
				<?php if ( ! empty( $h4 ) ) : ?>
					<h4 class="wpb_heading">
						<?php echo esc_html( $h4 ); ?>
					</h4>
				<?php endif; ?>
			</hgroup>
			<?php
		endif;

		echo wpb_js_remove_wpautop( $content, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $link ) && 'bottom' === $position ) {
			echo do_shortcode( '[vc_button2 link="' . $link . '" title="' . $title . '" color="' . $color . '" icon="' . $icon . '" size="' . $size . '" style="' . $btn_style . '" el_class="vc_cta_btn"]' );
		}
		?>
	</div>
