<?php

use Elementor\Controls_Manager;

class Elementor_STM_Events_Grid extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_events_grid';
	}

	public function get_title() {
		return esc_html__( 'Events Grid', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-events_grid lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {  }

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-stm-widgets' ),
			)
		);

		$this->add_control(
			'per_page',
			array(
				'label'       => __( 'Events per page', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Type Events per page here', 'elementor-stm-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_events_grid_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_events_grid_';

			masterstudy_show_template( 'events_grid', $settings );

		}
	}

	protected function content_template() {
	}

}
