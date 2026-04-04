<?php
add_action('init', 'stm_vc_post_type_init');

function stm_vc_post_type_init() {
	remove_filter('vc_iconpicker-type-fontawesome', 'vc_iconpicker_type_fontawesome');
}