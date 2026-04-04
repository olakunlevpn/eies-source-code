<?php
add_action('after_setup_theme', 'stm_post_type_local_theme_setup');
function stm_post_type_local_theme_setup() {
	add_image_size('img-840-400', 840, 400, true);
	add_image_size('img-480-380', 480, 380, true);
	add_image_size('img-300-225', 300, 225, true);
	add_image_size('img-270-283', 270, 283, true);
	add_image_size('img-270-180', 270, 180, true);
	add_image_size('img-270-153', 270, 153, true);
	add_image_size('img-129-129', 129, 129, true);
	add_image_size('img-122-120', 122, 120, true);
	add_image_size('img-75-75', 75, 75, true);
	add_image_size('img-69-69', 69, 69, true);
	add_image_size('img-63-50', 63, 50, true);
	add_image_size('img-50-56', 50, 56, true);
}