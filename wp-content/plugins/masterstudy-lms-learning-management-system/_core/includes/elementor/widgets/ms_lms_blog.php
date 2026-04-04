<?php

use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MsLmsBlog extends Widget_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style( 'lms-blog', STM_LMS_URL . 'assets/css/elementor-widgets/blog.css', array(), STM_LMS_VERSION, false );
	}

	public function get_name() {
		return 'ms_lms_blog';
	}

	public function get_title() {
		return esc_html__( 'Blog', 'masterstudy-lms-learning-management-system' );
	}

	public function get_style_depends() {
		return array( 'lms-blog' );
	}

	public function get_icon() {
		return 'stmlms-posts lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms' );
	}

	/** Register General Controls */
	protected function register_controls() {
		$this->register_general_content_controls();
		$this->register_style_controls_layout();
		$this->register_style_controls_date();
		$this->register_style_controls_title();
		$this->register_style_controls_taxonomy();
		$this->register_style_controls_pagination();
	}

	protected function register_general_content_controls() {
		$this->start_controls_section(
			'section_layout_fields',
			array(
				'label' => esc_html__( 'Layout', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'blog_style',
			array(
				'label'   => esc_html__( 'Style', 'masterstudy-lms-learning-management-system' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => array(
					'classic' => esc_html__( 'Classic', 'masterstudy-lms-learning-management-system' ),
					'cards'   => esc_html__( 'Cards', 'masterstudy-lms-learning-management-system' ),
				),
			)
		);
		$this->add_responsive_control(
			'blog_columns',
			array(
				'label'              => esc_html__( 'Columns', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'devices'            => array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default'    => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'frontend_available' => true,
			)
		);
		$this->add_control(
			'blog_per_page',
			array(
				'name'    => 'posts_per_page',
				'label'   => __( 'Posts per page', 'masterstudy-lms-learning-management-system' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 10,
			)
		);
		$this->add_control(
			'blog_pagination',
			array(
				'label'              => esc_html__( 'Pagination', 'masterstudy-lms-learning-management-system' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'masterstudy-lms-learning-management-system' ),
				'label_off'          => esc_html__( 'Off', 'masterstudy-lms-learning-management-system' ),
				'return_value'       => 'yes',
				'default'            => false,
				'frontend_available' => true,
			)
		);
		$this->end_controls_section();
	}

	/** Register Typography Controls */
	protected function register_style_controls_layout() {

		$this->start_controls_section(
			'section_layout_styles',
			array(
				'label' => esc_html__( 'Layout', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'section_layout_columns_gap',
			array(
				'label'      => esc_html__( 'Columns Gap', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors'  => array(
					'body {{WRAPPER}} .masterstudy-post-template section' => 'padding: 0 {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_layout_row_gap',
			array(
				'label'      => esc_html__( 'Row Gap', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => -15,
				),
				'selectors'  => array(
					'body {{WRAPPER}} .masterstudy-post-template' => 'margin: 0 {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'section_layout_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'section_layout_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main .masterstudy-post-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .masterstudy-post-template-main-info' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_date() {

		$this->start_controls_section(
			'section_date',
			array(
				'label'     => esc_html__( 'Date', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'blog_style' => 'classic',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_date_typography',
				'selector'       => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->start_controls_tabs(
			'section_date_tab'
		);
		$this->start_controls_tab(
			'section_date_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_date_background',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_date_border',
				'selector' => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date',
			)
		);
		$this->add_control(
			'section_date_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_date_shadow',
				'selector' => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date',
			)
		);
		$this->add_responsive_control(
			'section_date_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 240,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'section_date__hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_date_background_hover',
			array(
				'label'     => esc_html__( 'Background', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'section_date_border_hover',
				'selector' => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date:hover',
			)
		);
		$this->add_control(
			'section_date_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_date_shadow_hover',
				'selector' => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date:hover',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'section_date_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_title() {

		$this->start_controls_section(
			'section_title',
			array(
				'label' => esc_html__( 'Title', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_title_typography',
				'selector'       => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-title',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->start_controls_tabs(
			'section_title_tab'
		);
		$this->start_controls_tab(
			'section_title_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_title_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-title a' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'section_title_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_title_color_hover',
			array(
				'label'     => esc_html__( 'Color Hover', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'section_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_taxonomy() {

		$this->start_controls_section(
			'section_taxonomy',
			array(
				'label' => esc_html__( 'Taxonomy', 'masterstudy-lms-learning-management-system' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'section_taxonomy_typography',
				'selector'       => '{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list',
				'fields_options' => array(
					'typography' => array(
						'label' => esc_html__( 'Typography', 'masterstudy-lms-learning-management-system' ),
					),
				),
			)
		);
		$this->start_controls_tabs(
			'section_taxonomy_tab'
		);
		$this->start_controls_tab(
			'section_taxonomy_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_taxonomy_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list, {{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list a' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'section_taxonomy_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'section_taxonomy_color_hover',
			array(
				'label'     => esc_html__( 'Color Hover', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list:hover, {{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list a:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'section_taxonomy_padding',
			array(
				'label'      => esc_html__( 'Padding', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'section_taxonomy_margin',
			array(
				'label'      => esc_html__( 'Margin', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template-main-info .masterstudy-post-category-list a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	protected function register_style_controls_pagination() {

		$this->start_controls_section(
			'style_pagination',
			array(
				'label'     => esc_html__( 'Pagination', 'masterstudy-lms-learning-management-system' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'blog_pagination' => 'yes',
				),
			)
		);
		$this->add_responsive_control(
			'style_pagination_align',
			array(
				'label'     => esc_html__( 'Alignment', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'masterstudy-lms-learning-management-system' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination' => 'justify-content: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'style_pagination_pages_typography',
				'selector' => '{{WRAPPER}} .masterstudy-post-template__pagination_list_item a, {{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots)',
			)
		);
		$this->add_responsive_control(
			'style_pagination_pages_width',
			array(
				'label'      => esc_html__( 'Width', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item a' => 'min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots)' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'style_pagination_pages_height',
			array(
				'label'      => esc_html__( 'Height', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item a' => 'min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots)' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs(
			'pagination_pages_tabs'
		);
		$this->start_controls_tab(
			'pagination_pages_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'pagination_pages_color',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots):not(.current)' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pagination_pages_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-post-template__pagination_list_item a, {{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots):not(.current)',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_pages_border',
				'selector' => '{{WRAPPER}} .masterstudy-post-template__pagination_list_item a, {{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots):not(.current)',
			)
		);
		$this->add_control(
			'pagination_pages_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item span:not(.dots):not(.current)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_pages_hover_tab',
			array(
				'label' => esc_html__( 'Hover | Active', 'masterstudy-lms-learning-management-system' ),
			)
		);
		$this->add_control(
			'pagination_pages_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'masterstudy-lms-learning-management-system' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item span.current' => 'color: {{VALUE}}',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pagination_pages_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .masterstudy-post-template__pagination_list_item a:hover, {{WRAPPER}} .masterstudy-post-template__pagination_list_item span.current',
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_pages_border_hover',
				'selector' => '{{WRAPPER}} .masterstudy-post-template__pagination_list_item a:hover, {{WRAPPER}} .masterstudy-post-template__pagination_list_item span.current',
			)
		);
		$this->add_control(
			'pagination_pages_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'masterstudy-lms-learning-management-system' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .masterstudy-post-template__pagination_list_item span.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	/** Render the widget output on the frontend */
	protected function render() {
		$settings = $this->get_settings_for_display();

		/* ajax turn off for editor mode */
		if ( ! Plugin::$instance->editor->is_edit_mode() ) {
			wp_enqueue_script( 'ms_lms_blog', STM_LMS_URL . 'assets/js/elementor-widgets/blog/blog.js', array(), STM_LMS_VERSION, true );
			wp_localize_script(
				'ms_lms_blog',
				'ms_lms_blog',
				array(
					'nonce'    => wp_create_nonce( 'blog' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		extract( $settings );

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$posts = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => $blog_per_page,
				'paged'          => $paged,
			)
		);
		if ( $posts->have_posts() ) : ?>
		<div class="masterstudy-post-template post-layout-<?php echo esc_attr( $blog_style ); ?> desktop-columns-<?php echo esc_attr( $blog_columns ); ?> tablet-columns-<?php echo esc_attr( $blog_columns_tablet ); ?> mobile-columns-<?php echo esc_attr( $blog_columns_mobile ); ?>">
			<div class="masterstudy-post-template__wrap">
			<?php
			while ( $posts->have_posts() ) {
				$posts->the_post();
				\STM_LMS_Templates::show_lms_template( 'elementor-widgets/blog/styles/' . $blog_style, array() );
			}
			?>
			</div>
			<?php
			if ( $blog_pagination && $posts->max_num_pages > 1 ) {
				?>
				<nav class="masterstudy-post-template__pagination">
					<?php
					\STM_LMS_Templates::show_lms_template(
						'elementor-widgets/blog/pagination',
						array(
							'pagination_data' => array(
								'current_page'   => $paged,
								'total_pages'    => $posts->max_num_pages,
								'total_posts'    => $posts->found_posts,
								'posts_per_page' => $blog_per_page,
								'offset'         => ( $paged - 1 ) * $blog_per_page,
							),
						)
					);
					?>
				</nav>
				<?php
			}
				wp_reset_postdata();
			?>
		</div>
			<?php
		endif;
	}
}
