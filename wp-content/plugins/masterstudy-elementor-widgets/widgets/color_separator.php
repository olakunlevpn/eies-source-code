<?php

use Elementor\Controls_Manager;

class Elementor_STM_Colored_Separator extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_color_separator';
	}

	public function get_title() {
		return esc_html__( 'Colored Separator', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-color_separator lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Style', 'elementor-stm-widgets' ),
			)
		);

		$this->add_control(
			'color',
			array(
				'label' => __( 'Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_color_separator_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_color_separator_';

			$color  = ( isset( $settings['color'] ) ) ? $settings['color'] : '';
			$unique = stm_create_unique_id( $settings );

			$settings['unique']        = $unique;
			$settings['inline_styles'] = "
                .{$unique} .triangled_colored_separator {
                    background-color: {$color} !important;
                }
                .{$unique} .triangled_colored_separator .triangle {
                    border-bottom-color: {$color} !important;
                }
            ";

			masterstudy_show_template( 'color_separator', $settings );

		}
	}

	protected function content_template() {
	}

}
