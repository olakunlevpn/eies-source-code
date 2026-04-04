<?php

use Elementor\Controls_Manager;

class Elementor_STM_Pricing_Plan extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_pricing_plan';
	}

	public function get_title() {
		return esc_html__( 'Pricing Plan', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-pricing_plan lms-icon';
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

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Plan title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Plan Title here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'color',
			array(
				'label' => __( 'Plan Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'price',
			array(
				'label'       => __( 'Plan price', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Plan price here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'period',
			array(
				'label'       => __( 'Plan payment period', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type period here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'       => __( 'Plan Text', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Type Plan Text here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'button',
			array(
				'label'       => __( 'Plan Button', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'masterstudy-elementor-widgets' ),
				'default'     => array(
					'url' => '#',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Plan Button Text', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Plan Button Text here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_pricing_plan_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_pricing_plan_';

			$settings['button']['title'] = ( isset( $settings['button_text'] ) ) ? $settings['button_text'] : __( 'Buy', 'masterstudy-elementor-widgets' );

			$settings['plan'] = 'stm_pricing_plan_' . stm_create_unique_id( $settings );

			masterstudy_show_template( 'pricing_plan', $settings );

		}
	}

	protected function content_template() {
	}

}
