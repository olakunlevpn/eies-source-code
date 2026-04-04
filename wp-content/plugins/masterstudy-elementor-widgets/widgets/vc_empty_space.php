<?php

use Elementor\Controls_Manager;

class Elementor_STM_Empty_Space extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_empty_space';
	}

	public function get_title() {
		return esc_html__( 'Empty Space', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-empty_space lms-icon';
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
			'height',
			array(
				'label'       => __( 'Height', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => '32',
				'description' => 'Height in px',
			)
		);

		$this->add_control(
			'el_id',
			array(
				'label' => __( 'Element ID', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
			)
		);

		$this->add_control(
			'el_class',
			array(
				'label' => __( 'Extra class name', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'laptop_height',
			array(
				'label' => __( 'Laptop Height', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
			)
		);

		$this->add_control(
			'tablet_height',
			array(
				'label' => __( 'Tablet Height', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::NUMBER,
			)
		);

		$this->add_control(
			'mobile_height',
			array(
				'label' => __( 'Mobile Height', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_testimonials_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$wrapper_attributes = array();
			if ( ! empty( $settings['el_id'] ) ) {
				$wrapper_attributes[] = 'id="' . esc_attr( $settings['el_id'] ) . '"';
			}

			$height = floatval( $settings['height'] );

			$settings['inline_css'] = ( (float) $height >= 0.0 ) ? ' style="height: ' . esc_attr( $height ) . 'px"' : '';

			$settings['wrapper_attributes'] = $wrapper_attributes;

			$settings['uniq'] = stm_create_unique_id( $settings );

			$settings['css_class'] = ' masterstudy_elementor_empty_space ' . $settings['uniq'];

			$settings['atts'] = $settings;

			masterstudy_show_template( 'vc_empty_space', $settings );

		}
	}

	protected function content_template() {
	}

}
