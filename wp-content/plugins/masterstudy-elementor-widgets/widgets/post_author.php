<?php

use Elementor\Controls_Manager;

class Elementor_STM_Post_Author extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_post_author';
	}

	public function get_title() {
		return esc_html__( 'Post Author', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-post_author lms-icon';
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

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_post_author_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_post_author_';

			masterstudy_show_template( 'post_author', $settings );

		}
	}

	protected function content_template() {
	}

}
