<?php
/**
 * @var $button_text
 * @var $link
 * @var $link_tooltip
 * @var $btn_align
 * @var $btn_size
 * @var $button_color
 * @var $button_text_color_hover
 * @var $button_bg_color
 * @var $button_bg_color_hover
 * @var $button_border_color
 * @var $button_border_color_hover
 * @var $icon
 * @var $icon_size
 */

$btn_rand_style = 'btn_number_' . wp_rand( 0, 99999 );

$btn_styles  = 'color:' . $button_color . '; background-color:' . $button_bg_color . '; border-color:' . $button_border_color . ';';
$icon_styles = 'color:' . $button_border_color . '; border-color:' . $button_border_color . '; font-size:' . $icon_size . 'px;';

if ( empty( $icon ) ) {
	$btn_classes = 'btn btn-default btn-no-icon ' . $btn_size;
} else {
	$btn_classes = 'icon-btn';
	$btn_styles  = 'color:' . $button_color . '; border-color:' . $button_border_color . ';';
}

$library = $library ?? '';
?>

<?php if( !empty($link['url']) and !empty($link['title']) ): //phpcs:ignore ?>
	<div class="heading_font text-<?php echo esc_attr($btn_align.' '.$css_class.' '.$btn_rand_style); //phpcs:ignore ?>">
		<a href="<?php echo esc_attr($link['url']); ?>" class="<?php echo esc_attr($btn_classes); ?>" title="<?php echo esc_attr($link_tooltip); ?>" style="<?php echo esc_attr($btn_styles); ?>" <?php if(!empty($link['target']) ) echo 'target="' . $link['target'] . '"' ; //phpcs:ignore?>>
			<?php if ( ! empty( $icon ) ) : ?>
				<?php if ( 'svg' === $library ) : ?>
					<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<i class="fa <?php echo esc_attr( $icon ); ?> icon_in_btn" style="<?php echo esc_attr( $icon_styles ); ?>"></i>
				<?php endif; ?>
			<?php endif; ?>
			<span class="link-title" style="background-color:<?php echo esc_attr( $button_bg_color ); ?>;"><?php echo esc_attr( $link['title'] ); ?></span>
		</a>
	</div>
<?php endif; ?>

<style>
	<?php if ( ! empty( $library ) && 'svg' === $library ) : ?>
		.<?php echo esc_attr( $btn_rand_style ); ?> .icon-btn svg {
			position: absolute;
			color: <?php echo esc_attr( $button_border_color ); ?>; 
			border-color: <?php echo esc_attr( $button_border_color ); ?>; 
			width: <?php echo esc_attr( $icon_size ) . 'px'; ?>; 
			height: <?php echo esc_attr( $icon_size ) . 'px'; ?>; 
			top: calc( 50% - <?php echo esc_attr( ( $icon_size ) / 2 ) . 'px'; ?> ); 
			left: <?php echo esc_attr( 23 - ( $icon_size / 2 ) ) . 'px'; ?>;	
		}
	<?php endif; ?>

	<?php if ( ! empty( $button_bg_color_hover ) && empty( $icon ) ) : ?>
		.<?php echo esc_attr( $btn_rand_style ); ?> .btn.btn-default:after {
			background-color: <?php echo esc_attr( $button_bg_color_hover ); ?>;
		}
	<?php endif; ?>

	<?php if ( ! empty( $button_text_color_hover ) && empty( $icon ) ) : ?>
		.<?php echo esc_attr( $btn_rand_style ); ?> .btn.btn-default:hover {
			color: <?php echo esc_attr( $button_text_color_hover ); ?> !important;
		}
	<?php endif; ?>

	<?php if ( ! empty( $button_text_color_hover ) ) : ?>
		.<?php echo esc_attr( $btn_rand_style ); ?> .icon-btn:hover .link-title{
			color: <?php echo esc_attr( $button_text_color_hover ); ?> !important;
		}
	<?php endif; ?>

	<?php if ( ! empty( $button_bg_color_hover ) ) : ?>
		.<?php echo esc_attr( $btn_rand_style ); ?> .icon-btn:hover .link-title {
			background-color: <?php echo esc_attr( $button_bg_color_hover ); ?> !important;
		}
	<?php endif; ?>

	<?php if ( ! empty( $button_border_color_hover ) ) : ?>
		.<?php echo esc_attr( $btn_rand_style ); ?> .icon-btn:hover{
			border-color: <?php echo esc_attr( $button_border_color_hover ); ?> !important;
		}
		.<?php echo esc_attr( $btn_rand_style ); ?> .icon-btn:hover .icon_in_btn{
			color: <?php echo esc_attr( $button_border_color_hover ); ?> !important;
			border-color: <?php echo esc_attr( $button_border_color_hover ); ?> !important;
		}
	<?php endif; ?>

</style>
