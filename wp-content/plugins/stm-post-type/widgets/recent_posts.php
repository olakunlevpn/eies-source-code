<?php

class Stm_Recent_Posts extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'stm_recent_posts', // Base ID
			__('STM Recent posts', 'stm-post-type'), // Name
			array( 'description' => __( 'Theme recent posts widget', 'stm-post-type' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = (!empty($instance['title'])) ? apply_filters( 'widget_title', $instance['title'] ) : esc_html__('News', 'stm-post-type');
		$output = (!empty($instance['output'])) ? apply_filters( 'widget_output', $instance['output'] ) : 2;

		$style = (!empty($instance['style'])) ? $instance['style'] : 'style_1';

		echo stm_post_type_filtered_output($args['before_widget']);
		if ( ! empty( $title ) ) {
			echo stm_post_type_filtered_output($args['before_title']) . esc_html( $title ) . $args['after_title'];
		}

		$wp_query_args = array(
			'post_type' => 'post',
			'posts_per_page' => $output,
            'ignore_sticky_posts' => true
		);

		$query = new WP_Query($wp_query_args);

		if($query->have_posts()): ?>
			<?php while($query->have_posts()): $query->the_post(); ?>
                <div class="widget_media clearfix widget_media_<?php echo esc_attr($style); ?>">
					<?php get_template_part('partials/widgets/recent_posts/' . $style); ?>
                </div>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif;

		$args['after_widget'] = (!empty($args['after_widget'])) ? $args['after_widget'] : '</div>';
		echo stm_post_type_filtered_output($args['after_widget']);
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$title = '';
		$output = '';

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Recent posts', 'stm-post-type' );
		}

		if ( isset( $instance[ 'output' ] ) ) {
			$output = $instance[ 'output' ];
		}else {
			$output = __( '3', 'stm-post-type' );
		}

		$instance['style'] = (!empty($instance['style'])) ? $instance['style'] : 'style_1';

		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'stm-post-type' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo stm_post_type_filtered_output($this->get_field_id('style')); ?>"><?php _e( 'Style:', 'stm-post-type' ); ?></label>
            <select name="<?php echo stm_post_type_filtered_output($this->get_field_name('style')); ?>" id="<?php echo stm_post_type_filtered_output($this->get_field_id('style')); ?>" class="widefat">
                <option value="style_1"<?php selected( $instance['style'], 'style_1' ); ?>><?php _e('Style 1', 'stm-post-type'); ?></option>
                <option value="style_2"<?php selected( $instance['style'], 'style_2' ); ?>><?php _e('Style 2', 'stm-post-type'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'output' ) ); ?>"><?php _e( 'Output number:', 'stm-post-type' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'output' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'output' ) ); ?>" type="number" value="<?php echo esc_attr( $output ); ?>">
        </p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? esc_attr( $new_instance['title'] ) : '';
		$instance['output'] = ( ! empty( $new_instance['output'] ) ) ? esc_attr( $new_instance['output'] ) : '';

		if ( in_array( $new_instance['style'], array( 'style_1', 'style_2' ) ) ) {
			$instance['style'] = $new_instance['style'];
		} else {
			$instance['style'] = 'style_1';
		}

		return $instance;
	}

}

function register_stm_recent_posts_widget() {
	register_widget( 'Stm_Recent_Posts' );
}
add_action( 'widgets_init', 'register_stm_recent_posts_widget' );