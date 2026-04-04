<?php

new STM_LMS_Courses_Pro();

class STM_LMS_Courses_Pro {

	public function __construct() {
		add_action( 'stm-lms-content-stm-courses', array( self::class, 'single_course' ), 5 );
	}

	public static function single_course() {
		$course_id     = get_the_ID();
		$course_option = get_post_meta( $course_id, 'page_style', true );
		$style         = ! empty( $course_option ) ? $course_option : ( function_exists( 'get_course_style_from_categories' ) ? get_course_style_from_categories( $course_id ) : '' );

		if ( isset( $_GET['course_style'] ) ) {
			$style = sanitize_text_field( wp_unslash( $_GET['course_style'] ) );
		}

		if ( empty( $style ) ) {
			$style = STM_LMS_Options::get_option( 'course_style', 'default' );
		}

		$elementor_templates = function_exists( 'masterstudy_lms_get_my_templates' )
			? masterstudy_lms_get_my_templates( false )
			: array();

		$native_templates = function_exists( 'masterstudy_lms_get_native_templates' )
			? masterstudy_lms_get_native_templates()
			: array();

		$matched_elementor = array();

		if ( is_array( $elementor_templates ) ) {
			$matched_elementor = array_values(
				array_filter(
					$elementor_templates,
					function( $existing_style ) use ( $style ) {
						return isset( $existing_style['name'] ) && $existing_style['name'] === $style;
					}
				)
			);
		}

		if ( ! empty( $matched_elementor ) && isset( $matched_elementor[0]['id'] ) && class_exists( '\Elementor\Plugin' ) ) {
			global $masterstudy_single_page_course_id;
			$masterstudy_single_page_course_id = get_the_ID();

			remove_all_actions( 'stm-lms-content-stm-courses' );
			wp_enqueue_style( 'masterstudy-container-reset', STM_LMS_PRO_URL . 'assets/css/container-reset.css', array(), STM_LMS_PRO_VERSION );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $matched_elementor[0]['id'] );
		} else {
			if ( 'default' !== $style ) {
				remove_all_actions( 'stm-lms-content-stm-courses' );

				$matched_native = array_filter(
					$native_templates,
					function( $existing_style ) use ( $style ) {
						return isset( $existing_style['name'] ) && $existing_style['name'] === $style;
					}
				);

				if ( ! empty( $matched_native ) ) {
					STM_LMS_Templates::show_lms_template( 'course/' . $style );
				} else {
					STM_LMS_Templates::show_lms_template( 'course' );
				}
			}
		}
	}
}
