<?php

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Utility\Media;

// Enqueue admin styles and scripts
function masterstudy_lms_ai_enqueue_admin_scripts() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Settings page styles and scripts
	if ( ! empty( $_GET['page'] ) && 'stm-lms-settings' === $_GET['page'] ) {
		wp_enqueue_style( 'masterstudy-lms-ai-settings', STM_LMS_PRO_URL . 'assets/css/ai-lab/settings.css', array(), STM_LMS_PRO_VERSION );
		wp_enqueue_script( 'masterstudy-lms-ai-settings', STM_LMS_PRO_URL . 'assets/js/ai-lab/settings.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	}
}
add_action( 'admin_enqueue_scripts', 'masterstudy_lms_ai_enqueue_admin_scripts' );

// Upload Post Images on Save
function masterstudy_lms_ai_upload_post_images( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || empty( $post->post_content ) ) {
		return;
	}

	$content = $post->post_content;

	if ( preg_match_all( '/<img[^>]*\sdata-prompt=([\'"]).*?\1[^>]*>/i', $content, $matches ) ) {
		foreach ( $matches[0] as $match ) {
			// Get prompt from match
			preg_match( '/data-prompt="([^"]+)"/', $match, $prompt_matches );
			$prompt = isset( $prompt_matches[1] ) ? $prompt_matches[1] : '';

			// Get image src from match
			preg_match( '/src="([^"]+)"/', $match, $image_src_matches );
			$image_src = isset( $image_src_matches[1] ) ? $image_src_matches[1] : '';

			$attachment = Media::create_attachment_from_url( $image_src, substr( $prompt, 0, min( mb_strlen( $prompt ), 20 ) ) );

			if ( isset( $attachment['error'] ) ) {
				continue;
			}

			// Replace the old image with the new one
			$content = str_replace( $match, '<img src="' . $attachment['url'] . '" alt="' . $prompt . '" />', $content );
		}

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $content,
			)
		);
	}
}
add_action( 'masterstudy_lms_save_lesson', 'masterstudy_lms_ai_upload_post_images' );
add_action( 'masterstudy_lms_save_assignment', 'masterstudy_lms_ai_upload_post_images' );

// wp admin head
function masterstudy_lms_ai_admin_head() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['post_type'] ) || PostType::COURSE !== $_GET['post_type'] ) {
		return;
	}

	if ( ! method_exists( \STM_LMS_Instructor::class, 'has_ai_access' ) || ! \STM_LMS_Instructor::has_ai_access( get_current_user_id() ) ) {
		return;
	}

	$url = ms_plugin_user_account_url( 'edit-course' ) . '#ai-open';
	if ( empty( $url ) ) {
		return;
	}
	?>
	<script>
		(function ($) {
			$(document).ready(function ($) {
				$($(".wrap .wp-header-end")[0])
					.before("<a href='<?php echo esc_url( $url ); ?>' class='ai-generate-course-button'><?php esc_html_e( 'Generate Course', 'masterstudy-lms-learning-management-system-pro' ); ?></a>");
			});
		})(jQuery);
	</script>
	<?php
}
add_action( 'admin_head', 'masterstudy_lms_ai_admin_head', );
