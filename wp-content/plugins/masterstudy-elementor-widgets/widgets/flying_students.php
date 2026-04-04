<?php

use Elementor\Controls_Manager;

class Elementor_STM_Flying_Students extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_flying_students';
	}

	public function get_title() {
		return esc_html__( 'Flying Students', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-flying_students lms-icon';
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
			'horizontal_align',
			array(
				'label'   => __( 'Vertical Align', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'left'   => 'Left',
					'center' => 'Center',
					'right'  => 'Right',
				),
				'default' => 'center',
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_flying_students' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_flying_students';

			masterstudy_show_template( 'flying_students', $settings );

		}
	}

	protected function content_template() {
	}

}
