<?php
/**
 * Strips third-party content injections (AddToAny share buttons, MasterStudy
 * LMS course-completion notice) from any singular page that hosts the
 * [eies_certificate_verify] shortcode. Without this, the public verification
 * page bleeds AddToAny share rails and a "course not completed" warning from
 * MasterStudy, which is incorrect copy for an anonymous verifier.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Cert_Clean {
	public function __construct() {
		add_action( 'wp', array( $this, 'detect' ) );
	}

	public function detect() {
		if ( ! is_singular( 'page' ) ) return;
		global $post;
		if ( ! $post ) return;

		if ( ! $this->is_cert_page( $post ) ) return;

		// Strip AddToAny share buttons.
		if ( function_exists( 'A2A_SHARE_SAVE_addtoany_content_filter' ) ) {
			remove_filter( 'the_content', 'A2A_SHARE_SAVE_addtoany_content_filter' );
			remove_filter( 'the_excerpt', 'A2A_SHARE_SAVE_addtoany_content_filter' );
		}
		add_filter( 'addtoany_filter_content_visibility', '__return_false' );

		// Replace content with only shortcode output (defeats prepended/appended
		// HTML from MasterStudy LMS templates that hijack certificate pages and
		// guarantees the verify form renders even if post_content is empty).
		add_filter( 'the_content', array( $this, 'force_shortcode_only' ), PHP_INT_MAX );
	}

	private function is_cert_page( $post ) {
		// Signal 1: shortcode literal in content.
		if ( ! empty( $post->post_content ) && false !== strpos( $post->post_content, '[eies_certificate_verify]' ) ) {
			return true;
		}

		// Signal 2: known cert page slugs.
		$slugs = array( 'certificate-page', 'verificar-certificado', 'verify-certificate' );
		if ( in_array( $post->post_name, $slugs, true ) ) {
			return true;
		}

		// Signal 3: MasterStudy LMS cert page setting points at this post.
		$stm = get_option( 'stm_lms_options', array() );
		if ( is_array( $stm ) && ! empty( $stm['certificate_page'] ) && (int) $stm['certificate_page'] === (int) $post->ID ) {
			return true;
		}

		return false;
	}

	public function force_shortcode_only( $content ) {
		if ( ! is_main_query() ) return $content;
		return do_shortcode( '[eies_certificate_verify]' );
	}
}

new EIES_Cert_Clean();
