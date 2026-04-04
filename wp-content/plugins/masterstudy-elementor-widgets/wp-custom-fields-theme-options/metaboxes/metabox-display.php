<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly


/***
 * @var $post
 * @var $metabox
 * @var $args_id
 *
 */

$vue_id = '';


if (empty($id)) {
    /*We are on a post*/
    $post_id = $post->ID;
    $id = $metabox['id'];
    $sections = $metabox['args'][$id];
    $active = '';
    $vue_id = "data-vue='" . $id . "'";
    $source_id = "data-source='" . $post_id . "'";
}

?>

<div class="stm_metaboxes_grid <?php echo esc_attr('sections_count_' . count($sections)); ?>" <?php echo stm_wpcfto_filtered_output($vue_id . ' ' . $source_id); ?>>

    <div class="stm_metaboxes_grid__inner" v-if="data !== ''">

        <div class="container">

            <div class="stm-lms-tab-nav">
                <?php
                $i = 0;
                foreach ($sections as $section_name => $section):
                    if ($i == 0) $active = $section_name;
                    ?>
                    <div class="stm-lms-nav <?php if ($active == $section_name) echo 'active'; ?> <?php if (empty($section['icon'])) echo 'no-icon'; ?>"
                         data-section="<?php echo esc_attr($section_name); ?>"
                         @click="changeTab('<?php echo esc_attr($section_name); ?>')">

                        <?php if (!empty($section['icon'])) : ?>
                            <i class="<?php echo esc_attr($section['icon']); ?>"></i>
                        <?php endif; ?>

                        <?php echo sanitize_text_field($section['name']); ?>

                        <i class="fa fa-chevron-right"></i>

                    </div>
                    <?php $i++; endforeach; ?>
            </div>

            <?php foreach ($sections as $section_name => $section): ?>
                <div id="<?php echo esc_attr($section_name); ?>"
                     class="stm-lms-tab <?php if ($section_name == $active) echo 'active'; ?>">
                    <div class="container container-constructed">
                        <div class="row">

                            <div class="column">

                                <?php if (!empty($section['label'])) : ?>
                                    <div data-notice="enable_courses_filter_notice"
                                         class="wpcfto_generic_field wpcfto_generic_field__notice first opened">
                                        <label><?php echo sanitize_text_field($section['label']); ?></label>
                                    </div>
                                <?php endif; ?>

                                <?php foreach ($section['fields'] as $field_name => $field) {

                                    if(!empty($field['pre_open']) and $field['pre_open']) {
                                        stm_lms_metaboxes_preopen_field($section, $section_name, $field, $field_name);
                                        continue;
                                    }

                                    if(!empty($field['group'])) {
                                        stm_lms_metaboxes_display_group_field($section, $section_name, $field, $field_name);
                                        continue;
                                    }

                                    stm_lms_metaboxes_display_single_field($section, $section_name, $field, $field_name);

                                } ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>


    </div>
</div>