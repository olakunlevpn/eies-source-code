<?php

class STM_Widget_Menus extends WP_Widget
{

	public function __construct()
	{
		$widget_ops = array('classname' => 'widget_menus', 'description' => __('A list of your site&#8217;s Menus.', 'stm-post-type'));
		parent::__construct('stm_menus', __('STM Menus', 'stm-post-type'), $widget_ops);
	}

	public function widget($args, $instance)
	{

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Menu', 'stm-post-type') : $instance['title'], $instance, $this->id_base);
		$menu = (!empty($instance['menu'])) ? $instance['menu'] : '';

		echo stm_post_type_filtered_output($args['before_widget']);
		if ($title) {
			echo stm_post_type_filtered_output($args['before_title']) . $title . $args['after_title'];
		}

		wp_nav_menu(
			array(
				'menu' => $menu
			)
		);

		echo stm_post_type_filtered_output($args['after_widget']);
	}

	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['menu'] = (!empty($new_instance['menu'])) ? $new_instance['menu'] : '';

		return $instance;
	}

	public function form($instance)
	{
		//Defaults
		$instance = wp_parse_args((array)$instance, array('menu' => '', 'title' => ''));
		$title = esc_attr($instance['title']);
		$menus = get_terms('nav_menu', array('hide_empty' => true));
		?>
        <p><label for="<?php echo stm_post_type_filtered_output($this->get_field_id('title')); ?>"><?php _e('Title:', 'stm-post-type'); ?></label>
            <input class="widefat"
                   id="<?php echo stm_post_type_filtered_output($this->get_field_id('title')); ?>"
                   name="<?php echo stm_post_type_filtered_output($this->get_field_name('title')); ?>"
                   type="text" value="<?php echo stm_post_type_filtered_output($title); ?>"/>
        </p>

        <p>
            <label for="<?php echo stm_post_type_filtered_output($this->get_field_id('Menu')); ?>"><?php _e('Menu:', 'stm-post-type'); ?></label>

            <select name="<?php echo stm_post_type_filtered_output($this->get_field_name('menu')); ?>" id="<?php echo stm_post_type_filtered_output($this->get_field_id('menu')); ?>"
                    class="widefat">
				<?php foreach ($menus as $menu): ?>
                    <option value="<?php echo intval($menu->term_id); ?>"<?php selected($instance['menu'], $menu->term_id); ?>>
						<?php echo sanitize_text_field($menu->name); ?>
                    </option>
				<?php endforeach; ?>
            </select>
        </p>

		<?php
	}

}

function register_menus_widget()
{
	register_widget('STM_Widget_Menus');
}

add_action('widgets_init', 'register_menus_widget');