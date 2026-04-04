<?php

use Elementor\Controls_Manager;

class Elementor_STM_LMS_Courses_Search extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_lms_courses_search';
	}

	public function get_title() {
		return esc_html__( 'Courses Search', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-course_search lms-icon';
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
			'include_categories',
			array(
				'label'        => __( 'Show Categories', 'masterstudy-elementor-widgets' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'your-plugin' ),
				'label_off'    => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => __( 'Limit categories', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'step'      => 1,
				'default'   => 10,
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => __( 'Categories Background Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy_elementor_stm_lms_courses_search_' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => __( 'Categories Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stm_lms_categories span, {{WRAPPER}} .stm_lms_categories i' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->add_control(
			'second_background_color',
			array(
				'label'     => __( 'Categories dropdown background', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy_elementor_stm_lms_courses_search_ .stm_lms_categories_dropdown__parents' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->add_control(
			'second_color',
			array(
				'label'     => __( 'Categories dropdown color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy_elementor_stm_lms_courses_search_ .stm_lms_categories_dropdown__parent > a' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->add_control(
			'input_background_color',
			array(
				'label'     => __( 'Search background Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy_elementor_stm_lms_courses_search_ input' => 'background-color: {{VALUE}}; border-color : {{VALUE}}',
				),
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->add_control(
			'input_color',
			array(
				'label'     => __( 'Search Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy_elementor_stm_lms_courses_search_ input' => 'color : {{VALUE}}',
				),
				'condition' => array(
					'include_categories' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_stm_lms_courses_search_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			if ( ! empty( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
				$this->content_template();
			} else {
				$settings = $this->get_settings_for_display();

				$settings['css_class'] = ' masterstudy_elementor_stm_lms_courses_search_';

				masterstudy_show_template( 'header/courses_search', $settings );
			}
		}
	}

	protected function content_template() {
		 echo 'LMS Courses Search';
	}

}
