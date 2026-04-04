<?php

add_filter('stm_wpcfto_boxes', function ($boxes) {

    $data_boxes = array(
        'stm_hfe_settings' => array(
            'post_type' => array('elementor-hf'),
            'label' => esc_html__('STM additional settings for Headers only', 'masterstudy-elementor-widgets'),
        ),
    );

    $boxes = array_merge($data_boxes, $boxes);

    return $boxes;
});

add_filter('stm_wpcfto_fields', function ($fields) {

    $data_fields = array(
        'stm_hfe_settings' => array(
            'section_appearance' => array(
                'name' => esc_html__('Appearance', 'masterstudy-elementor-widgets'),
                'fields' => array(
                    'absolute' => array(
                        'label' => esc_html__('Header Absolute position (lies on top of the following elements)', 'masterstudy-elementor-widgets'),
                        'type' => 'checkbox',
                        'value' => false,
                    ),

                    'sticky' => array(
                        'group' => 'started',
                        'label' => esc_html__('Make Header Sticky', 'masterstudy-elementor-widgets'),
                        'type' => 'checkbox',
                        'value' => false,
                    ),

                    'sticky_threshold' => array(
                        'label' => esc_html__('Sticky threshold apply styles (px)', 'masterstudy-elementor-widgets'),
                        'placeholder' => esc_html__('Enter number of px needed to scroll down to show sticky header', 'masterstudy-elementor-widgets'),
                        'type' => 'number',
                        'value' => 100,
                        'dependency' => array(
                            'key' => 'sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => 50
                    ),
                    'sticky_threshold_color' => array(
                        'group' => 'ended',
                        'label' => esc_html__('Sticky threshold background color', 'masterstudy-elementor-widgets'),
                        'type' => 'color',
                        'value' => '#000',
                        'position' => 'top',
                        'dependency' => array(
                            'key' => 'sticky',
                            'value' => 'not_empty'
                        ),
                    ),


                )
            )
        ),
    );

    $fields = array_merge($data_fields, $fields);

    return $fields;
});