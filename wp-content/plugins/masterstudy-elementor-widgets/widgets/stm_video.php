<?php

use Elementor\Controls_Manager;

class Elementor_STM_Video extends \Elementor\Widget_Base {

	public function get_name() {
		return 'stm_video';
	}

	public function get_title() {
		return esc_html__( 'Video', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-video lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {
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
			'title',
			array(
				'label' => __( 'Title', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'         => __( 'URL (Link)', 'masterstudy-elementor-widgets' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'show_external' => true,
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'el_class',
			array(
				'label'       => __( 'Extra class name', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			)
		);

		$this->add_control(
			'video_style',
			array(
				'label'   => __( 'Style', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => array_flip(
					array(
						esc_html__( 'Style 1', 'masterstudy-elementor-widgets' ) => 'style_1',
						esc_html__( 'Style 2', 'masterstudy-elementor-widgets' ) => 'style_2',
					)
				),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.stm_video' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_stm_video ' . $settings['video_style'];

			masterstudy_show_template( 'stm_video', $settings );

		}
	}

	protected function content_template() {

	}

}
