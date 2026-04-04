<?php

use Elementor\Controls_Manager;

class Elementor_STM_Image_Box extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_image_box';
	}

	public function get_title() {
		return esc_html__( 'Image Box', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-image_box lms-icon';
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
			'style',
			array(
				'label'   => __( 'Style', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'style_1' => 'Style 1',
					'style_2' => 'Style 2',
					'style_3' => 'Style 3',
				),
				'default' => 'style_1',
			)
		);

		$this->add_control(
			'align',
			array(
				'label'   => __( 'Align', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'left'   => 'Left',
					'right'  => 'Right',
					'center' => 'Center',
				),
				'default' => 'left',
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
			'image_size',
			array(
				'label'       => __( 'Image Size', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Image Size here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'textarea',
			array(
				'label' => __( 'Content', 'elementor' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$this->add_control(
			'button',
			array(
				'label'       => __( 'Button', 'elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'elementor' ),
				'default'     => array(
					'url' => '#',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'TypeButton Text here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'text-domain' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value' => '',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label' => __( 'Background Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_image_box_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_image_box ';

			$settings['icon'] = ( isset( $settings['icon']['value'] ) ) ? ' ' . $settings['icon']['value'] : '';

			$settings['button']['title'] = ( isset( $settings['button_text'] ) && ! empty( $settings['button_text'] ) ) ? $settings['button_text'] : 'Button';

			$settings['main_class'] = stm_create_unique_id( $settings );

			$settings['atts'] = array(
				'main_class' => $settings['main_class'],
				'style'      => $settings['style'],
				'align'      => $settings['align'],
				'title'      => $settings['title'],
				'image'      => $settings['image'],
				'image_size' => $settings['image_size'],
				'textarea'   => $settings['textarea'],
				'button'     => $settings['button'],
				'icon'       => $settings['icon'],
				'icon_bg'    => $settings['icon_bg'],
				'button'     => $settings['button'],
			);

			masterstudy_show_template( 'image_box', $settings );

		}
	}

	protected function content_template() {
	}

}
