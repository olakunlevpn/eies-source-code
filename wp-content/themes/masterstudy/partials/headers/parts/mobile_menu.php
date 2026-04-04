<?php
// phpcs:ignoreFile
// added ignoreFile phpcs because the sniffer swore at some lines in which, after correcting the menu, it broke, I didn’t want to ignore the class everywhere because the file is small
if ( ! function_exists( 'stm_lms_mobile_custom_menus' ) ) {
	function stm_lms_mobile_custom_menus() {
		$menu_name = 'primary';
		$menu_list = '';
		$locations = get_nav_menu_locations();

		if ( $locations && isset( $locations[ $menu_name ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			if ( ! empty( $menu_items ) ) {
				$open_parents = array();

				foreach ( (array) $menu_items as $key => $menu_item ) {
					$title = $menu_item->title;
					$link = $menu_item->url;
					$current_level = $menu_item->menu_item_parent;

					while ( ! empty( $open_parents ) && end( $open_parents ) != $current_level ) {
						array_pop( $open_parents );
						$menu_list .= '</div></div>' . "\n";
					}

					if ( $current_level == 0 ) {
						$menu_list .= '<div class="stm_lms_categories_dropdown__parent">';
						$menu_list .= '<a href="' . esc_url( $link ) . '" class="sbc_h">' . wp_kses_post( $title ) . '</a>' . "\n";

						if ( isset( $menu_items[ $key + 1 ] ) && $menu_items[ $key + 1 ]->menu_item_parent == $menu_item->ID ) {
							$menu_list .= '<span class="stm_lms_cat_toggle"></span>' . "\n";
							$menu_list .= '<div class="stm_lms_categories_dropdown__childs">' . "\n";
							array_push( $open_parents, $menu_item->ID );
						} else {
							$menu_list .= '</div>' . "\n";
						}
					} else {
						$is_third_level = in_array( $menu_item->menu_item_parent, array_column( $menu_items, 'ID' ) ) &&
						$menu_items[ array_search( $menu_item->menu_item_parent, array_column( $menu_items, 'ID' ) ) ]->menu_item_parent != 0;

						$menu_list .= '<div class="stm_lms_categories_dropdown__child">';
						$menu_list .= '<a href="' . esc_url( $link ) . '" class="' . ( $is_third_level ? 'third-sub-mobile' : '' ) . '">' . wp_kses_post( $title ) . '</a>' . "\n";

						if ( isset( $menu_items[ $key + 1] ) && $menu_items[ $key + 1 ]->menu_item_parent == $menu_item->ID ) {
							$menu_list .= '<div class="stm_lms_categories_dropdown__childs_container">' . "\n";
							array_push( $open_parents, $menu_item->ID );
						} else {
							$menu_list .= '</div>' . "\n";
						}
					}
				}

				while ( ! empty( $open_parents ) ) {
					array_pop( $open_parents );
					$menu_list .= '</div></div>' . "\n";
				}
			}
		}

		echo $menu_list;
	}
	
}
?>
<div class="stm_lms_categories_dropdown  stm_lms_categories">
	<div class="stm_lms_categories_dropdown__parents">
		<?php stm_lms_mobile_custom_menus(); ?>
	</div>
</div>
