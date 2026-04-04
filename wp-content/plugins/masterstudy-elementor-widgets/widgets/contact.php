<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

class Elementor_STM_Contact extends Widget_Base {
	public function get_name() {
		return 'stm_contact';
	}

	public function get_title() {
		return esc_html__( 'Contact', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-contact lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	public function add_dimensions( $selector = '' ) {  }

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-stm-widgets' ),
			)
		);

		$this->add_control(
			'name',
			array(
				'label'       => __( 'Name', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type contact name here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Choose Image', 'masterstudy-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'       => __( 'Image size', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "default" size.', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'job',
			array(
				'label'       => __( 'Job', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type contact job here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'       => __( 'Phone', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type contact phone here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'email',
			array(
				'label'       => __( 'Email', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type contact email here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'skype',
			array(
				'label'       => __( 'Skype', 'masterstudy-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type contact skype here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_contact_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_contact_';

			$settings['image']['thumbnail'] = wp_get_attachment_image( $settings['image']['id'], $settings['image_size'] );

			$settings['image_id'] = intval( $settings['image']['id'] );

			masterstudy_show_template( 'contact', $settings );

		}
	}

	protected function content_template() {
	}

}
