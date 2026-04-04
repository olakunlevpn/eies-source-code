<?php

use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository;
use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleSettings;
use MasterStudy\Lms\Pro\addons\CourseBundle\Utility\CourseBundleCheckout;

add_action( 'wp_ajax_stm_lms_save_bundle', array( CourseBundleRepository::class, 'save_bundle' ) );

function masterstudy_lms_ajax_delete_bundle() {
	do_action( 'stm_lms_delete_bundle' );

	check_ajax_referer( 'stm_lms_delete_bundle', 'nonce' );

	$bundle_id = intval( $_GET['bundle_id'] );

	if ( ! CourseBundleRepository::check_bundle_author( $bundle_id, get_current_user_id() ) ) {
		die;
	}

	wp_delete_post( $bundle_id, true );

	wp_send_json( 'OK' );
}
add_action( 'wp_ajax_stm_lms_delete_bundle', 'masterstudy_lms_ajax_delete_bundle' );

function masterstudy_lms_ajax_change_bundle_status() {
	do_action( 'stm_lms_change_bundle_status' );

	check_ajax_referer( 'stm_lms_change_bundle_status', 'nonce' );

	$bundle_id = intval( $_GET['bundle_id'] );

	if ( ! CourseBundleRepository::check_bundle_author( $bundle_id, get_current_user_id() ) ) {
		die;
	}

	$bundle_status = get_post_status( $bundle_id );
	$post_status   = 'draft';
	$quota         = floatval( ( new CourseBundleSettings() )->get_bundles_limit() ) - floatval( CourseBundleRepository::count() );

	if ( 'draft' === $bundle_status && $quota ) {
		$post_status = 'publish';
	}

	if ( 'draft' === $bundle_status && ! $quota ) {
		wp_send_json( esc_html__( 'Quota exceeded', 'masterstudy-lms-learning-management-system-pro' ) );
	}

	wp_update_post(
		array(
			'ID'          => $bundle_id,
			'post_status' => $post_status,
		)
	);

	wp_send_json( 'OK' );
}
add_action( 'wp_ajax_stm_lms_change_bundle_status', 'masterstudy_lms_ajax_change_bundle_status' );

function masterstudy_lms_ajax_get_user_bundles() {
	check_ajax_referer( 'stm_lms_get_user_bundles', 'nonce' );

	$page     = isset( $_GET['page'] ) ? max( 1, (int) $_GET['page'] ) : 1;
	$per_page = isset( $_GET['per_page'] ) ? max( 1, (int) $_GET['per_page'] ) : 6;

	$repo = new \MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleRepository();

	$data = $repo->get_bundles(
		array(
			'page'           => $page,
			'posts_per_page' => $per_page,
		)
	);

	$render = isset( $_GET['render'] ) ? sanitize_text_field( wp_unslash( $_GET['render'] ) ) : '';

	if ( 'html' === $render ) {
		$bundles_list = ! empty( $data['posts'] ) ? $data['posts'] : array();
		$courses      = ! empty( $data['courses'] ) ? $data['courses'] : array();
		$total_pages  = ! empty( $data['pages'] ) ? (int) $data['pages'] : 1;

		ob_start();
		if ( ! empty( $bundles_list ) ) {
			foreach ( $bundles_list as $bundle ) {
				STM_LMS_Templates::show_lms_template(
					'bundle/card/main',
					array(
						'bundle'  => $bundle,
						'courses' => $courses,
					)
				);
			}
		}
		$html = ob_get_clean();

		ob_start();
		if ( ! empty( $bundles_list ) && $total_pages > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $total_pages,
					'current_page'      => $page,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
					'thin'              => true,
				)
			);
		}
		$pagination = ob_get_clean();

		wp_send_json(
			array(
				'html'        => $html,
				'pagination'  => $pagination,
				'page'        => $page,
				'pages'       => $total_pages,
				'total_pages' => $total_pages,
			)
		);
	}

	wp_send_json( $data );
}
add_action( 'wp_ajax_stm_lms_get_user_bundles', 'masterstudy_lms_ajax_get_user_bundles' );

function ajax_add_to_cart_bundle() {
	check_ajax_referer( 'stm_lms_add_bundle_to_cart', 'nonce' );

	wp_send_json( CourseBundleCheckout::add_to_cart( intval( $_GET['item_id'] ), get_current_user_id() ) );
}
add_action( 'wp_ajax_stm_lms_add_bundle_to_cart', 'ajax_add_to_cart_bundle' );
