<?php

use Elementor\Controls_Manager;

class Elementor_STM_SignUpNow extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_sign_up_now';
	}

	public function get_title() {
		return esc_html__( 'Sign Up Now', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-sign_up_now lms-icon';
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
			'title',
			array(
				'label'       => __( 'Title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Title here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'form',
			array(
				'label'   => __( 'Choose form', 'masterstudy-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $available_cf7,
				'default' => 'none',
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_sign_up_now_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_sign_up_now_';

			masterstudy_show_template( 'sign_up_now', $settings );

		}
	}

	protected function content_template() {
	}

}
