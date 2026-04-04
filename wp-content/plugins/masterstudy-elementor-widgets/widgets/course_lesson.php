<?php

use Elementor\Controls_Manager;

class Elementor_STM_Course_Lesson extends \Elementor\Widget_Base {


	public function get_name() {
		return 'stm_course_lesson';
	}

	public function get_title() {
		return esc_html__( 'Course Lesson', 'masterstudy-elementor-widgets' );
	}

	public function get_icon() {
		return 'ms-elementor-course_lesson lms-icon';
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
				'label'       => __( 'Lesson Title', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Lesson Title here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'private_lesson',
			array(
				'label' => __( 'Private', 'masterstudy-elementor-widgets' ),
				'type'  => Controls_Manager::SWITCHER,
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
			'content',
			array(
				'label' => __( 'Tab Text', 'masterstudy-elementor-widgets' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'   => __( 'Lesson badge', 'masterstudy-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'no_badge',
				'options' => array_flip(
					array(
						__( 'Choose Badge', 'masterstudy-elementor-widgets' )   => 'no_badge',
						__( 'Test', 'masterstudy-elementor-widgets' )           => 'test',
						__( 'Video', 'masterstudy-elementor-widgets' )      => 'video',
						__( 'Exam', 'masterstudy-elementor-widgets' )           => 'exam',
						__( 'Quiz', 'masterstudy-elementor-widgets' )           => 'quiz',
						__( 'Lecture', 'masterstudy-elementor-widgets' )    => 'lecture',
						__( 'Seminar', 'masterstudy-elementor-widgets' )        => 'seminar',
						__( 'Free', 'masterstudy-elementor-widgets' )           => 'free',
						__( 'Practice', 'masterstudy-elementor-widgets' )   => 'practice',
						__( 'Exercise', 'masterstudy-elementor-widgets' )   => 'exercise',
						__( 'Activity', 'masterstudy-elementor-widgets' )   => 'activity',
					)
				),
			)
		);

		$this->add_control(
			'preview_video',
			array(
				'label'       => __( 'Preview video', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Preview video here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'private_placeholder',
			array(
				'label'       => __( 'Private lesson content placeholder', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Private lesson content placeholder here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'meta',
			array(
				'label'       => __( 'Lesson meta', 'masterstudy-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type Lesson meta here', 'masterstudy-elementor-widgets' ),
			)
		);

		$this->add_control(
			'meta_icon',
			array(
				'label'   => __( 'Icon', 'text-domain' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->add_dimensions( '.masterstudy_elementor_course_lesson_' );

	}

	protected function render() {
		if ( function_exists( 'masterstudy_show_template' ) ) {

			$settings = $this->get_settings_for_display();

			$settings['css_class'] = ' masterstudy_elementor_course_lesson_';

			masterstudy_show_template( 'course_lesson', $settings );

		}
	}

	protected function content_template() {
	}

}
