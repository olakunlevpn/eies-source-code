<?php

use Elementor\Controls_Manager;

class Elementor_STM_Post_List extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_post_list';
	}

	public function get_title() {
		return esc_html__( 'Post List', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-post_list lms-icon';
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

		// Get post types to offer
		$post_list_data = array(
			'post'        => 'Post',
			'teachers'    => 'Experts',
			'testimonial' => 'Testimonials',
		);

		$this->add_control(
			'post_list_data_source',
			array(
				'label'   => __( 'Post Data', 'masterstudy-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $post_list_data,
				'default' => 'post',
			)
		);

		$this->add_control(
			'post_list_per_page',
			array(
				'label'       => __( 'Number of items to output', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Fill field with number only', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'post_list_per_row',
			array(
				'label'   => __( 'Number of items to output per row', 'masterstudy-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					6 => '6',
				),
				'default' => 3,
			)
		);

		$this->add_control(
			'post_list_show_date',
			array(
				'label' => __( 'Show post date', 'masterstudy-elementor-widgets' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'post_list_show_cats',
			array(
				'label' => __( 'Show post categories', 'masterstudy-elementor-widgets' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'post_list_show_tags',
			array(
				'label' => __( 'Show post tags', 'masterstudy-elementor-widgets' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'post_list_show_comments',
			array(
				'label' => __( 'Show comments tags', 'masterstudy-elementor-widgets' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'custom_color',
			array(
				'label' => __( 'Custom color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_post_list_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_post_list_';

			masterstudy_show_template( 'post_list', $settings );

		}
	}

	protected function content_template() {
	}

}
