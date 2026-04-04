<?php

use Elementor\Controls_Manager;

class Elementor_STM_Experts extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_experts';
	}

	public function get_title() {
		return esc_html__( 'Experts', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-experts lms-icon';
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
			'experts_title',
			array(
				'label'       => __( 'Section title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Section Title here', 'masterstudy-elementor-widgets' ),
				'description' => __( 'Title will be shown on the top of section', 'masterstudy' ),
			)
		);

		$this->add_control(
			'experts_max_num',
			array(
				'label'       => __( 'Number of Teachers to output', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => __( 'Type Number of Teachers here', 'masterstudy-elementor-widgets' ),
				'description' => __( 'Fill field with number only', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'experts_output_style',
			array(
				'label'   => __( 'Style', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'experts_carousel' => 'Carousel',
					'experts_list'     => 'List',
				),
				'default' => 'experts_carousel',
			)
		);

		$this->add_control(
			'experts_all',
			array(
				'label'   => __( 'All teachers', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'yes' => 'Show link to all Teachers',
					'no'  => 'Hide link to all Teachers',
				),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'expert_slides_per_row',
			array(
				'label'   => __( 'Number of Teachers per row', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					1 => '1',
					2 => '2',
				),
				'default' => 1,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_experts_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_experts_';

			masterstudy_show_template( 'experts', $settings );

		}
	}

	protected function content_template() {
	}

}
