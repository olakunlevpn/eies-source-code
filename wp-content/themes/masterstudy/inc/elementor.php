<?php
function masterstudy_locate_template( $template_name ) {
	$slug = basename( sanitize_text_field( $template_name ) );
	if ( '' === $slug ) {
		return '';
	}

	$dirs = array(
		trailingslashit( get_stylesheet_directory() ) . 'partials/vc_templates/',
		trailingslashit( get_template_directory() ) . 'partials/vc_templates/',
	);

	foreach ( $dirs as $dir ) {
		$file      = $dir . $slug . '.php';
		$real_file = realpath( $file );
		$real_dir  = realpath( $dir );

		if ( $real_file && $real_dir && strpos( $real_file, $real_dir ) === 0 && is_readable( $real_file ) ) {
			return $real_file;
		}
	}

	return '';
}

function masterstudy_load_template( $template_name, $vars = array() ) {
	ob_start();
	extract( $vars ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	$template = masterstudy_locate_template( $template_name );
	if ( empty( $template ) ) {
		return false;
	}
	include $template;
	return apply_filters( 'masterstudy_template_' . $template_name, ob_get_clean(), $vars );
}

function masterstudy_show_template( $template_name, $vars = array() ) {
	 echo masterstudy_load_template( $template_name, $vars ); // phpcs:ignore
}

add_action(
	'admin_init',
	function () {
		delete_transient( 'elementor_activation_redirect' );
	}
);

function masterstudy_filtered_output( $output ) {
	return apply_filters( 'masterstudy_filtered_output', $output );
}
