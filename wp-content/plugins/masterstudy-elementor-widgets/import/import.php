<?php

define('STM_ELEMENTOR_TEMPLATE_LIBRARY_PATH', dirname(__FILE__));
define('STM_ELEMENTOR_TEMPLATE_LIBRARY_URL', plugin_dir_url(__FILE__));

require_once STM_ELEMENTOR_TEMPLATE_LIBRARY_PATH . '/import_ajax.php';

if (!empty($_GET['post']) and !empty($_GET['action']) && $_GET['action'] === 'elementor') {
    new STM_Elementor_Template_Library();
}

class STM_Elementor_Template_Library
{

    public $post_id = 0;
    public $v = 1;

    public function __construct()
    {
        $this->post_id = intval($_GET['post']);
        $this->v = (WP_DEBUG) ? time() : $this->v;

        add_action('admin_print_footer_scripts', [$this, 'init']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'scripts']);
    }

    static function includeTemplate($path, $type = 'php')
    {
        include(STM_ELEMENTOR_TEMPLATE_LIBRARY_PATH . "/templates/{$path}.{$type}");
    }

    function init()
    {
        $this->includeTemplate('main');
    }

    function scripts()
    {

        wp_enqueue_script('vue.min.js', STM_ELEMENTOR_TEMPLATE_LIBRARY_URL . 'assets/vendors/vue.min.js', array(), $this->v, true);
        wp_enqueue_script('vue-resource.js', STM_ELEMENTOR_TEMPLATE_LIBRARY_URL . 'assets/vendors/vue-resource.js', array(), $this->v, true);
        wp_enqueue_script('stm-elementor-importer', STM_ELEMENTOR_TEMPLATE_LIBRARY_URL . 'assets/js/stm-elementor-importer.js', array('vue.min.js', 'vue-resource.js'), $this->v, true);

        wp_localize_script('stm-elementor-importer', 'stm_import_data', array(
            'url' => admin_url('admin-ajax.php'),
            'post_id' => $this->post_id,
            'nonces' => array(
                'clear_cache' => wp_create_nonce( 'elementor_clear_cache' )
            )
        ));

        wp_enqueue_style('stm-elementor-importer', STM_ELEMENTOR_TEMPLATE_LIBRARY_URL . 'assets/css/importer.css', null, $this->v);

    }

}