<?php

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

class Elementor_STM_Icon_Box extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_icon_box';
	}

	public function get_title() {
		return esc_html__( 'Icon Box', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-icon_box lms-icon';
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
			'title',
			array(
				'label'       => __( 'Title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Title here', 'masterstudy-elementor-widgets' ),
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
			'title_holder',
			array(
				'label'   => __( 'Title Holder', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
				),
				'default' => 'h3',
			)
		);

		$this->add_control(
			'hover_pos',
			array(
				'label'   => __( 'Hover position', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'none'   => esc_html__( 'None', 'masterstudy-elementor-widgets' ),
					'top'    => esc_html__( 'Top', 'masterstudy-elementor-widgets' ),
					'right'  => esc_html__( 'Right', 'masterstudy-elementor-widgets' ),
					'left'   => esc_html__( 'Left', 'masterstudy-elementor-widgets' ),
					'bottom' => esc_html__( 'Bottom', 'masterstudy-elementor-widgets' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'box_bg_color',
			array(
				'label' => __( 'Box background color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'box_text_color',
			array(
				'label' => __( 'Box text color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'box_icon_bg_color',
			array(
				'label' => __( 'Box icon color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'link_color_style',
			array(
				'label'       => __( 'Link color style', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'standart' => esc_html__( 'Standart', 'masterstudy-elementor-widgets' ),
					'dark'     => esc_html__( 'Dark', 'masterstudy-elementor-widgets' ),
				),
				'default'     => 'standart',
				'description' => __( 'Enter icon size in px', 'masterstudy' ),
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
				'label'       => __( 'Icon Size', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Enter icon size in px', 'masterstudy' ),
				'default'     => '60',
			)
		);

		$this->add_control(
			'icon_align',
			array(
				'label'   => __( 'Icon Align', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'left'       => esc_html__( 'Left', 'plugin-domain' ),
					'right'      => esc_html__( 'Right', 'plugin-domain' ),
					'top_left'   => esc_html__( 'Top Left', 'plugin-domain' ),
					'top_center' => esc_html__( 'Top Center', 'plugin-domain' ),
					'top_right'  => esc_html__( 'Top Right', 'plugin-domain' ),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'box_align',
			array(
				'label'   => __( 'Box Align', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'left'   => esc_html__( 'Left', 'plugin-domain' ),
					'right'  => esc_html__( 'Right', 'plugin-domain' ),
					'center' => esc_html__( 'Center', 'plugin-domain' ),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'icon_height',
			array(
				'label'       => __( 'Icon Height', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Enter icon height in px', 'masterstudy-elementor-widgets' ),
				'default'     => '65',
			)
		);

		$this->add_control(
			'icon_width',
			array(
				'label'       => __( 'Icon Width', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Enter icon width in px', 'masterstudy-elementor-widgets' ),
				'default'     => '65',
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'       => __( 'Icon Color', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'description' => 'Default - White',
			)
		);

		$this->add_control(
			'content',
			array(
				'label' => __( 'Text', 'elementor' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_css',
			array(
				'label' => __( 'Icon Box Css', 'elementor-stm-widgets' ),
			)
		);

		$selector = 'icon_box';

		$this->add_responsive_control(
			'margin_' . $selector,
			array(
				'label'      => __( 'Margin', 'masterstudy-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					"{{WRAPPER}} .{$selector}" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					"{{WRAPPER}} .{$selector}" => 'margin: 0px 0px 0px 0px;',
				),
			)
		);

		$this->add_responsive_control(
			'padding_' . $selector,
			array(
				'label'      => __( 'Padding', 'masterstudy-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					"{{WRAPPER}} .{$selector}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					"{{WRAPPER}} .{$selector}" => 'padding: 0px 0px 0px 0px;',
				),
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'       => __( 'Icon Box radius', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Enter Icon Box radius in px', 'masterstudy-elementor-widgets' ),
				'selectors'   => array(
					"{{WRAPPER}} .{$selector}" => 'border-radius: {{VALUE}}px',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_box_css',
			array(
				'label' => __( 'Icon Css', 'elementor-stm-widgets' ),
			)
		);

		$this->add_responsive_control(
			'margin_' . $selector . '_box',
			array(
				'label'      => __( 'Margin', 'masterstudy-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					"{{WRAPPER}} .{$selector} .icon" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					"{{WRAPPER}} .{$selector} .icon" => 'margin: 0px 0px 0px 0px;',
				),
			)
		);

		$this->add_responsive_control(
			'padding_' . $selector . '_box',
			array(
				'label'      => __( 'Padding', 'masterstudy-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					"{{WRAPPER}} .{$selector} .icon" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					"{{WRAPPER}} .{$selector} .icon" => 'padding: 0px 0px 0px 0px;',
				),
			)
		);

		$this->add_control(
			'border_radius_icon',
			array(
				'label'       => __( 'Icon radius', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Enter Icon radius in px', 'masterstudy-elementor-widgets' ),
				'selectors'   => array(
					"{{WRAPPER}} .{$selector} .icon" => 'border-radius: {{VALUE}}px',
				),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_icon_box ' );
	}


	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			if ( ! isset( $settings['icon']['library'] ) ) {
				return false;
			}

			$settings['unique']         = stm_create_unique_id( $settings );
			$settings['library']        = $settings['icon']['library'];
			$settings['css_class']      = ' masterstudy_elementor_icon_box ';
			$settings['css_icon']       = ' masterstudy_elementor_icon ';
			$settings['css_icon_class'] = ' ' . $settings['unique'];

			if ( ! empty( $settings['icon']['value'] ) ) {
				if ( 'svg' === $settings['library'] ) {
					$settings['icon'] = Icons_Manager::render_uploaded_svg_icon( $settings['icon']['value'] );
				} else {
					$settings['icon'] = ' ' . $settings['icon']['value'];
				}
			} else {
				$settings['icon'] = '';
			}

			$settings['atts'] = $settings;

			masterstudy_show_template( 'icon_box', $settings );
		}
	}

	protected function content_template() {
	}
}
