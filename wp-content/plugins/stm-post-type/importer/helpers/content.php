<?php
function stm_theme_import_content( $layout, $builder = 'js_composer' ) {
	set_time_limit( 0 );

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', true );
	}

	require_once STM_POST_TYPE_PATH . '/importer/wordpress-importer/class-stm-wp-import.php';

	$wp_import                    = new STM_WP_Import();
	$wp_import->theme             = 'masterstudy';
	$wp_import->layout            = $layout;
	$wp_import->builder           = $builder;
	$wp_import->fetch_attachments = true;

	if ( 'elementor' === $builder ) {
		if ( defined( 'STM_DEV_MODE' ) ) {
			$ready = STM_POST_TYPE_PATH . '/importer/demos/elementor/elementor-' . $layout . '.xml';
		} else {
			$ready = prepare_demo( $builder . '-' . $layout );
		}
	} else {
		if ( defined( 'STM_DEV_MODE' ) ) {
			$ready = STM_POST_TYPE_PATH . '/importer/demos/' . $layout . '/xml/demo.xml';
		} else {
			$ready = prepare_demo( $layout );
		}
	}

	if ( is_wp_error( $ready ) ) {
		return $ready;
	}

	if ( $ready ) {
		ob_start();
		$wp_import->import( $ready );
		ob_end_clean();
	}

	return true;
}


function prepare_demo( $layout ) {

	if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	}

	$upgrader = new WP_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->run(
		array(
			'package'                     => "downloads://masterstudy/demos/{$layout}.zip",
			'destination'                 => apply_filters( 'stm_get_temp_path', '' ),
			'clear_destination'           => false,
			'abort_if_destination_exists' => false,
			'clear_working'               => true,
		)
	);

	if ( false === $result ) {
		$result = new WP_Error( '', 'WP_Upgrader returned "false" when downloading demo ZIP.' );
	}

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return $result['destination'] . "{$layout}.xml";
}

if ( ! function_exists( 'stm_get_temp_path' ) ) {
	function stm_get_temp_path() {
		$upload_dir = wp_upload_dir();
		$dir        = $upload_dir['basedir'] . '/tmp-stm/';
		if ( ! is_dir( $dir ) ) {
			mkdir( $dir, 0777, true );
		}
		return $dir;
	}
	add_filter( 'stm_get_temp_path', 'stm_get_temp_path' );
}

function stm_import_products_content( $layout, $builder = 'js_composer' ) {
	set_time_limit( 0 );

	$exclude_layouts = stm_import_products_exclude_layouts();

	if ( in_array( $layout, $exclude_layouts, true ) ) {
		return;
	}

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', true );
	}

	require_once STM_POST_TYPE_PATH . '/importer/wordpress-importer/class-stm-wp-import.php';

	$wp_import                    = new STM_WP_Import();
	$wp_import->fetch_attachments = true;

	$ready = STM_POST_TYPE_PATH . '/importer/demos/products/' . $builder . '/demo.xml';

	if ( $ready ) {
		ob_start();
		$wp_import->import( $ready, $layout );
		ob_end_clean();
	}

	return true;
}


add_action( 'stm_masterstudy_importer_done', 'stm_masterstudy_importer_done' );
function stm_masterstudy_importer_done( $layout ) {
	$exclude_layouts = stm_import_products_exclude_layouts();

	if ( in_array( $layout, $exclude_layouts, true ) ) {
		return;
	}

	$options                = get_option( 'stm_option', array() );
	$options['enable_shop'] = '1';
	update_option( 'stm_option', $options );
}


function consulting_upload_placeholder() {

	$placeholder = consulting_importer_get_placeholder();
	if ( empty( $placeholder ) ) {

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$image_url = 'https://stylemixthemes.com/masterstudy/demo/wp-content/uploads/2015/07/placeholder.gif';

		$upload_dir = wp_upload_dir();

		$placeholder_path = STM_POST_TYPE_PATH . '/assets/images/placeholder.gif';
		$image_data       = $wp_filesystem->get_contents( $placeholder_path );

		$filename = basename( $image_url );

		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		$wp_filesystem->put_contents( $file, $image_data, FS_CHMOD_FILE );

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file );
		update_post_meta( $attach_id, '_wp_attachment_image_alt', 'masterstudy_placeholder' );

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
	}
}

function consulting_importer_get_placeholder() {
	$placeholder_id    = 0;
	$placeholder_array = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_attachment_image_alt',
			'meta_value'     => 'masterstudy_placeholder',
		)
	);
	if ( $placeholder_array ) {
		foreach ( $placeholder_array as $val ) {
			$placeholder_id = $val->ID;
		}
	}

	return $placeholder_id;
}

function consulting_import_rebuilder_elementor_data( &$data ) {

	if ( ! empty( $data ) ) {
		$data = maybe_unserialize( $data );
		if ( ! is_array( $data ) ) {
			if ( consulting_import_is_elementor_data_unslash_required() ) {
				$data = wp_unslash( $data );
			}
			$data = json_decode( $data, true );
		}
		consulting_import_rebuilder_elementor_data_walk( $data );
		$data = wp_slash( wp_json_encode( $data ) );
	}

}

function consulting_import_is_elementor_data_unslash_required() {
	// No elementor plugin is active - so no unslash is required
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return false;
	}

	// before version 2.9.10 it was required
	if ( version_compare( ELEMENTOR_VERSION, '2.9.10', '<' ) ) {
		return true;
	}

	// otherwise not required
	return false;
}

function consulting_import_rebuilder_elementor_data_walk( &$data_arg ) {

	if ( is_array( $data_arg ) ) {

		foreach ( $data_arg as &$args ) {

			if ( ! empty( $args['url'] ) && empty( $args['id'] ) ) {
				$localhost   = 'http://lmsdemo.loc';
				$host        = get_bloginfo( 'url' );
				$args['url'] = str_replace( $localhost, $host, $args['url'] );
			}

			consulting_import_rebuilder_elementor_data_walk( $args );
		}
	}
}

add_action( 'stm_wp_import_after_insert_attachment', 'lms_pt_wp_import_after_insert_attachment_action', 100, 2 );

function lms_pt_wp_import_after_insert_attachment_action( $post_id, $builder ) {
	if ( 'elementor' === $builder ) {
		update_post_meta( $post_id, '_wp_attachment_image_alt', 'masterstudy_placeholder' );
	}
}
