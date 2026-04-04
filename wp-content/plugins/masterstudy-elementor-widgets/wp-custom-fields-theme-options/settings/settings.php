<?php

class STM_LMS_Settings
{

    public $option_name;
    public $page_args;
    public $fields;

    function __construct($option_name, $page_args, $fields)
    {

        $this->option_name = $option_name;
        $this->page_args = $page_args;
        $this->fields = $fields;

        add_action('admin_menu', array($this, 'stm_lms_settings_page'), 1000);
        add_action('wp_ajax_stm_save_settings', array($this, 'stm_save_settings'));
    }

    function stm_lms_settings_page()
    {

        if (current_user_can('manage_options')) {

            if (!empty($this->page_args['parent_slug'])) {
                $r = add_submenu_page(
                    $this->page_args['parent_slug'],
                    $this->page_args['page_title'],
                    $this->page_args['menu_title'],
                    'manage_options',
                    $this->page_args['menu_slug'],
                    array($this, 'stm_lms_settings_page_view')
                );
            } else {
                add_menu_page(
                    $this->page_args['page_title'],
                    $this->page_args['menu_title'],
                    'manage_options',
                    $this->page_args['menu_slug'],
                    array($this, 'stm_lms_settings_page_view'),
                    $this->page_args['icon'],
                    $this->page_args['position']
                );
            }

            do_action("wpcfto_screen_{$this->option_name}_added");
        }
    }

    public static function stm_get_post_type_array($post_type, $args = array())
    {
        $r = array(
            '' => esc_html__('Choose Page', 'wp-custom-fields-theme-options'),
        );

        $default_args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $q = get_posts(wp_parse_args($args, $default_args));

        if (!empty($q)) {
            foreach ($q as $post_data) {
                $r[$post_data->ID] = $post_data->post_title;
            }
        }

        wp_reset_query();

        return $r;
    }

    function stm_lms_settings()
    {

        $args = array();
        $args[$this->option_name] = $this->fields;

        return apply_filters($this->option_name, array(
            'id' => $this->option_name,
            'args' => $args
        ));
    }

    function stm_lms_get_settings()
    {
        return get_option($this->option_name, array());
    }

    function stm_lms_settings_page_view()
    {
        $metabox = $this->stm_lms_settings();
        $settings = $this->stm_lms_get_settings();
        $page = $this->page_args;

        foreach ($metabox['args'][$this->option_name] as $section_name => $section) {
            foreach ($section['fields'] as $field_name => $field) {
                $default_value = (!empty($field['value'])) ? $field['value'] : '';
                $metabox['args'][$this->option_name][$section_name]['fields'][$field_name]['value'] = (isset($settings[$field_name])) ? $settings[$field_name] : $default_value;
            }
        }
        include STM_WPCFTO_PATH . '/settings/view/main.php';
    }

    public static function get_my_settings()
    {

    }

    function stm_save_settings()
    {

        check_ajax_referer('stm_save_settings', 'nonce');

        if (!current_user_can('manage_options')) die;

        if (empty($_REQUEST['name'])) die;
        $id = sanitize_text_field($_REQUEST['name']);
        $settings = array();
        $request_body = file_get_contents('php://input');
        if (!empty($request_body)) {
            $request_body = json_decode($request_body, true);
            foreach ($request_body as $section_name => $section) {
                foreach ($section['fields'] as $field_name => $field) {
                    $settings[$field_name] = $field['value'];
                }
            }
        }

        wp_send_json(update_option($id, $settings));
    }
}

add_action('init', function () {
    $theme_options_page = apply_filters('wpcfto_options_page_setup', array());

    if (!empty($theme_options_page)) {
        foreach ($theme_options_page as $setup) {
            if (empty($setup['option_name']) or empty($setup['page']) or !isset($setup['fields'])) continue;

            new STM_LMS_Settings($setup['option_name'], $setup['page'], $setup['fields']);
        }
    }
});