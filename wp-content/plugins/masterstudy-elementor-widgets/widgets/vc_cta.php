<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

class Elementor_VC_CTA extends \Elementor\Widget_Base {

	public function get_name() {
		return 'vc_cta';
	}

	public function get_title() {
		return esc_html__( 'Call to action', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-cta lms-icon';
	}

	public function get_categories() {
		return array( 'stm_lms_theme' );
	}

	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'masterstudy-elementor-widgets' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		/*H2*/
		$this->add_control(
			'h2',
			array(
				'label' => __( 'Heading', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'h2_link',
			array(
				'label' => __( 'URL (Link)', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);

		masterstudy_Elementor_Widgets::add_font_settings( $this, 'h2_font_container', array(), '.ce_cta__content__title' );

		$this->add_control(
			'h2_el_id',
			array(
				'label' => __( 'Element ID', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'h2_el_class',
			array(
				'label' => __( 'Extra class name', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'hr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		/*H4*/
		$this->add_control(
			'h4',
			array(
				'label' => __( 'Subheading', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'h4_link',
			array(
				'label' => __( 'URL (Link)', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);

		masterstudy_Elementor_Widgets::add_font_settings( $this, 'h4_font_container', array( 'tag' => 'h4' ), '.ce_cta__content__subtitle' );

		$this->add_control(
			'h4_el_id',
			array(
				'label' => __( 'Element ID', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'h4_el_class',
			array(
				'label' => __( 'Extra class name', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'hr_4',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'txt_align',
			array(
				'label'   => __( 'Text alignment', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array_flip(
					array(
						'Left'    => 'left',
						'Right'   => 'right',
						'Center'  => 'center',
						'Justify' => 'justify',
					)
				),
			)
		);

		$this->add_control(
			'custom_text',
			array(
				'label'     => __( 'Text color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__content__text' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'content',
			array(
				'label' => __( 'Text', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
				'rows'  => 5,
			)
		);

		$this->add_control(
			'el_id',
			array(
				'label' => __( 'Element ID', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'el_class',
			array(
				'label' => __( 'Extra class name', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'particles',
			array(
				'label'        => __( 'Enable Particles', 'masterstudy-elementor-widgets' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		/*Button Section*/

		$this->start_controls_section(
			'button_section',
			array(
				'label' => __( 'Button', 'masterstudy-elementor-widgets' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'add_button',
			array(
				'label'   => __( 'Add button', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array_flip(
					array(
						esc_html__( 'No', 'masterstudy-elementor-widgets' )  => '',
						esc_html__( 'Top', 'masterstudy-elementor-widgets' ) => 'top',
						esc_html__( 'Bottom', 'masterstudy-elementor-widgets' ) => 'bottom',
						esc_html__( 'Left', 'masterstudy-elementor-widgets' ) => 'left',
						esc_html__( 'Right', 'masterstudy-elementor-widgets' ) => 'right',
					)
				),
			)
		);

		$this->add_control(
			'btn_title',
			array(
				'label'     => __( 'Text', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'btn_link',
			array(
				'label'     => __( 'URL (Link)', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'btn_custom_background',
			array(
				'label'     => __( 'Background Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'btn_custom_background_hover',
			array(
				'label'     => __( 'Background Color on hover', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'brdr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'btn_custom_text',
			array(
				'label'     => __( 'Text', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'btn_custom_text_hover',
			array(
				'label'     => __( 'Text Color on hover', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'txt',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'btn_custom_border',
			array(
				'label'     => __( 'Border Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'btn_custom_border_hover',
			array(
				'label'     => __( 'Border Color on hover', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'btn_align',
			array(
				'label'     => __( 'Alignment', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array_flip(
					array(
						'Inline' => 'inline',
						'Left'   => 'left',
						'Right'  => 'right',
						'Center' => 'center',
					)
				),
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'btn_button_block',
			array(
				'label'        => __( 'Set full width button?', 'masterstudy-elementor-widgets' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'btn_i_icon',
			array(
				'label'     => __( 'Icon', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
				'default'   => array(
					'value'   => 'fa fa-chevron-right',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'btn_i_color',
			array(
				'label'     => __( 'Icon Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button i' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'btn_i_color_hover',
			array(
				'label'     => __( 'Icon Color on hover', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ce_cta__action .button:hover i' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'btn_i_align',
			array(
				'label'     => __( 'Icon Alignment', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => array_flip(
					array(
						'Left'  => 'left',
						'Right' => 'right',
					)
				),
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		/*57*/
		$this->add_control(
			'btn_el_id',
			array(
				'label'     => __( 'Element ID', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		$this->add_control(
			'btn_el_class',
			array(
				'label'     => __( 'Extra class name', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => array(
					'add_button' => array( 'top', 'bottom', 'left', 'right' ),
				),
			)
		);

		$this->end_controls_section();

		/*COMPOSER SETTINGS*/

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$title_font    = masterstudy_Elementor_Widgets::get_font_settings( $settings, 'h2_font_container' );
			$subtitle_font = masterstudy_Elementor_Widgets::get_font_settings( $settings, 'h4_font_container' );

			$settings['title_font']    = masterstudy_Elementor_Widgets::build_font_styles( $title_font );
			$settings['subtitle_font'] = masterstudy_Elementor_Widgets::build_font_styles( $subtitle_font );

			masterstudy_show_template( 'vc_cta', $settings );

		}
	}

	protected function content_template() {

	}

}
