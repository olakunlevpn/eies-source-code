<?php
function stm_theme_enable_addons($layout) {
	$addons = array(
		'udemy' => array(
			'udemy' => 'on',
		),
	);

	if(!empty($addons[$layout])) {
		update_option('stm_lms_addons', $addons[$layout]);
	}
}