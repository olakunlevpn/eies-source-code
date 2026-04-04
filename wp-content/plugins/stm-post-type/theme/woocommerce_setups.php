<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'stm_get_cat_icons' ) ) {
	// Pick icon to cat font awesome
	function stm_get_cat_icons( $font_pack = 'fa' ) {
		$fonts = array();
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( 'fa' === $font_pack ) {
			$font_icons = file( STM_POST_TYPE_PATH . '/theme/assets/font-awesome.json' );
			$font_icons = json_decode( implode( '', $font_icons ), true );
			foreach ( $font_icons as $key => $val ) {
				$fonts[] = $key;
			};
		} elseif ( 'new_fa' === $font_pack ) {
			// Pick icon to cat custom font pack 1
			$fonts_custom_type_1 = $wp_filesystem->get_contents( STM_POST_TYPE_PATH . '/theme/assets/new-fa.json' );
			$fonts_custom_type_1 = json_decode( $fonts_custom_type_1, true );
			foreach ( $fonts_custom_type_1 as $icon => $info ) {
				foreach ( $info['styles'] as $style ) {
					$style_f = 'fa';
					if ( 'brands' === $style ) {
						$style_f = 'fab';
					}
					if ( 'regular' === $style ) {
						$style_f = 'far';
					}

					$fonts[] = "{$style_f} fa-{$icon}";
				}
			}
		} elseif ( 'type_1' === $font_pack ) {
			// Pick icon to cat custom font pack 1
			$fonts_custom_type_1 = file( STM_POST_TYPE_PATH . '/theme/assets/custom-icon-font.json' );
			$fonts_custom_type_1 = json_decode( implode( '', $fonts_custom_type_1 ), true );
			foreach ( $fonts_custom_type_1 as $font_custom_type_1 ) {
				$fonts[] = str_replace( ' ', '', $font_custom_type_1 );
			}
		}

		return $fonts;
	}
}

//This code will add standart WPcolorpicker to woocommerce categories
// Add term page
function stm_theme_taxonomy_add_new_meta_field() {
	$counter                 = 0;
	$fa_icons                = stm_get_cat_icons( 'new_fa' );
	$stm_custom_icons_type_1 = stm_get_cat_icons( 'type_1' );
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[custom_term_meta]"><?php esc_html_e( 'Category Background Color', 'stm-post-type' ); ?></label>
		<input type="text" class="stm_theme_admin_cat_colorpicker" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
		<p class="description"><?php esc_html_e( 'Enter a value for this field', 'stm-post-type' ); ?></p>
		<label for="term_meta[custom_term_font]"><?php esc_html_e( 'Category Background font', 'stm-post-type' ); ?></label>
		<input type="hidden" class="stm_theme_admin_cat_icon" name="term_meta[custom_term_font]" id="term_meta[custom_term_font]" value="">
		<div class="stm_theme_cat_chosen_icon_preview"></div>
		<div class="stm_theme_font_pack_holder">
			<button type="button" class="stm_theme_choose_fa_icons button"><?php esc_html_e( 'Choose icons from Font Awesome Pack', 'stm-post-type' ); ?></button>
			<table class="form-table stm_theme_icon_font_table">
				<tr>
				<?php
				foreach ( $fa_icons as $fa_icon ) :
					$counter ++;
					?>
					<td>
						<i class="fa fa-<?php echo esc_attr( $fa_icon ); ?>" data-value="<?php echo esc_attr( $fa_icon ); ?>"></i>
					</td>
					<?php if ( 0 === $counter % 15 ) : ?>
				</tr>
				<tr>
					<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			</table>
		</div>
		<div class="stm_theme_font_pack_holder">
			<button type="button" class="stm_theme_choose_custom_icons button"><?php esc_html_e( 'Choose from Master Study Icons Pack', 'stm-post-type' ); ?></button>
			<table class="form-table stm_theme_icon_font_table">
				<tr>
				<?php
				$counter = 0;
				foreach ( $stm_custom_icons_type_1 as $stm_custom_icon_type_1 ) :
					$counter ++;
					?>
					<td>
						<i class="fa-<?php echo esc_attr( $stm_custom_icon_type_1 ); ?>" data-value="fa-<?php echo esc_attr( $stm_custom_icon_type_1 ); ?>"></i>
					</td>
					<?php if ( 0 === $counter % 15 ) : ?>
				</tr>
				<tr>
					<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			</table>
		</div>
		<p class="description"><?php esc_html_e( 'Click on button, then choose icon, which will be held for this category', 'stm-post-type' ); ?></p>
		<p class="description"><?php esc_html_e( 'If none of this icons suits you, upload thumbnail (130*120)', 'stm-post-type' ); ?></p>
	</div>
	<script type="text/javascript">
		jQuery(function ($) {
			$(function () {
				$(".stm_theme_admin_cat_colorpicker").wpColorPicker();

				$('.stm_theme_font_pack_holder .button').click(function () {
					$(this).closest('.stm_theme_font_pack_holder').find('.stm_theme_icon_font_table').toggleClass('visible');
				});

				$('.stm_theme_icon_font_table i').click(function () {
					$('.stm_theme_icon_font_table i').closest('td').removeClass('cat_icon_chosen');
					$(this).closest('td').addClass('cat_icon_chosen');
					var chosen_icon_cat_stm_theme = $(this).attr('data-value');
					$('.stm_theme_admin_cat_icon').val(chosen_icon_cat_stm_theme);
					var cat_chosen_icon_preview = $(this).closest('td').html();
					$('.stm_theme_cat_chosen_icon_preview').html(cat_chosen_icon_preview);
				});

				var stm_theme_cat_current_icon = $('.stm_theme_admin_cat_icon').val();
				if (stm_theme_cat_current_icon != '') {
					$('.stm_theme_icon_font_table i[data-value=' + stm_theme_cat_current_icon + ']').closest('td').addClass('cat_icon_chosen');
				}
			});
		});
	</script>
	<?php
}
add_action( 'product_cat_add_form_fields', 'stm_theme_taxonomy_add_new_meta_field', 10, 2 );

// Edit term page
function stm_theme_taxonomy_edit_meta_field( $term ) {
	$counter                 = 0;
	$fa_icons                = stm_get_cat_icons( 'new_fa' );
	$stm_custom_icons_type_1 = stm_get_cat_icons( 'type_1' );
	// put the term ID into a variable
	$t_id = $term->term_id;

	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label
					for="term_meta[custom_term_meta]"><?php esc_html_e( 'Category Background Color', 'stm-post-type' ); ?></label>
		</th>
		<td>
			<input type="text" class="stm_theme_admin_cat_colorpicker" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]"
				value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">
			<p class="description"><?php esc_html_e( 'Enter a value for this field', 'stm-post-type' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="term_meta[custom_term_meta]"><?php esc_html_e( 'Category Background Color', 'stm-post-type' ); ?></label>
		</th>
		<td>
			<input type="hidden" class="stm_theme_admin_cat_icon" name="term_meta[custom_term_font]" id="term_meta[custom_term_font]"
				value="<?php echo esc_attr( $term_meta['custom_term_font'] ) ? esc_attr( $term_meta['custom_term_font'] ) : ''; ?>">
			<div class="stm_theme_cat_chosen_icon_preview"></div>
			<div class="stm_theme_font_pack_holder">
				<button type="button"
						class="stm_theme_choose_fa_icons button"><?php esc_html_e( 'Choose icons from Font Awesome Pack', 'stm-post-type' ); ?></button>
				<table class="form-table stm_theme_icon_font_table">
					<tr>
					<?php
					foreach ( $fa_icons as $fa_icon ) :
						$counter ++;
						?>
						<td>
							<i class="<?php echo esc_attr( $fa_icon ); ?>" data-value="<?php echo esc_attr( $fa_icon ); ?>" data-search="<?php echo esc_attr( $fa_icon ); ?>"></i>
						</td>
						<?php if ( 0 === $counter % 15 ) : ?>
					</tr>
					<tr>
						<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				</table>
			</div>
			<div class="stm_theme_font_pack_holder">
				<button type="button"
						class="stm_theme_choose_custom_icons button"><?php esc_html_e( 'Choose from Master Study Icons Pack', 'stm-post-type' ); ?></button>
				<table class="form-table stm_theme_icon_font_table">
					<tr>
					<?php
					$counter = 0;
					foreach ( $stm_custom_icons_type_1 as $stm_custom_icon_type_1 ) :
						$counter ++;
						?>
						<td>
							<i class="fa-<?php echo esc_attr( $stm_custom_icon_type_1 ); ?>" data-value="fa-<?php echo esc_attr( $stm_custom_icon_type_1 ); ?>"></i>
						</td>
						<?php if ( 0 === $counter % 15 ) : ?>
					</tr>
					<tr>
						<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				</table>
			</div>
			<button type="button" class="button stm_theme_remove_cat_icon"><?php esc_html_e( 'Remove icon', 'stm-post-type' ); ?></button>
		</td>
	</tr>
	<script type="text/javascript">
		jQuery(function ($) {
			$(function () {
				$(".stm_theme_admin_cat_colorpicker").wpColorPicker();

				$('.stm_theme_font_pack_holder .button').click(function () {
					$(this).closest('.stm_theme_font_pack_holder').find('.stm_theme_icon_font_table').toggleClass('visible');
				});

				$('.stm_theme_icon_font_table i').click(function () {
					$('.stm_theme_icon_font_table i').closest('td').removeClass('cat_icon_chosen');
					$(this).closest('td').addClass('cat_icon_chosen');
					var chosen_icon_cat_stm_theme = $(this).attr('data-value');
					$('.stm_theme_admin_cat_icon').val(chosen_icon_cat_stm_theme);
					var cat_chosen_icon_preview = $(this).closest('td').html();
					$('.stm_theme_cat_chosen_icon_preview').html(cat_chosen_icon_preview);
				});

				var stm_theme_cat_current_icon = $('.stm_theme_admin_cat_icon').val();

				if (stm_theme_cat_current_icon != '') {
					$('.stm_theme_icon_font_table i[data-value="' + stm_theme_cat_current_icon + '"]').closest('td').addClass('cat_icon_chosen');
					var cat_chosen_icon_preview = $('.cat_icon_chosen').closest('td').html();
					$('.stm_theme_cat_chosen_icon_preview').html(cat_chosen_icon_preview);
				}

				$('.stm_theme_remove_cat_icon').click(function () {
					$('.stm_theme_admin_cat_icon').val("");
					$('.stm_theme_cat_chosen_icon_preview').empty();
				})
			});

		});
	</script>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'stm_theme_taxonomy_edit_meta_field', 10, 2 );

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id      = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys  = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset( $_POST['term_meta'][ $key ] ) ) {
				$term_meta[ $key ] = $_POST['term_meta'][ $key ];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}
add_action( 'edited_category', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_category', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );

// Change out of stock text
function custom_get_availability( $availability, $_product ) {
	if ( ! $_product->is_in_stock() ) {
		$availability['availability'] = __( 'No available seats', 'stm-post-type' );
	}

	return $availability;
}
add_filter( 'woocommerce_get_availability', 'custom_get_availability', 1, 2 );

function woo_custom_cart_button_text( $text ) {
	$enable_shop = get_option( 'stm_option', array() );
	if ( ! empty( $enable_shop['enable_shop'] ) ) {
		$enable_shop = $enable_shop['enable_shop'];
	}

	if ( ! $enable_shop ) {
		return __( 'Enroll this Course', 'stm-post-type' );
	}

	return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );

// Display 9 products per page. Goes in functions.php
function stm_loop_shop_per_page( $cols ) {
	return 9;
}
add_filter( 'loop_shop_per_page', 'stm_loop_shop_per_page', 20 );
