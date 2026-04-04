<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Controls_Stack;
use Elementor\Plugin;

class Elementor_VC_Custom_Heading extends \Elementor\Widget_Base {

	public function get_name() {
		return 'vc_custom_heading';
	}

	public function get_title() {
		return esc_html__( 'Custom Heading', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-custom_heading lms-icon';
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
			'icon',
			array(
				'label' => __( 'Icon', 'text-domain' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label' => __( 'Icon size (px)', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'icon_pos',
			array(
				'label'   => __( 'Icon - Position', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array_flip(
					array(
						esc_html__( 'Left', 'masterstudy' ) => '',
						esc_html__( 'Right', 'masterstudy' ) => 'right',
						esc_html__( 'Top', 'masterstudy' ) => 'top',
						esc_html__( 'Bottom', 'masterstudy' ) => 'bottom',
					)
				),
			)
		);

		$this->add_control(
			'icon_pos_right',
			array(
				'label'     => __( 'Icon - right position', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array_flip(
					array(
						esc_html__( 'Right default', 'masterstudy' ) => '',
						esc_html__( 'Right after the text', 'masterstudy' ) => 'right_text',
					)
				),
				'condition' => array(
					'icon_pos' => 'right',
				),
			)
		);

		$this->add_control(
			'icon_pos_top',
			array(
				'label'     => __( 'Icon - top position', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array_flip(
					array(
						esc_html__( 'Top and center', 'masterstudy' ) => 'top_center',
						esc_html__( 'Top and right', 'masterstudy' ) => 'top_right',
						esc_html__( 'Top and left', 'masterstudy' ) => 'top_left',
					)
				),
				'condition' => array(
					'icon_pos' => 'top',
				),
			)
		);

		$this->add_control(
			'icon_pos_bottom',
			array(
				'label'     => __( 'Icon - bottom position', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array_flip(
					array(
						esc_html__( 'Bottom and center', 'masterstudy' ) => 'bottom_center',
						esc_html__( 'Bottom and right', 'masterstudy' ) => 'bottom_right',
						esc_html__( 'Bottom and left', 'masterstudy' ) => 'bottom_left',
					)
				),
				'condition' => array(
					'icon_pos' => 'bottom',
				),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label' => __( 'Subtitle', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label' => __( 'Subtitle Color', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'stripe_pos',
			array(
				'label'   => __( 'Stripe Position', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array_flip(
					array(
						esc_html__( 'Bottom', 'masterstudy' ) => 'bottom',
						esc_html__( 'Between Title and Subtitle', 'masterstudy' ) => 'between',
						esc_html__( 'Top and Bottom', 'masterstudy' ) => 'top_bottom',
						esc_html__( 'Hide', 'masterstudy' )   => 'hide',
					)
				),
			)
		);

		$this->add_control(
			'stm_title_font_weight',
			array(
				'label'   => __( 'Font weight', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array_flip(
					array(
						esc_html__( 'Select', 'masterstudy' )  => '',
						esc_html__( 'Thin', 'masterstudy' )    => 100,
						esc_html__( 'Thin-Light', 'masterstudy' ) => 200,
						esc_html__( 'Light', 'masterstudy' )   => 300,
						esc_html__( 'Regular', 'masterstudy' ) => 400,
						esc_html__( 'Medium', 'masterstudy' )  => 500,
						esc_html__( 'Semi-bold', 'masterstudy' ) => 600,
						esc_html__( 'Bold', 'masterstudy' )    => 700,
						esc_html__( 'Black', 'masterstudy' )   => 900,
					)
				),
				'default' => '400',
			)
		);

		/*COMPOSER SETTINGS*/
		$this->add_control(
			'source',
			array(
				'label'       => __( 'Source', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array_flip(
					array(
						esc_html__( 'Custom text', 'js_composer' ) => '',
						esc_html__( 'Post or Page Title', 'js_composer' ) => 'post_title',
					)
				),
				'description' => esc_html__( 'Select text source.', 'js_composer' ),
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'description' => esc_html__( 'Note: If you are using non-latin characters be sure to activate them under Settings/WPBakery Page Builder/General Settings.', 'js_composer' ),
				'condition'   => array(
					'source' => '',
				),
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

		masterstudy_Elementor_Widgets::add_font_settings( $this, 'font_container', array(), '.masterstudy-custom-title' );

		$this->add_control(
			'el_id',
			array(
				'label'       => __( 'Element ID', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
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

		$this->end_controls_section();

		$this->add_dimensions( '.vc_custom_heading' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = " vc_custom_heading {$settings['el_class']} masterstudy_heading_font ";

			$settings['use_theme_fonts'] = 'yes';

			$settings['font_container_data'] = array();

			$settings['font_container_data']['values'] = masterstudy_Elementor_Widgets::get_font_settings( $settings, 'font_container' );

			$settings['icon'] = $settings['icon']['value'];

			$settings['link'] = masterstudy_Elementor_Widgets::build_link( $settings );

			/*Styles*/
			$styles_data        = masterstudy_Elementor_Widgets::get_font_settings( $settings, 'font_container' );
			$settings['styles'] = array();
			if ( ! empty( $styles_data ) ) {
				if ( ! empty( $styles_data['font_size'] ) ) {
					$settings['styles'][] = "font-size: {$styles_data['font_size']}px";
				}
				if ( ! empty( $styles_data['color'] ) ) {
					$settings['styles'][] = "color: {$styles_data['color']}";
				}
				if ( ! empty( $styles_data['text_align'] ) ) {
					$settings['styles'][] = "text-align: {$styles_data['text_align']}";
				}
				if ( ! empty( $styles_data['line_height'] ) ) {
					$settings['styles'][] = "line-height: {$styles_data['line_height']}px";
				}
			}

			$settings['text'] = strip_tags( wpautop( $settings['text'] ), '<br> <mark>' );

			masterstudy_show_template( 'vc_custom_heading', $settings );

		}
	}

	protected function content_template() {

	}

}
