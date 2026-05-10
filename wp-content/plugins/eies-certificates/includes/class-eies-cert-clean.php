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
		if ( ! $post || empty( $post->post_content ) ) return;
		if ( false === strpos( $post->post_content, '[eies_certificate_verify]' ) ) return;

		// Strip AddToAny share buttons.
		if ( function_exists( 'A2A_SHARE_SAVE_addtoany_content_filter' ) ) {
			remove_filter( 'the_content', 'A2A_SHARE_SAVE_addtoany_content_filter' );
			remove_filter( 'the_excerpt', 'A2A_SHARE_SAVE_addtoany_content_filter' );
		}
		add_filter( 'addtoany_filter_content_visibility', '__return_false' );

		// Replace content with only shortcode output (defeats prepended/appended
		// HTML from MasterStudy LMS templates that hijack certificate pages).
		add_filter( 'the_content', array( $this, 'force_shortcode_only' ), PHP_INT_MAX );
	}

	public function force_shortcode_only( $content ) {
		if ( ! is_main_query() ) return $content;
		return do_shortcode( '[eies_certificate_verify]' );
	}
}

new EIES_Cert_Clean();
