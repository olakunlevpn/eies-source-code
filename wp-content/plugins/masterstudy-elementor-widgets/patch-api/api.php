<?php

new Cew_Patch_API();

class Cew_Patch_API {

	public function __construct() {
		add_action( 'wp_ajax_cew_get_post_types', array( $this, 'get_post_types_list' ) );
		add_action( 'wp_ajax_nopriv_cew_get_post_types', array( $this, 'get_post_types_list' ) );

		add_action( 'wp_ajax_cew_retrieve_post_to_patch', array( $this, 'retrieve_post' ) );
		add_action( 'wp_ajax_nopriv_cew_retrieve_post_to_patch', array( $this, 'retrieve_post' ) );

		add_action( 'wp_ajax_cew_patch_post', array( $this, 'patch_post' ) );
		add_action( 'wp_ajax_nopriv_cew_patch_post', array( $this, 'patch_post' ) );
	}

	public function get_post_types_list() {
		$r = get_post_types(
			array(
				'public' => true,
			)
		);

		foreach ( $r as &$data ) {
			$labels = get_post_type_object( $data );
			$label  = $labels->label;

			if ( 'Elementor - Header Footer & Blocks Template' === $label ) {
				$label = 'Header Footer & Blocks';
			}
			$data = $label;
		}

		wp_send_json( $r );
	}

	public function retrieve_post() {
		if ( empty( $_GET['post_types'] ) ) {
			wp_send_json(
				array(
					'error'   => true,
					'message' => esc_html__( 'Please, select Post type', 'masterstudy-elementor-widgets' ),
				)
			);
		}

		$post_types = explode( ',', sanitize_text_field( $_GET['post_types'] ) );

		$elementor_cpt_support = get_option( 'elementor_cpt_support', array() );
		$elementor_cpt_support = array_merge( $post_types, $elementor_cpt_support );

		/*Update Options Elementor*/
		update_option( 'elementor_cpt_support', array_unique( $elementor_cpt_support ) );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
		update_option( 'elementor_load_fa4_shim', 'yes' );
		update_option( 'elementor_container_width', 1200 );
		update_option( 'elementor_space_between_widgets', 0 );

		$args = array(
			'post_type'      => $post_types,
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'cew_patched',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'cew_patched',
					'value'   => STM_PATCH_VAR,
					'compare' => '!=',
				),
			),
		);

		$q = new WP_Query( $args );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();

				$post = array(
					'id'    => get_the_ID(),
					'title' => get_the_title(),
				);

			}

			wp_reset_postdata();
		}

		Elementor\Plugin::$instance->files_manager->clear_cache();

		if ( empty( $post ) ) {
			wp_send_json(
				array(
					'error'   => true,
					'message' => esc_html__( 'Seems like, all posts were patched!', 'masterstudy-elementor-widgets' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'error' => false,
					'post'  => $post,
				)
			);
		}

	}

	public function patch_post() {
		check_ajax_referer( 'stm_lms_wpb_patch', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		if ( empty( $_GET['post_id'] ) ) {
			wp_send_json(
				array(
					'error'   => true,
					'message' => __( 'Post id is undefined. Stopping.', 'masterstudy-elementor-widgets' ),
				)
			);
		}

		$post_id = intval( $_GET['post_id'] );

		$vc = new CEW_Patch( $post_id, $post_id );

		update_post_meta( $post_id, 'cew_patched', STM_PATCH_VAR );

		wp_send_json(
			array(
				'error'   => false,
				'message' => sprintf( __( 'Post "%s" patched', 'masterstudy-elementor-widgets' ), get_the_title( $post_id ) ),
				'r'       => $vc,
			)
		);
	}

}
