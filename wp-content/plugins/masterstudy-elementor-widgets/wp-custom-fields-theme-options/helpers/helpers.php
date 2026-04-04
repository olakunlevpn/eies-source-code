<?php

function stm_wpcfto_filtered_output( $data ) {
	return apply_filters( 'stm_wpcfto_filter_output', $data );
}

function stm_wpcfto_is_pro() {
	return defined( 'STM_LMS_PRO_PATH' );
}

function stm_wpcfto_wp_head() {     ?>
	<script type="text/javascript">
		var stm_wpcfto_ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
	</script>

	<style>
		.vue_is_disabled {
			display: none;
		}
	</style>
	<?php
}

add_action( 'wp_head', 'stm_wpcfto_wp_head' );
add_action( 'admin_head', 'stm_wpcfto_wp_head' );

function stm_wpcfto_nonces() {
	$nonces = array(
		'load_modal',
		'load_content',
		'get_image_url',
		'start_quiz',
		'user_answers',
		'get_order_info',
		'user_orders',
		'stm_lms_get_instructor_courses',
		'stm_lms_add_comment',
		'stm_lms_get_comments',
		'stm_lms_login',
		'stm_lms_register',
		'stm_lms_become_instructor',
		'stm_lms_enterprise',
		'stm_lms_get_user_courses',
		'stm_lms_get_user_quizzes',
		'stm_lms_wishlist',
		'stm_lms_save_user_info',
		'stm_lms_lost_password',
		'stm_lms_change_avatar',
		'stm_lms_delete_avatar',
		'stm_lms_complete_lesson',
		'stm_lms_use_membership',
		'stm_lms_change_featured',
		'stm_lms_delete_course_subscription',
		'stm_lms_get_reviews',
		'stm_lms_add_review',
		'stm_lms_add_to_cart',
		'stm_lms_delete_from_cart',
		'stm_lms_purchase',
		'stm_lms_send_message',
		'stm_lms_get_user_conversations',
		'stm_lms_get_user_messages',
		'stm_curriculum',
		'stm_manage_posts',
		'stm_lms_change_post_status',
		'stm_curriculum_create_item',
		'stm_curriculum_get_item',
		'stm_save_questions',
		'stm_save_title',
		'stm_save_settings',
		'stm_lms_tables_update',
		'stm_lms_get_enterprise_groups',
		'stm_lms_get_enterprise_group',
		'stm_lms_add_enterprise_group',
		'stm_lms_delete_enterprise_group',
		'stm_lms_add_to_cart_enterprise',
		'stm_lms_get_user_ent_courses',
		'stm_lms_delete_user_ent_courses',
		'stm_lms_add_user_ent_courses',
		'stm_lms_change_ent_group_admin',
		'stm_lms_delete_user_from_group',
		'stm_lms_import_groups',
		'stm_lms_upload_file_assignment',
		'stm_lms_delete_assignment_file',
		'stm_lms_save_draft_content',
		'stm_lms_accept_draft_assignment',
		'stm_lms_get_assignment_data',
		'stm_lms_get_instructor_assingments',
		'stm_lms_get_user_assingments',
		'stm_lms_edit_user_answer',
		'stm_lms_get_user_points_history',
		'stm_lms_buy_for_points',
		'stm_lms_get_point_users',
		'stm_lms_get_user_points_history_admin',
		'stm_lms_change_points',
		'stm_lms_delete_points',
		'stm_lms_get_user_bundles',
		'stm_lms_change_bundle_status',
		'stm_lms_delete_bundle',
		'stm_lms_get_co_courses',
		'stm_lms_check_certificate_code',
		'wpcfto_upload_file',
		'stm_lms_wpb_patch',
	);

	$nonces_list = array();

	foreach ( $nonces as $nonce_name ) {
		$nonces_list[ $nonce_name ] = wp_create_nonce( $nonce_name );
	}

	?>
	<script>
		var stm_wpcfto_nonces = <?php echo json_encode( $nonces_list ); ?>;
	</script>
	<?php
}

add_action( 'admin_head', 'stm_wpcfto_nonces' );
add_action( 'wp_head', 'stm_wpcfto_nonces' );

add_action(
	'wp_ajax_stm_wpcfto_get_settings',
	function () {

		$source = sanitize_text_field( $_GET['source'] );
		$name   = sanitize_text_field( $_GET['name'] );

		if ( 'settings' === $source ) {

			$theme_options_page = apply_filters( 'wpcfto_options_page_setup', array() );
			$settings_data      = get_option( $name, array() );
			$settings           = array();
			/*Get Our settings*/
			foreach ( $theme_options_page as $option_page ) {
				if ( $option_page['option_name'] !== $name ) {
					continue;
				}

				$settings = $option_page['fields'];
			}

			foreach ( $settings as $section_name => $section ) {
				foreach ( $section['fields'] as $field_name => $field ) {
					$default_value = ( ! empty( $field['value'] ) ) ? $field['value'] : '';
					$settings[ $section_name ]['fields'][ $field_name ]['value'] = ( isset( $settings_data[ $field_name ] ) ) ? $settings_data[ $field_name ] : $default_value;
				}
			}

			wp_send_json( $settings );

		} else {
			$post_id = intval( $source );

			$meta = STM_Metaboxes::convert_meta( $post_id );

			$fields_data = apply_filters( 'stm_wpcfto_fields', array() );
			$sections    = $fields_data[ $name ];

			foreach ( $sections as $section_name => $section ) {
				foreach ( $section['fields'] as $field_name => $field ) {
					$default_value = ( ! empty( $field['value'] ) ) ? $field['value'] : '';
					$value         = ( isset( $meta[ $field_name ] ) ) ? $meta[ $field_name ] : $default_value;
					if ( isset( $value ) ) {
						switch ( $field['type'] ) {
							case 'dates':
								if ( ! empty( $value ) ) {
									$value = explode( ',', $value );
								}
								break;
							case 'answers':
								if ( ! empty( $value ) ) {
									$value = unserialize( $value );
								}
								break;
							case 'repeater':
								if ( empty( $value ) ) {
									$value = array();
								}

								break;
						}
					}
					$sections[ $section_name ]['fields'][ $field_name ]['value'] = $value;
				}
			}

			wp_send_json( $sections );

		}

	}
);


function stm_wpcfto_get_options( $option_name, $option = '', $default_value = null ) {
	$options = get_option( $option_name, array() );

	if ( empty( $option ) ) {
		return $options;
	}

	return isset( $options[ $option ] ) ? $options[ $option ] : $default_value;

}


add_action( 'wp_ajax_stm_lms_get_image_url', 'wpcfto_get_image_url' );

function wpcfto_get_image_url() {
	check_ajax_referer( 'get_image_url', 'nonce' );

	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}

	if ( empty( $_GET['image_id'] ) ) {
		die;
	}
	wp_send_json( wp_get_attachment_url( intval( $_GET['image_id'] ) ) );
}
