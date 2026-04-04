<?php

use Elementor\Controls_Manager;

class Elementor_STM_Share extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_share';
	}

	public function get_title() {
		return esc_html__( 'Share', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-share lms-icon';
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
				'label'       => __( 'Title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Title here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'code',
			array(
				'label'   => __( 'Code', 'masterstudy-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => "<span class='st_facebook_large' displayText=''></span>
<span class='st_twitter_large' displayText=''></span>
<span class='st_googleplus_large' displayText=''></span>",
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_share_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_share_';

			masterstudy_show_template( 'share', $settings );

		}
	}

	protected function content_template() {
	}

}
