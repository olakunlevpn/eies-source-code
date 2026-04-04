<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

if (class_exists('STM_LMS_Pro_Addons')):

	include STM_WPCFTO_PATH .'/metaboxes/components_js/addons.php';

	$addons_enabled = get_option('stm_lms_addons', array());
	?>

    <label v-html="<?php echo esc_attr($field_key); ?>['label']"></label>

    <stm-addons v-bind:enabled_addons='<?php echo json_encode($addons_enabled); ?>'></stm-addons>

<?php endif;