<?php

require_once STM_WPCFTO_PATH . '/helpers/icons.php';

function stm_lms_term_meta_field_icon($field_key, $value)
{
	?>
    <div class="stm_lms_image_field">
        <input type="text"
               class="stm_lms_font"
               value="<?php echo sanitize_text_field($value); ?>"
               name="<?php echo esc_attr($field_key) ?>"/>
    </div>

	<?php
    $ms_icons = stm_wpcfto_add_vc_icons(array());
	$ms_icons = $ms_icons['STM LMS Icons'];
	$ms_icons_i = array();
	foreach ($ms_icons as $icon) {
		$icons = array_keys($icon);
		$ms_icons_i[] = $icons[0];
	}
    $ms_icons_i = apply_filters('stm_wpcfto_ms_icons', $ms_icons_i);
	$fa_icons_i = array();
	if(function_exists('stm_new_fa_icons')) {
		$fa_icons = stm_wpcfto_new_fa_icons();
		foreach ($fa_icons as $icon) {
			$icons = array_keys($icon);
			$fa_icons_i[] = $icons[0];
		}
	}

	$lr_icons = stm_wpcfto_add_vc_icons_linear(array());
	$lr_icons = $lr_icons['STM LMS Linear'];
	$lr_icons_i = array();
	foreach ($lr_icons as $icon) {
		$icons = array_keys($icon);
		$lr_icons_i[] = str_replace('lnr-', 'lnricons-', $icons[0]);
	}


	?>

    <script type="text/javascript">
        (function ($) {
            var icons = {
                'masterstudy-elementor-widgets': ["","<?php echo implode('","', $ms_icons_i); ?>"],
                'FontAwesome': ["","<?php echo implode('","', $fa_icons_i); ?>"],
                'Linear': ["","<?php echo implode('","', $lr_icons_i); ?>"],
            };

            var iconsSearch = {
                'masterstudy-elementor-widgets': ["","<?php echo implode('","', $ms_icons_i); ?>"],
                'FontAwesome': ["","<?php echo implode('","', $fa_icons_i); ?>"],
                'Linear': ["","<?php echo implode('","', $lr_icons_i); ?>"],
            };
            $(document).ready(function () {
                $('.stm_lms_font').each(function () {
                    $(this).fontIconPicker({
                        source: icons,
                        searchSource: iconsSearch,
                    });
                });
            });
        })(jQuery)
    </script>
<?php }