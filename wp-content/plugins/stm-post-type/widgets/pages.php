<?php

class STM_Widget_Pages extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_pages', 'description' => __( 'A list of your site&#8217;s Pages.', 'stm-post-type') );
		parent::__construct('stm_pages', __('STM Pages', 'stm-post-type'), $widget_ops);
	}

	public function widget( $args, $instance ) {

		/**
		 * Filter the widget title.
		 *
		 * @since 2.6.0
		 *
		 * @param string $title    The widget title. Default 'Pages'.
		 * @param array  $instance An array of the widget's settings.
		 * @param mixed  $id_base  The widget ID.
		 */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Pages', 'stm-post-type' ) : $instance['title'], $instance, $this->id_base );

		$sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$style = empty( $instance['style'] ) ? 'style_1' : $instance['style'];
		$include = empty( $instance['include'] ) ? '' : $instance['include'];

		if ( $sortby == 'menu_order' )
			$sortby = 'menu_order, post_title';

		/**
		 * Filter the arguments for the Pages widget.
		 *
		 * @since 2.8.0
		 *
		 * @see wp_list_pages()
		 *
		 * @param array $args An array of arguments to retrieve the pages list.
		 */
		$out = wp_list_pages( apply_filters( 'stm_widget_pages_args', array(
			'title_li'    => '',
			'echo'        => 0,
			'link_before' => '<span class="h6">',
			'link_after' => '</span>',
			'sort_column' => $sortby,
			'include'     => $include
		) ) );

		if ( ! empty( $out ) ) {
			echo stm_post_type_filtered_output($args['before_widget']);
			if ( $title ) {
				echo stm_post_type_filtered_output($args['before_title']) . $title . $args['after_title'];
			}
			?>
			<ul class="<?php echo esc_attr($style); ?>">
				<?php echo stm_post_type_filtered_output($out); ?>
			</ul>
			<?php
			echo stm_post_type_filtered_output($args['after_widget']);
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}
		
		if ( in_array( $new_instance['style'], array( 'style_1', 'style_2' ) ) ) {
			$instance['style'] = $new_instance['style'];
		} else {
			$instance['style'] = 'style_1';
		}

		$instance['include'] = strip_tags( $new_instance['include'] );

		return $instance;
	}

	public function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'include' => '') );
		$title = esc_attr( $instance['title'] );
		$include = esc_attr( $instance['include'] );
		$instance['style'] = (!empty($instance['style'])) ? $instance['style'] : 'style_1';
		?>
		<p><label for="<?php echo stm_post_type_filtered_output($this->get_field_id('title')); ?>"><?php _e('Title:', 'stm-post-type'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo stm_post_type_filtered_output($this->get_field_id('style')); ?>"><?php _e( 'Style:', 'stm-post-type' ); ?></label>
			<select name="<?php echo stm_post_type_filtered_output($this->get_field_name('style')); ?>" id="<?php echo stm_post_type_filtered_output($this->get_field_id('style')); ?>" class="widefat">
				<option value="style_1"<?php selected( $instance['style'], 'style_1' ); ?>><?php _e('Style 1', 'stm-post-type'); ?></option>
				<option value="style_2"<?php selected( $instance['style'], 'style_2' ); ?>><?php _e('Style 2', 'stm-post-type'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo stm_post_type_filtered_output($this->get_field_id('sortby')); ?>"><?php _e( 'Sort by:', 'stm-post-type' ); ?></label>
			<select name="<?php echo stm_post_type_filtered_output($this->get_field_name('sortby')); ?>" id="<?php echo stm_post_type_filtered_output($this->get_field_id('sortby')); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title', 'stm-post-type'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order', 'stm-post-type'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID', 'stm-post-type' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo stm_post_type_filtered_output($this->get_field_id('include')); ?>"><?php _e( 'Include:', 'stm-post-type' ); ?></label> <input type="text" value="<?php echo $include; ?>" name="<?php echo $this->get_field_name('include'); ?>" id="<?php echo $this->get_field_id('include'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.', 'stm-post-type' ); ?></small>
		</p>
	<?php
	}

}

function register_pages_widget() {
	register_widget( 'STM_Widget_Pages' );
}
add_action( 'widgets_init', 'register_pages_widget' );