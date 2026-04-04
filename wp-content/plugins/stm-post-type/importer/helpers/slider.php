<?php

function stm_theme_import_sliders($layout)
{
	$slider_names = array(
		'default' => array(
			'main_slider',
		),
		'online-dark' => array(
			'lms-slider',
		),
		'classic_lms' => array(
			'main_slider',
		),
        'classic-lms-2' => array(
            'main_slider4',
        ),
		'udemy' => array(
			'home_slider',
		),
		'single_instructor' => array(
			'home_slider',
		),
        'rtl-demo' => array(
            'lms-slider',
        ),
        'buddypress-demo' => array(
            'lms-slider',
        ),
        'tech' => array(
            'rotating-words1',
            'slider-1',
        ),
	);

	if (!empty($slider_names[$layout])) {

		if (class_exists('RevSlider')) {
			$path = STM_POST_TYPE_PATH . '/importer/demos/' . $layout . '/sliders/';
			foreach ($slider_names[$layout] as $slider_name) {
				$slider_path = $path . $slider_name . '.zip';
				if (file_exists($slider_path)) {
					$slider = new RevSlider();
					$slider->importSliderFromPost(true, true, $slider_path);
				}
			}
		}
	}
}