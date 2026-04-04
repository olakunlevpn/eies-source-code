<?php
function cew_inline_css()
{

    ob_start();

    $secondary_color = stm_option('secondary_color', '#2c75e4');
    $font_btn = stm_option('font_btn');
    ?>

    <style>
        @media (min-width: 768px) {
            .elementor-column-gap-default,
            .elementor-column-gap-default .elementor-row .elementor-column {
                padding: 0 15px
            }

            .elementor-column-gap-default .elementor-row {
                margin: 0 -15px !important;
                width: calc(100% + 30px) !important
            }

            .elementor-column-gap-default .elementor-row .elementor-column > .elementor-element-populated,
            .elementor-column-gap-default .elementor-row .elementor-row .elementor-column:first-child:last-child {
                padding: 0
            }

            .elementor-column-gap-default .elementor-row .elementor-row .elementor-column:first-child {
                padding-left: 0
            }

            .elementor-column-gap-default .elementor-row .elementor-row .elementor-column:last-child {
                padding-right: 0
            }

            #main .elementor-section.elementor-section-boxed.auto-margin:not(.elementor-section-stretched) > .elementor-container {
                margin: 0 auto;
            }
        }

        .elementor-container .stm_lms_courses_carousel__buttons .fa {
            font-weight: 900;
        }



        .stm_lms_courses_categories.style_1 .stm_lms_courses_category__image {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .stm_lms_lazy_image img {
            height: 100%;
        }

        .elementor-widget-tabs.elementor-tabs-view-vertical .elementor-tab-desktop-title {
            writing-mode: vertical-lr;
            text-orientation: mixed;
        }

        .elementor-widget-tabs.elementor-tabs-view-vertical .elementor-tab-content {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .elementor-editor-active .select2-container .select2-selection--single {
            height: 45px;
        }

        .elementor-editor-active .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
        }

        .elementor-editor-active .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
            width: 48px;
        }

        .elementor-editor-active .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            border: 0;
            line-height: 45px;
            text-align: center;
        }

    </style>

    <?php

    $css = ob_get_contents();
    ob_end_clean();

    return str_replace(array('<style>', '</style>'), '', apply_filters('masterstudy-elementor-widgets-styles', $css));
}
