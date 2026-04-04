<?php

new STM_Elementor_Ajax();

class STM_Elementor_Ajax {

	public static $info_json          = 'https://stylemixthemes.com/masterstudy/template-library/';
	public $post_id                   = 0;
	public $post_status               = '';
	public static $elementor_data_key = '_elementor_data';
	public $placeholder               = array();

	public function __construct() {

		add_action( 'wp_ajax_stm_fetch_templates', array( $this, 'fetch_templates' ) );

		add_action( 'wp_ajax_stm_import_template', array( $this, 'import_template' ) );

	}

	public function fetch_templates() {
		wp_send_json(
			json_decode(
				file_get_contents(
					"{$this::$info_json}info.json",
					false,
					stream_context_create(
						array(
							'ssl' => array(
								'verify_peer'      => false,
								'verify_peer_name' => false,
							),
						)
					)
				),
				true
			)
		);
	}

	public function import_template() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		$this->post_id     = intval( $_GET['post_id'] );
		$this->post_status = get_post_status( $this->post_id );
		$template_id       = intval( $_GET['template_id'] );

		$template = json_decode(
			file_get_contents(
				"{$this::$info_json}templates/elementor-{$template_id}.json",
				false,
				stream_context_create(
					array(
						'ssl' => array(
							'verify_peer'      => false,
							'verify_peer_name' => false,
						),
					)
				)
			),
			true
		);

		if ( empty( $template ) ) {
			die;
		}
		if ( empty( $template['content'] ) ) {
			die;
		}

		$template = $template['content'];

		$current_content = $this->get_json_meta( $this::$elementor_data_key );

		$elementor_data = array_merge( $current_content, $template );

		$this->upload_placeholder();
		$this->placeholder['id']  = $this->get_placeholder();
		$image_url                = wp_get_attachment_image_src( $this->placeholder['id'], 'full' );
		$this->placeholder['url'] = $image_url[0];

		$this->rebuild_elementor_data( $elementor_data );

		wp_send_json( $this->save_elements( $elementor_data ) );

	}

	public function get_json_meta( $key ) {
		$meta = get_post_meta( $this->post_id, $key, true );

		if ( is_string( $meta ) && ! empty( $meta ) ) {
			$meta = json_decode( $meta, true );
		}

		if ( empty( $meta ) ) {
			$meta = array();
		}

		return $meta;
	}

	protected function save_elements( $editor_data ) {

		$json_value      = wp_slash( wp_json_encode( $editor_data ) );
		$is_meta_updated = update_metadata( 'post', $this->post_id, $this::$elementor_data_key, $json_value );

		return $is_meta_updated;
	}

	public static function generate_id() {
		return substr( uniqid(), 6, 7 );
	}

	public function rebuild_elementor_data( &$data_arg ) {

		if ( is_array( $data_arg ) ) {

			foreach ( $data_arg as &$args ) {

				if ( ! empty( $args['id'] ) && ! empty( $args['elType'] ) ) {
					$args['id'] = $this::generate_id();
				}

				if ( ! empty( $args['url'] ) && ! empty( $args['id'] ) ) {
					$args = $this->placeholder;
				}

				$this->rebuild_elementor_data( $args );
			}
		}
	}

	public function upload_placeholder() {

		$placeholder = $this->get_placeholder();
		if ( empty( $placeholder ) ) {

			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$upload_dir       = wp_upload_dir();
			$placeholder_path = STM_ELEMENTOR_TEMPLATE_LIBRARY_PATH . '/assets/images/placeholder.gif';
			$image_data       = $wp_filesystem->get_contents( $placeholder_path );

			$filename = basename( $placeholder_path );

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
			update_post_meta( $attach_id, '_wp_attachment_image_alt', 'ms_elementor_library_placeholder' );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );
		}
	}

	public function get_placeholder() {
		$placeholder_id    = 0;
		$placeholder_array = get_posts(
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => 1,
				'meta_key'       => '_wp_attachment_image_alt',
				'meta_value'     => 'ms_elementor_library_placeholder',
			)
		);
		if ( $placeholder_array ) {
			foreach ( $placeholder_array as $val ) {
				$placeholder_id = $val->ID;
			}
		}
		return $placeholder_id;
	}
}
