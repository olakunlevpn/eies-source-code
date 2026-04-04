<?php

use Elementor\Controls_Manager;

class Elementor_STM_Contact_Form_7 extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_contact_form_7';
	}

	public function get_title() {
		return esc_html__( 'Contact Form 7', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-contact_form_7 lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-stm-widgets' ),
			)
		);

		$cf7 = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );

		$contact_forms = array();
		if ( $cf7 ) {
			foreach ( $cf7 as $cform ) {
				$contact_forms[ $cform->ID ] = $cform->post_title;
			}
		} else {
			$contact_forms[0] = esc_html__( 'No contact forms found', 'js_composer' );
		}

		$this->add_control(
			'form_id',
			array(
				'label'   => __( 'Select form', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $contact_forms,
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			masterstudy_show_template( 'contact_form_7', $settings );

		}
	}

	protected function content_template() {
	}

}
