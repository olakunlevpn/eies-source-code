<?php
/**
 * Plugin Name: WordPress Custom Fields & Theme Options
 * Plugin URI:  https://github.com/StylemixThemes/wp-custom-fields-theme-options
 * Description: WordPress Custom Fields & Theme Options with Vue.js.
 * Version:     2.2.1
 * Author:      StylemixThemes
 * Author URI:  https://stylemixthemes.com
 *
 * @package    WordPress Custom Fields & Theme Options
 * @author     StylemixThemes
 * @copyright  Copyright (c) 2011-2020, StylemixThemes
 * @link       https://github.com/StylemixThemes/wp-custom-fields-theme-options
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

add_action('plugins_loaded', function () {

    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    $framework_versions = apply_filters('wpcfto_versions', array());

    $max_version = array_keys($framework_versions, max($framework_versions));

    if (!class_exists('Stylemix_WPCFTO') and $max_version[0] === __FILE__) {

        define('STM_WPCFTO_FILE', __FILE__);
        define('STM_WPCFTO_PATH', dirname(STM_WPCFTO_FILE));
        define('STM_WPCFTO_URL', plugin_dir_url(STM_WPCFTO_FILE));

        class Stylemix_WPCFTO
        {
            function __construct()
            {

                require_once STM_WPCFTO_PATH . '/metaboxes/metabox.php';
                require_once STM_WPCFTO_PATH . '/taxonomy_meta/metaboxes.php';
                require_once STM_WPCFTO_PATH . '/settings/settings.php';


                if (!is_textdomain_loaded('wp-custom-fields-theme-options')) {
                    $loaded = load_plugin_textdomain(
                        'wp-custom-fields-theme-options',
                        false,
                        dirname(plugin_basename(__FILE__)) . '/languages'
                    );

                }

            }
        }

        new Stylemix_WPCFTO();
    }
});


add_filter('wpcfto_versions', function ($versions) {

    $plugin_data = get_plugin_data(__FILE__);

    $versions[__FILE__] = $plugin_data['Version'];

    return $versions;

});