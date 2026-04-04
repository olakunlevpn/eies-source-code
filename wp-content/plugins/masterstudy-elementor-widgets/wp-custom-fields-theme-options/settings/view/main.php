<?php

/**
 * @var $metabox
 * @var $page
 */

if (!defined('ABSPATH')) exit; //Exit if accessed directly

?>

    <h1><?php echo sanitize_text_field($page['page_title']); ?></h1>

<?php
$id = $metabox['id'];
$sections = $metabox['args'][$id];
$source_id = 'data-source="settings"';


do_action("wpcfto_settings_screen_{$id}_before");

if (!empty($sections)) : ?>

    <div class="stm-lms-settings"
         v-bind:class="'data-' + data.length"
         data-vue="<?php echo sanitize_text_field($id); ?>" <?php echo stm_wpcfto_filtered_output($source_id); ?>>

        <?php require_once(STM_WPCFTO_PATH . '/metaboxes/metabox-display.php'); ?>

        <div class="stm_metaboxes_grid stm_metaboxes_grid_btn">
            <div class="stm_metaboxes_grid__inner">
                <a href="#"
                   @click.prevent="saveSettings('<?php echo esc_attr($id); ?>')"
                   v-bind:class="{'loading': loading}"
                   class="button load_button">
                    <span><?php esc_html_e('Save Settings', 'wp-custom-fields-theme-options'); ?></span>
                    <i class="lnr lnr-sync"></i>
                </a>
            </div>
        </div>

    </div>

<?php endif;

do_action("wpcfto_settings_screen_{$id}_after");
