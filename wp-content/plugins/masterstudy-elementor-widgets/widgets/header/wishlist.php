<?php

use Elementor\Controls_Manager;

class Elementor_STM_LMS_Wishlist extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_lms_wishlist';
	}

	public function get_title() {
		return esc_html__( 'Wishlist', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-wishlist lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {
		$this->start_controls_section(
			'section_dimensions',
			array(
				'label' => __( 'Dimensions', 'elementor-stm-widgets' ),
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => __( 'Margin', 'masterstudy-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					"{{WRAPPER}} {$selector}" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => __( 'Padding', 'masterstudy-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					"{{WRAPPER}} {$selector}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'plugin-name' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value' => 'lnr lnr-bookmark',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => __( 'Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy_elementor_stm_lms_wishlist i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_stm_lms_wishlist' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_stm_lms_wishlist';

			masterstudy_show_template( 'header/wishlist', $settings );

		}
	}

	protected function content_template() {
	}

}
