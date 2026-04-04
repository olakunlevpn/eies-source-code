<?php

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

class Elementor_STM_Icon_Button extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_icon_button';
	}

	public function get_title() {
		return esc_html__( 'Icon Button', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-icon_button lms-icon';
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
			'button_text',
			array(
				'label' => __( 'Button Text', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'elementor' ),
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
			'link_tooltip',
			array(
				'label' => __( 'Link tooltip (title)', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'btn_align',
			array(
				'label'   => __( 'Button alignment', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'center' => esc_html__( 'Center', 'masterstudy-elementor-widgets' ),
					'left'   => esc_html__( 'Left', 'masterstudy-elementor-widgets' ),
					'right'  => esc_html__( 'Right', 'masterstudy-elementor-widgets' ),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'btn_size',
			array(
				'label'   => __( 'Button Size', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'btn-sm'          => esc_html__( 'Small', 'masterstudy-elementor-widgets' ),
					'btn-normal-size' => esc_html__( 'Normal', 'masterstudy-elementor-widgets' ),
				),
				'default' => 'btn-sm',
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label' => __( 'Button Text Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label' => __( 'Button Text Color Hover', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'button_bg_color',
			array(
				'label' => __( 'Button Background Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label' => __( 'Button Background Color Hover', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label' => __( 'Button Border Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label' => __( 'Button Border Color Hover', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
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
			'icon_size',
			array(
				'label'   => __( 'Icon Size', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'10' => esc_html__( '10px', 'masterstudy-elementor-widgets' ),
					'11' => esc_html__( '11px', 'masterstudy-elementor-widgets' ),
					'12' => esc_html__( '12px', 'masterstudy-elementor-widgets' ),
					'13' => esc_html__( '13px', 'masterstudy-elementor-widgets' ),
					'14' => esc_html__( '14px', 'masterstudy-elementor-widgets' ),
					'15' => esc_html__( '15px', 'masterstudy-elementor-widgets' ),
					'16' => esc_html__( '16px', 'masterstudy-elementor-widgets' ),
					'17' => esc_html__( '17px', 'masterstudy-elementor-widgets' ),
					'18' => esc_html__( '18px', 'masterstudy-elementor-widgets' ),
					'19' => esc_html__( '19px', 'masterstudy-elementor-widgets' ),
					'20' => esc_html__( '20px', 'masterstudy-elementor-widgets' ),
				),
				'default' => '16',
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_icon_button_' );
	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			if ( ! isset( $settings['icon']['library'] ) ) {
				return false;
			}

			$settings['library']       = $settings['icon']['library'];
			$settings['css_class']     = ' masterstudy_elementor_icon_button_';
			$settings['link']['title'] = ( isset( $settings['button_text'] ) ) ? $settings['button_text'] : __( 'Button', 'masterstudy-elementor-widgets' );

			if ( ! empty( $settings['icon']['value'] ) ) {
				if ( 'svg' === $settings['library'] ) {
					$settings['icon'] = Icons_Manager::render_uploaded_svg_icon( $settings['icon']['value'] );
				} else {
					$settings['icon'] = ' ' . $settings['icon']['value'];
				}
			} else {
				$settings['icon'] = '';
			}

			masterstudy_show_template( 'icon_button', $settings );

		}
	}

	protected function content_template() {
	}
}
