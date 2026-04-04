<?php

class Stm_Contacts_Widget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    function __construct()
    {
        parent::__construct(
            'contacts', // Base ID
            __('Contacts', 'stm-post-type'), // Name
            array('description' => __('Contacts widget', 'stm-post-type'),) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        $title = (!empty($instance['title'])) ? apply_filters('widget_title', $instance['title']) : esc_html__('Contacts', 'stm-post-type');

        $style = (!empty($instance['style'])) ? $instance['style'] : 'style_1';

        if (!function_exists('stm_module_styles')) return '';

        stm_module_styles('contacts_widget', $style);

        echo stm_post_type_filtered_output($args['before_widget']);
        if (!empty($title)) {
            echo stm_post_type_filtered_output($args['before_title']) . esc_html($title) . $args['after_title'];
        }
        echo '<ul class="widget_contacts_' . $style . '">';
        if (!empty($instance['address'])) {
            echo '<li class="widget_contacts_address"><div class="icon"><i class="fa-icon-stm_icon_pin"></i></div><div class="text">' . html_entity_decode($instance['address']) . '</div></li>';
        }

        if (!empty($instance['phone'])) {
            echo '<li class="widget_contacts_phone"><div class="icon"><i class="fa-icon-stm_icon_phone"></i></div><div class="text">' . html_entity_decode($instance['phone']) . '</div></li>';
        }

        if (!empty($instance['fax'])) {
            echo '<li class="widget_contacts_fax"><div class="icon"><i class="fa-icon-stm_icon_fax"></i></div><div class="text">' . html_entity_decode($instance['fax']) . '</div></li>';
        }

        if (!empty($instance['email'])) {
            echo '<li class="widget_contacts_email"><div class="icon"><i class="fa fa-envelope"></i></div><div class="text"><a href="mailto:' . sanitize_email($instance['email']) . '">' . sanitize_email($instance['email']) . '</a></div></li>';
        }
        echo '</ul>';


        echo stm_post_type_filtered_output($args['after_widget']);
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {

        $title = '';
        $address = '';
        $phone = '';
        $fax = '';
        $email = '';

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Contact', 'stm-post-type');
        }

        if (isset($instance['address'])) {
            $address = $instance['address'];
        }

        if (isset($instance['phone'])) {
            $phone = $instance['phone'];
        }

        if (isset($instance['fax'])) {
            $fax = $instance['fax'];
        }

        if (isset($instance['email'])) {
            $email = $instance['email'];
        }

        $instance['style'] = (!empty($instance['style'])) ? $instance['style'] : 'style_1';

        ?>
        <p>
            <label for="<?php echo stm_post_type_filtered_output($this->get_field_id('style')); ?>"><?php _e('Style:', 'stm-post-type'); ?></label>
            <select name="<?php echo stm_post_type_filtered_output($this->get_field_name('style')); ?>"
                    id="<?php echo stm_post_type_filtered_output($this->get_field_id('style')); ?>" class="widefat">
                <option value="style_1"<?php selected($instance['style'], 'style_1'); ?>><?php _e('Style 1', 'stm-post-type'); ?></option>
                <option value="style_2"<?php selected($instance['style'], 'style_2'); ?>><?php _e('Style 2', 'stm-post-type'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'stm-post-type'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('address')); ?>"><?php _e('Address:', 'stm-post-type'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('address')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('address')); ?>" type="text"
                   value="<?php echo esc_attr($address); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('phone')); ?>"><?php _e('Phone:', 'stm-post-type'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('phone')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('phone')); ?>" type="text"
                   value="<?php echo esc_attr($phone); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('fax')); ?>"><?php _e('Fax:', 'stm-post-type'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('fax')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('fax')); ?>" type="text"
                   value="<?php echo esc_attr($fax); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('email')); ?>"><?php _e('E-mail:', 'stm-post-type'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('email')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('email')); ?>" type="text"
                   value="<?php echo sanitize_email($email); ?>">
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
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? esc_attr($new_instance['title']) : '';
        $instance['address'] = (!empty($new_instance['address'])) ? esc_attr($new_instance['address']) : '';
        $instance['phone'] = (!empty($new_instance['phone'])) ? esc_attr($new_instance['phone']) : '';
        $instance['fax'] = (!empty($new_instance['fax'])) ? esc_attr($new_instance['fax']) : '';
        $instance['email'] = (!empty($new_instance['email'])) ? sanitize_email($new_instance['email']) : '';

        if (in_array($new_instance['style'], array('style_1', 'style_2'))) {
            $instance['style'] = $new_instance['style'];
        } else {
            $instance['style'] = 'style_1';
        }

        return $instance;
    }

}

function register_contacts_widget()
{
    register_widget('Stm_Contacts_Widget');
}

add_action('widgets_init', 'register_contacts_widget');