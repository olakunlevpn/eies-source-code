<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

include STM_WPCFTO_PATH .'/metaboxes/components_js/manage_post_type.php';
?>


<stm-manage-post-type v-bind:post_type="<?php echo esc_attr($field_key); ?>['post_type']" v-bind:meta_key="<?php echo esc_attr($field_key); ?>['meta_key']"></stm-manage-post-type>