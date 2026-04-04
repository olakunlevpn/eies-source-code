<?php

use Elementor\Controls_Manager;

class Elementor_STM_Countddown extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_countdown';
	}

	public function get_title() {
		return esc_html__( 'Countdown', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-countdown lms-icon';
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
			'label_color',
			array(
				'label' => __( 'Labels color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'datepicker',
			array(
				'label'          => __( 'Date', 'elementor' ),
				'placeholder'    => __( 'Tab Date', 'elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'enableTime' => false,
				),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_countdown_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_countdown_';

			masterstudy_show_template( 'countdown', $settings );

		}
	}

	protected function content_template() {
	}

}
