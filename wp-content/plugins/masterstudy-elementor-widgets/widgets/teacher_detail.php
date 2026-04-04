<?php

use Elementor\Controls_Manager;

class Elementor_STM_Teacher_Detail extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_teacher_detail';
	}

	public function get_title() {
		return esc_html__( 'Teacher Detail', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-teacher_detail lms-icon';
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
				'label' => __( 'Widget Information', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'important_note',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => __( 'This widget will call the teacher information from the custom Teachers post type.', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_teacher_detail_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_teacher_detail_';

			masterstudy_show_template( 'teacher_detail', $settings );

		}
	}

	protected function content_template() {
	}

}
