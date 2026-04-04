<?php

class CEW_Patch_Widget_Converter {

	public static function converter( $data ) {

		if ( ! empty( $data['widgetType'] ) ) {
			$widget = $data['widgetType'];

			$widget = str_replace( '-', '_', $widget );

			if ( method_exists( 'CEW_Patch_Widget_Converter', $widget ) ) {
				self::$widget( $data );
			}
		}

		return $data;
	}

	public static function vc_column_text( &$data ) {
		$data['widgetType'] = 'text-editor';
		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}
		if ( ! empty( $data['settings']['content'] ) ) {
			$data['settings']['editor'] = $data['settings']['content'];
		}

	}

	public static function vc_gmaps( &$data ) {
		$data['widgetType'] = 'google_maps';
		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}
		$data['settings']['address'] = 'London Eye, London, United Kingdom';
	}

	public static function vc_separator( &$data ) {
		$data['widgetType'] = 'divider';
		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}
		if ( ! empty( $data['settings']['accent_color'] ) ) {
			$data['settings']['color'] = $data['settings']['accent_color'];
		} else {
			$data['settings']['color'] = '#dddddd';
		}

	}

	public static function rev_slider_vc( &$data ) {

		$data['widgetType'] = 'slider_revolution';

		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['alias'] ) ) {

			global $wpdb;
			$table = $wpdb->prefix . 'revslider_sliders';

			$request = "SELECT title FROM {$table}
			WHERE alias = " . esc_sql( $data['settings']['alias'] );

			$r = $wpdb->get_results( $request ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( ! empty( $r ) && ! empty( $r[0] ) ) {
				$title = $r[0]->title;

				$data['settings']['revslidertitle'] = $title;
				$data['settings']['shortcode']      = "[rev_slider alias=\"{$data['settings']['alias']}\"]";

			}
		}

		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}

	}

	public static function rev_slider( &$data ) {

		$data['widgetType'] = 'wp-widget-rev-slider-widget';

		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['alias'] ) ) {

			global $wpdb;
			$table = $wpdb->prefix . 'revslider_sliders';

			$request = "SELECT id FROM {$table}
			WHERE alias = " . esc_sql( $data['settings']['alias'] );

			$r = $wpdb->get_results( $request ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( ! empty( $r ) && ! empty( $r[0] ) ) {
				$alias = $r[0]->id;

				$data['settings']['wp'] = array(
					'rev_slider' => $alias,
				);
			}
		}

		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}
	}

	public static function stm_pricing_plan( &$data ) {
		if ( ! empty( $data['settings'] ) ) {
			$data['settings']['link_title'] = __( 'Get now', 'masterstudy-elementor-widgets' );
		}
	}

	public static function stm_image_carousel( &$data ) {
		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['images'] ) ) {
			$data['settings']['gallery'] = $data['settings']['images'];
		}
	}

	public static function stm_news( &$data ) {
		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['loop'] ) ) {
			$params = explode( '|', $data['settings']['loop'] );
			foreach ( $params as $param ) {
				if ( false !== strpos( $param, 'size:' ) ) {
					$size = str_replace( 'size:', '', $param );
					if ( ! empty( $size ) ) {
						$data['settings']['qb_query_builder_posts_per_page'] = $size;
					}
				}
				if ( false !== strpos( $param, 'post_type:' ) ) {
					$post_type = str_replace( 'post_type:', '', $param );
					if ( ! empty( $post_type ) ) {
						$data['settings']['qb_query_builder_post_type'] = $post_type;
					}
				}
			}
		}
	}

	public static function vc_custom_heading( &$data ) {
		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['icon'] ) ) {
			$data['settings']['icon'] = array(
				'value' => $data['settings']['icon'],
			);
		}

		if ( ! empty( $data['settings'] ) && empty( $data['settings']['stm_title_font_weight'] ) ) {
			$data['settings']['stm_title_font_weight'] = '400';
		}
	}

	public static function contact_form_7( &$data ) {
		$data['widgetType'] = 'stm_contact_form_7';
		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['id'] ) ) {
			$data['settings']['form_id'] = $data['settings']['id'];
			unset( $data['settings']['id'] );
		}
	}

	public static function vc_single_image( &$data ) {

		$data['widgetType'] = 'image';

		if ( ! empty( $data['alignment'] ) ) {
			$data['align'] = $data['alignment'];
		}

		if ( ! empty( $data['settings']['onclick'] ) && 'link_image' === $data['settings']['onclick'] ) {
			$data['settings']['link_to']       = 'file';
			$data['settings']['open_lightbox'] = 'yes';
		}
	}

	public static function vc_tta_accordion( &$data ) {
		$data['widgetType'] = 'accordion';
		if ( ! empty( $data['settings']['c_position'] ) && 'right' === $data['settings']['c_position'] ) {
			$data['settings']['icon_align'] = 'right';
		}
	}

	public static function vc_video( &$data ) {
		$data['widgetType'] = 'video';
	}

	public static function vc_progress_bar( &$data ) {
		$data['widgetType'] = 'progress';
		if ( ! empty( $data['settings'] ) && ! empty( $data['settings']['values'] ) ) {
			$data['settings']['data_values'] = CEW_Patch_Widget_Settings_Parser::vc_param_group_parse_atts( $data['settings']['values'] );
		}
	}

	public static function vc_wp_custommenu( &$data ) {
		$data['widgetType'] = 'wp-widget-nav_menu';

		if ( ! empty( $data['settings'] ) ) {
			if ( ! empty( $data['settings']['nav_menu'] ) ) {
				$data['settings']['wp'] = array(
					'nav_menu' => $data['settings']['nav_menu'],
					'title'    => '',
				);
			}

			if ( ! empty( $data['settings']['el_class'] ) ) {
				$data['settings']['_css_classes'] = $data['settings']['el_class'];
			}
		}

	}

	public static function vc_wp_text( &$data ) {
		$data['widgetType'] = 'wp-widget-text';

		if ( ! empty( $data['settings'] ) ) {
			if ( ! empty( $data['settings']['content'] ) ) {
				$data['settings']['wp'] = array(
					'text'   => $data['settings']['content'],
					'title'  => '',
					'filter' => 'on',
					'visual' => 'on',
				);
			}

			if ( ! empty( $data['settings']['el_class'] ) ) {
				$data['settings']['_css_classes'] = $data['settings']['el_class'];
			}
		}

	}

	public static function vc_wp_search( &$data ) {
		$data['widgetType'] = 'wp-widget-search';
	}

	public static function vc_wp_categories( &$data ) {
		$data['widgetType'] = 'wp-widget-categories';
	}

	public static function vc_wp_archives( &$data ) {
		$data['widgetType'] = 'wp-widget-archives';
	}

	public static function vc_wp_tagcloud( &$data ) {
		$data['widgetType'] = 'wp-widget-tag_cloud';
	}

	public static function vc_wp_pages( &$data ) {
		$data['widgetType'] = 'wp-widget-pages';
	}

	public static function vc_wp_posts( &$data ) {
		$data['widgetType'] = 'wp-widget-recent-posts';
	}

	public static function vc_wp_meta( &$data ) {
		$data['widgetType'] = 'wp-widget-meta';
	}

	public static function vc_wp_recentcomments( &$data ) {
		$data['widgetType'] = 'wp-widget-recent-comments';
	}

	public static function vc_wp_calendar( &$data ) {
		$data['widgetType'] = 'wp-widget-calendar';
	}

	public static function vc_btn( &$data ) {
		/*TODO Masterstudy only*/

		$data['widgetType'] = 'button';

		if ( ! empty( $data['settings'] ) ) {
			if ( ! empty( $data['settings']['title'] ) ) {
				$data['settings']['text'] = $data['settings']['title'];
			}
			if ( ! empty( $data['settings']['i_align'] ) ) {
				$data['settings']['icon_align'] = $data['settings']['i_align'];
			}

			/*ICON*/
			if ( ! empty( $data['settings']['add_icon'] ) && 'true' === $data['settings']['add_icon'] ) {

				$icon_type = ( ! empty( $data['settings']['i_type'] ) ) ? $data['settings']['i_type'] : 'fontawesome';
				if ( ! empty( $data['settings'][ "i_icon_{$icon_type}" ] ) ) {
					$data['settings']['selected_icon'] = $data['settings'][ "i_icon_{$icon_type}" ];
				}
			}

			if ( empty( $data['settings']['align'] ) ) {
				$data['settings']['_element_width'] = 'auto';
			}

			if ( ! empty( $data['settings']['color'] ) && 'link' === $data['settings']['color'] ) {
				$data['settings']['color_link'] = 'yes';
			}

			if ( ! empty( $data['settings']['button_block'] ) && 'true' === $data['settings']['button_block'] ) {
				$data['settings']['button_block'] = 'yes';
			}
		}

		if ( function_exists( 'masterstudy_get_actual_colors' ) ) {
			$colors = masterstudy_get_actual_colors();

			$base_color      = $colors['base_color'];
			$secondary_color = $colors['secondary_color'];
			$third_color     = $colors['third_color'];

			/**
			 * button_text_color
			 * hover_color
			 * background_color
			 * button_background_hover_color
			 * vc_border_color
			 * vc_border_color_hover
			 * vc_icon_color
			 * vc_icon_color_hover
			 */

			if ( ! empty( $data['settings']['style'] ) && ! empty( $data['settings']['color'] ) ) {
				$style = $data['settings']['style'];
				$color = $data['settings']['color'];

				if ( 'flat' === $style && 'theme_style_1' === $color ) {
					$data['settings']['button_text_color'] = '#ffffff';
					$data['settings']['background_color']  = $data['settings']['vc_icon_color_hover'] = $data['settings']['hover_color'] = $base_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['vc_icon_color']     = $data['settings']['button_background_hover_color'] = $third_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				}

				if ( 'flat' === $style && 'theme_style_3' === $color ) {
					$data['settings']['button_text_color'] = $data['settings']['button_background_hover_color'] = $base_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['hover_color']       = '#ffffff';
					$data['settings']['background_color']  = $data['settings']['vc_icon_color_hover'] = $third_color;//phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				}

				if ( 'flat' === $style && 'white' === $color ) {
					$data['settings']['button_text_color']   = $data['settings']['button_background_hover_color'] = $base_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['background_color']    = $data['settings']['hover_color'] = '#ffffff'; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['vc_icon_color_hover'] = $third_color;
				}

				if ( 'outline' === $style && 'theme_style_2' === $color ) {
					$data['settings']['background_color']      = $data['settings']['button_background_hover_color'] = 'rgba(255,255,255,0)'; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['vc_border_color']       = $data['settings']['button_text_color'] = $base_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['vc_border_color_hover'] = $data['settings']['hover_color'] = $secondary_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				}

				if ( 'outline' === $style && 'theme_style_4' === $color ) {
					$data['settings']['button_text_color'] = '#ffffff';
					$data['settings']['background_color']  = 'rgba(255,255,255,0)';
					$data['settings']['vc_border_color']   = $data['settings']['button_background_hover_color'] = $third_color; //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					$data['settings']['hover_color']       = $base_color;
				}
			}
		}
	}

	public static function vc_gallery( &$data ) {

		$data['widgetType'] = 'image-gallery';

		if ( ! empty( $data['settings'] ) ) {
			if ( ! empty( $data['settings']['images'] ) ) {
				$data['settings']['wp_gallery'] = $data['settings']['images'];
				unset( $data['settings']['images'] );
			}
			if ( ! empty( $data['settings']['type'] ) && 'image_full' === $data['settings']['type'] ) {
				$data['settings']['thumbnail_size']  = 'full';
				$data['settings']['gallery_columns'] = 1;
			}
		}

	}

	public static function vc_empty_space( &$data ) {
		$data['widgetType'] = 'stm_empty_space';
	}

	public static function vc_row( &$data ) {
		$data['widgetType'] = 'section';
		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}
		$data['settings']['full_width']      = 'boxed';
		$data['settings']['stretch_section'] = 'section-stretched';
	}

	public static function vc_section( &$data ) {
		$data['widgetType'] = 'section';
		if ( empty( $data['settings'] ) ) {
			$data['settings'] = array();
		}
		$data['settings']['full_width']      = 'boxed';
		$data['settings']['stretch_section'] = 'section-stretched';
	}

	public static function stm_course_lessons( &$data ) {
		$data['widgetType'] = 'stm_course_lessons';
	}

}
