<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

use Elementor\Controls_Manager;

class Elementor_STM_Pie_Chart extends \Elementor\Widget_Base {

	public function get_name() {
		return 'stm_pie_chart';
	}

	public function get_title() {
		return esc_html__( 'Pie chart', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-pie_chart lms-icon';
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

		$this->add_control(
			'widget_width',
			array(
				'label' => __( 'Width', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'value',
			array(
				'label' => __( 'Value', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'label_value',
			array(
				'label' => __( 'Value Label', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'units',
			array(
				'label' => __( 'Units', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'custom_color',
			array(
				'label'     => __( 'Custom Color', 'masterstudy-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .radial-progress .circle .mask .fill' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			wp_enqueue_style( 'cew_pie_chart', STM_CEW_URL . '/assets/css/pie_chart.css', array(), time() );

			masterstudy_show_template( 'vc_pie_chart', $settings );

		}
	}

	protected function content_template() {

	}

}
