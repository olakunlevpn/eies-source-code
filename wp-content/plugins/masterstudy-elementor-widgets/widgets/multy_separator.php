<?php

use Elementor\Controls_Manager;

class Elementor_STM_Multy_Separator extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_multy_separator';
	}

	public function get_title() {
		return esc_html__( 'Multy Separator', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-multy_separator lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {  }

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Style', 'elementor-stm-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_multy_separator_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_multy_separator_';

			masterstudy_show_template( 'multy_separator', $settings );

		}
	}

	protected function content_template() {
	}

}
