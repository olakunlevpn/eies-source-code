<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

include STM_WPCFTO_PATH .'/metaboxes/components_js/udemy/search.php';

?>

<label v-html="<?php echo esc_attr($field_key); ?>['label']"></label>

<stm-udemy-search></stm-udemy-search>