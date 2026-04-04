<?php

use Elementor\Controls_Manager;

class Elementor_STM_Post_Info extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_post_info';
	}

	public function get_title() {
		return esc_html__( 'Post Info', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-post_info lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {  }

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_post_info_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_post_info_';

			masterstudy_show_template( 'post_info', $settings );

		}
	}

	protected function content_template() {
	}

}
