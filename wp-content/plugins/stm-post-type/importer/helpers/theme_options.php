<?php
function stm_set_layout_options($layout)
{
	global $wp_filesystem;

	if (empty($wp_filesystem)) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$options = STM_POST_TYPE_PATH . '/importer/demos/' . $layout . '/options/theme_mods.json';

	if (file_exists($options)) {
		$encode_options = $wp_filesystem->get_contents($options);
		$import_options = json_decode( $encode_options, true );
		update_option('stm_option', $import_options);
	}

}