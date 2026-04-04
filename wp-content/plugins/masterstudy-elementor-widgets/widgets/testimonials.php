<?php

use Elementor\Controls_Manager;

class Elementor_STM_Testimonials extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_testimonials';
	}

	public function get_title() {
		return esc_html__( 'Testimonials', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-testimonials lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {  }

	protected function register_controls() {
		$args          = array(
			'post_type'      => 'wpcf7_contact_form',
			'posts_per_page' => -1,
		);
		$available_cf7 = array();
		$cf7Forms = get_posts( $args );
		if ( $cf7Forms && is_admin() ) {
			foreach ( $cf7Forms as $cf7Form ) {
				$available_cf7[ $cf7Form->ID ] = $cf7Form->post_title;
			};
		} else {
			$available_cf7['none'] = 'No CF7 forms found';
		};

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'testimonials_title',
			array(
				'label'       => __( 'Section title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Title will be shown on the top of section', 'masterstudy-elementor-widgets' ),
				'condition'   => array(
					'style' => array( 'style_1', 'style_2', 'style_3', 'style_4', 'style_5', 'style_6' ),
				),
			)
		);

		$this->add_control(
			'testimonials_max_num',
			array(
				'label'       => __( 'Number of testimonials to output', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Fill field with number only', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'testimonials_text_color',
			array(
				'label' => __( 'Text Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => __( 'Style', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_1' => __( 'Style 1', 'masterstudy-elementor-widgets' ),
					'style_2' => __( 'Style 2', 'masterstudy-elementor-widgets' ),
					'style_3' => __( 'Style 3', 'masterstudy-elementor-widgets' ),
					'style_4' => __( 'Style 4', 'masterstudy-elementor-widgets' ),
					'style_5' => __( 'Style 5', 'masterstudy-elementor-widgets' ),
					'style_6' => __( 'Style 6', 'masterstudy-elementor-widgets' ),
					'style_7' => __( 'Style 7', 'masterstudy-elementor-widgets' ),
					'style_8' => __( 'Style 8', 'masterstudy-elementor-widgets' ),
				),
				'default' => 'style_1',
			)
		);

		$this->add_control(
			'number_of_letters',
			array(
				'label'       => __( 'Number of letters', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Specify a number for the desired content length', 'masterstudy-elementor-widgets' ),
				'default'     => 240,
				'condition'   => array(
					'style' => array( 'style_7' ),
				),
			)
		);

		$this->add_control(
			'testimonials_slides_per_row',
			array(
				'label'   => __( 'Number of testimonials per row', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
				),
				'default' => 2,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_testimonials_' );

		$this->start_controls_section(
			'stars_styles',
			array(
				'label' => __( 'Stars', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'stars_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .fa-star' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_styles',
			array(
				'label' => __( 'Pagination', 'masterstudy-lms-learning-management-system' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'pagination_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .testimonials_main_wrapper .owl-carousel .owl-dots .owl-dot.active' => 'background-color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_testimonials_';

			$settings['icon'] = ( isset( $settings['icon']['value'] ) ) ? ' ' . $settings['icon']['value'] : '';

			$settings['atts'] = $settings;

			masterstudy_show_template( 'testimonials', $settings );

		}
	}

	protected function content_template() {
	}

}
