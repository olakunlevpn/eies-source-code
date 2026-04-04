<?php
// phpcs:ignoreFile
if ( ! STM_LMS_Options::get_option( 'course_premoderation', false ) ) {
	return;
}

if ( get_option( 'stm_lms_settings', array() )['course_premoderation'] ) {

	add_action( 'init', 'stm_lms_register_rejected_status', 0 );
	function stm_lms_register_rejected_status() {
		register_post_status(
			'rejected',
			array(
				'label'                     => __( 'Rejected', 'masterstudy-lms-learning-management-system-pro' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop(
					'Rejected <span class="count">(%s)</span>',
					'Rejected <span class="count">(%s)</span>',
					'masterstudy-lms-learning-management-system-pro'
				),
			)
		);
	}

	add_filter( 'display_post_states', 'stm_lms_show_rejected_post_state' );
	function stm_lms_show_rejected_post_state( $states ) {
		global $post;
		if ( 'stm-courses' === get_post_type( $post ) && 'rejected' === get_post_status( $post ) ) {
			$states[] = esc_html__( 'Rejected', 'masterstudy-lms-learning-management-system-pro' );
		}

		return $states;
	}

	add_action( 'post_submitbox_misc_actions', 'stm_lms_add_save_as_rejected_button' );
	function stm_lms_add_save_as_rejected_button() {
		global $post;
		if ( 'stm-courses' !== $post->post_type ) {
			return;
		}

		if ( current_user_can( 'edit_post', $post->ID ) && 'rejected' !== get_post_status( $post ) ) {
			echo '<div class="misc-pub-section misc-pub-save-as-rejected">';
			echo '<button type="submit" name="save_rejected" class="button button-secondary">';
			esc_html_e( 'Save as Rejected', 'masterstudy-lms-learning-management-system-pro' );
			echo '</button>';
			echo '</div>';
		}
	}

	add_filter( 'wp_insert_post_data', 'stm_lms_handle_save_as_rejected', 10, 2 );
	function stm_lms_handle_save_as_rejected( $data, $postarr ) {
		if ( 'rejected' === $data['post_status'] && 'stm-courses' === $postarr['post_type'] ) {
			$data['post_status'] = 'rejected';

			add_action(
				'save_post',
				function ( $post_id ) {
					remove_action( 'save_post', __FUNCTION__ );

					if ( get_post_status( $post_id ) === 'rejected' ) {
						do_action( 'stm_lms_course_rejected', $post_id );
					}
				}
			);
		}

		return $data;
	}

	add_action( 'admin_footer-post.php', 'stm_lms_rejected_status_js' );
	add_action( 'admin_footer-post-new.php', 'stm_lms_rejected_status_js' );

	function stm_lms_rejected_status_js() {
		$screen = get_current_screen();
		if ( ! $screen || 'stm-courses' !== $screen->post_type ) {
			return;
		}
		?>
		<script type="text/javascript">
            (function ($) {
                var $inline = $('#post-status-select select[name="post_status"]'),
                    $fallback = $('#post_status'),
                    $status = $inline.length ? $inline : $fallback,
                    $ok = $('.save-post-status'),
                    $btn = $('#publish');

                [$inline, $fallback].forEach(function ($sel) {
                    if ($sel.length && !$sel.find('option[value="rejected"]').length) {
                        $sel.append($('<option>', {
                            value: 'rejected',
                            text: '<?php echo esc_js( __( 'Rejected', 'masterstudy-lms-learning-management-system-pro' ) ); ?>'
                        }));
                    }
                });
                if ($('#hidden_post_status').val() === 'rejected') {
                    $status.val('rejected');
                    $('#post-status-display').text('Rejected');
                }

                if ($inline.length && $ok.length) {
                    $inline.on('change', function () {
                        $ok.trigger('click');
                    });
                }

                function syncBtn() {
                    if ('rejected' === $status.val()) {
                        $btn.val('Publish');
                    }
                }

                $(document).ready(function () {
                    syncBtn();
                    $status.on('change', syncBtn);
                });
            })(jQuery);
		</script>
		<?php
	}

	/**
	 * Include 'rejected' posts in the admin list when viewing "All".
	 */
	add_action(
		'pre_get_posts', function ( WP_Query $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->get( 'post_type' ) !== 'stm-courses' ) {
			return;
		}

		$status = $query->get( 'post_status' );

		if ( empty( $status ) || 'all' === $status ) {
			$default_statuses = array( 'publish', 'future', 'draft', 'pending', 'private' );
			$query->set( 'post_status', array_merge( $default_statuses, array( 'rejected' ) ) );
		}
	}
	);

}
