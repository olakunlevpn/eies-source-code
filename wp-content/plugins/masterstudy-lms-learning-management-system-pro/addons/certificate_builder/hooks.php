<?php

use MasterStudy\Lms\Pro\addons\certificate_builder\DemoImporter;
use MasterStudy\Lms\Pro\addons\certificate_builder\Http\Controllers\AdminPageController;
use MasterStudy\Lms\Pro\addons\certificate_builder\Http\Controllers\AjaxController;

add_action(
	'init',
	function () {
		$args = array(
			'labels'              => array(
				'name'          => esc_html__( 'Certificates', 'masterstudy-lms-learning-management-system-pro' ),
				'singular_name' => esc_html__( 'Certificate', 'masterstudy-lms-learning-management-system-pro' ),
				'add_new'       => esc_html__( 'Add New', 'masterstudy-lms-learning-management-system-pro' ),
				'add_new_item'  => esc_html__( 'Add New', 'masterstudy-lms-learning-management-system-pro' ),
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'capability_type'     => 'stm_lms_post',
			'capabilities'        => array(
				'publish_posts'       => 'publish_stm_lms_posts',
				'edit_posts'          => 'edit_stm_lms_posts',
				'delete_posts'        => 'delete_stm_lms_posts',
				'edit_post'           => 'edit_stm_lms_post',
				'delete_post'         => 'delete_stm_lms_post',
				'read_post'           => 'read_stm_lms_posts',
				'edit_others_posts'   => 'edit_others_stm_lms_posts',
				'delete_others_posts' => 'delete_others_stm_lms_posts',
				'read_private_posts'  => 'read_private_stm_lms_posts',
			),
			'supports'            => array( 'title', 'thumbnail' ),
			'menu_position'       => 20,
			'menu_icon'           => 'stmlms-menu-my-certificates',
		);
		register_post_type( 'stm-certificates', $args );
		wp_register_script( 'jspdf', STM_LMS_PRO_URL . 'assets/js/certificate-builder/jspdf.umd.js', array(), STM_LMS_PRO_VERSION, true );
		wp_register_script( 'pdfjs', STM_LMS_URL . '/assets/vendors/pdf.min.js', array(), MS_LMS_VERSION, true );
		wp_register_script( 'pdfjs_worker', STM_LMS_URL . '/assets/vendors/pdf.worker.min.js', array(), MS_LMS_VERSION, true );
		wp_register_script( 'qrcode', STM_LMS_PRO_URL . 'assets/js/certificate-builder/qrcode.min.js', array(), STM_LMS_PRO_VERSION, false );
		wp_register_script( 'html2canvas', STM_LMS_PRO_URL . 'assets/js/certificate-builder/html2canvas.min.js', array(), STM_LMS_PRO_VERSION, true );
		wp_register_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js', array(), STM_LMS_PRO_VERSION, true );
		wp_register_script( 'webfontloader', 'https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js', array(), STM_LMS_PRO_VERSION, true );
		wp_register_script( 'select-google-font', STM_LMS_PRO_URL . 'assets/js/certificate-builder/select-google-font.min.js', array(), STM_LMS_PRO_VERSION, true );

		wp_register_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css', array(), STM_LMS_PRO_VERSION, null );
		wp_register_style( 'select2-bootstrap', 'https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css', array(), STM_LMS_PRO_VERSION, null );
		wp_register_style( 'masterstudy-grades-certificate', STM_LMS_PRO_URL . 'assets/css/certificate-builder/grades.css', array(), STM_LMS_PRO_VERSION, null );
	}
);

add_action(
	'admin_init',
	function () {
		// TODO: consider add this to addon activation hook
		( new DemoImporter() )->import();

		// TODO: remove after next major release (v4.5.0)
		$is_imported = get_option( 'stm_lms_new_certificates_imported', '' );
		if ( empty( $is_imported ) ) {
			update_option( 'stm_lms_new_certificates_imported', '1' );

			( new DemoImporter() )->create_demo_certificates( array( 'demo-1', 'demo-2' ) );
		}
	}
);

add_action(
	'admin_menu',
	function () {
		$instructor_capability = STM_LMS_Options::get_option( 'instructors_certificates', false );
		$is_admin              = current_user_can( 'administrator' );

		if ( ! $instructor_capability && ! $is_admin ) {
			remove_menu_page( 'edit.php?post_type=stm-certificates' );
		} else {
			add_submenu_page(
				'',
				esc_html__(
					'Certificate Builder',
					'masterstudy-lms-learning-management-system-pro'
				),
				'',
				'edit_posts',
				'certificate_builder',
				new AdminPageController()
			);
		}
	}
);

add_action(
	'load-post-new.php',
	function () {
		if ( isset( $_GET['post_type'] ) && 'stm-certificates' === $_GET['post_type'] ) {
			wp_safe_redirect( admin_url( 'admin.php?page=certificate_builder' ) );
			exit;
		}
	}
);

add_action(
	'load-post.php',
	function () {
		$post_id = sanitize_text_field( wp_unslash( $_GET['post'] ?? '' ) );
		$action  = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );

		if ( 'edit' === $action ) {
			$post = get_post( $post_id );

			if ( $post instanceof WP_Post && 'stm-certificates' === $post->post_type ) {
				wp_safe_redirect( admin_url( 'admin.php?page=certificate_builder&certificate_id=' . $post_id ) );
				exit;
			}
		}
	}
);

add_filter(
	'stm_lms_menu_items',
	function ( $menus ) {
		$current_role          = STM_LMS_Instructor::is_instructor() || current_user_can( 'administrator' );
		$instructor_capability = STM_LMS_Options::get_option( 'instructors_certificates', false );
		$current_slug          = masterstudy_get_current_account_slug();

		$menus[] = array(
			'order'        => 155,
			'id'           => 'certificates',
			'slug'         => 'my-certificates',
			'lms_template' => 'stm-lms-certificates',
			'menu_title'   => esc_html__( 'My Certificates', 'masterstudy-lms-learning-management-system-pro' ),
			'menu_icon'    => 'stmlms-menu-my-certificates',
			'menu_url'     => \STM_LMS_Course::certificates_page_url(),
			'menu_place'   => 'learning',
			'is_active'    => 'my-certificates' === $current_slug,
			'section'      => 'progress',
		);

		if ( $current_role && $instructor_capability ) {
			$menus[] = array(
				'order'        => 160,
				'id'           => 'instructor-certificates',
				'slug'         => 'instructor-certificates',
				'lms_template' => 'stm-lms-instructor-certificates',
				'menu_title'   => esc_html__( 'Certificates', 'masterstudy-lms-learning-management-system-pro' ),
				'menu_icon'    => 'stmlms-menu-my-certificates',
				'menu_url'     => ms_plugin_user_account_url( 'certificates' ),
				'menu_place'   => 'main',
				'is_active'    => 'instructor-certificates' === $current_slug,
				'section'      => 'progress',
			);
		}

		return $menus;
	}
);

add_filter(
	'masterstudy_lms_certificate_fields_data',
	function ( $fields, $certificate ) {
		$user_id     = get_current_user_id();
		$field_types = array(
			'student_name',
			'author',
		);

		foreach ( $fields as &$field ) {
			if ( ! in_array( $field['type'], $field_types, true ) ) {
				continue;
			}

			$meta_key   = "certificate_{$field['type']}_{$certificate['id']}";
			$meta_value = get_user_meta( $user_id, sanitize_text_field( $meta_key ), true );

			if ( empty( $meta_value ) ) {
				update_user_meta( $user_id, sanitize_text_field( $meta_key ), $field['content'] );
				continue;
			}

			$author = get_post_field( 'post_author', intval( sanitize_text_field( $certificate['course_id'] ) ) );

			$first_name    = get_user_meta( $author, 'first_name', true );
			$last_name     = get_user_meta( $author, 'last_name', true );
			$composed_name = trim( "$first_name $last_name" );

			if ( 'author' === $field['type'] ) {
				$field['content'] = html_entity_decode( $composed_name );
			}
		}

		return $fields;
	},
	10,
	2
);

add_filter(
	'manage_edit-stm-certificates_columns',
	function ( $columns ) {
		$columns['preview-image'] = esc_html__( 'Preview', 'masterstudy-lms-learning-management-system-pro' );
		$columns['instructor']    = esc_html__( 'Instructor', 'masterstudy-lms-learning-management-system-pro' );
		$columns['post_id']       = esc_html__( 'ID', 'masterstudy-lms-learning-management-system-pro' );
		$columns['actions']       = '';

		unset( $columns['date'] );

		return $columns;
	}
);

add_filter(
	'manage_edit-stm-certificates_sortable_columns',
	function ( $sortable_columns ) {
		$sortable_columns['instructor'] = 'instructor';

		return $sortable_columns;
	}
);

add_action(
	'restrict_manage_posts',
	function ( $post_type ) {
		if ( 'stm-certificates' === $post_type && current_user_can( 'manage_options' ) ) {
			$selected_instructor = intval( $_GET['instructor_id'] ?? 0 );
			$users               = get_users(
				array(
					'role__in' => array( 'stm_lms_instructor', 'administrator' ),
				)
			);

			echo '<select name="instructor_id">';
			echo '<option value="">' . esc_html__( 'All Instructors', 'masterstudy-lms-learning-management-system-pro' ) . '</option>';
			foreach ( $users as $user ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $user->ID ),
					selected( $selected_instructor, $user->ID, false ),
					esc_html( $user->display_name )
				);
			}
			echo '</select>';
		}
	}
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() && $query->is_main_query() && 'stm-certificates' === $query->get( 'post_type' ) ) {
			if ( ! empty( $_GET['instructor_id'] ) ) {
				$instructor_id = intval( $_GET['instructor_id'] );
				$query->set( 'author', $instructor_id );
			}
		}
	}
);

add_action(
	'manage_stm-certificates_posts_custom_column',
	function ( $column_key, $post_id ) {
		global $post;
		switch ( $column_key ) {
			case 'preview-image':
				$certificate_preview_path = get_post_meta( $post_id, 'certificate_preview', true );
				$orientation              = get_post_meta( $post_id, 'stm_orientation', true );
				if ( ! empty( $certificate_preview_path ) ) {
					?>
					<img class="masterstudy-admin-certificate__preview <?php echo esc_html( 'portrait' === $orientation ? 'masterstudy-admin-certificate__preview-portrait' : '' ); ?>" src="<?php echo esc_url( $certificate_preview_path ); ?>"/>
					<?php
				}
				break;
			case 'post_id':
				echo esc_html( $post_id );
				break;
			case 'instructor':
				echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) );
				break;
			case 'actions':
				if ( get_post_status( $post_id ) !== 'trash' ) {
					$edit_link   = admin_url( 'admin.php?page=certificate_builder&certificate_id=' . $post_id );
					$delete_link = get_delete_post_link( $post_id );
					?>
					<div class="masterstudy-admin-certificate__block">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'id'    => 'masterstudy-admin-certificate-button',
								'title' => esc_html__( 'Edit', 'masterstudy-lms-learning-management-system-pro' ),
								'link'  => $edit_link,
								'style' => 'secondary',
								'size'  => 'sm',
							)
						);
						?>
						<a class="masterstudy-admin-certificate__delete" href="<?php echo esc_url( $delete_link ); ?>"></a>
					</div>
					<?php
				}
				break;
		}
	},
	10,
	2
);

add_filter(
	'post_row_actions',
	function ( $actions, $post ) {
		if ( 'stm-certificates' === $post->post_type ) {
			$link = admin_url( 'admin.php?page=certificate_builder&certificate_id=' . $post->ID );
			if ( isset( $actions['edit'] ) ) {
				$actions['edit'] = '<a href="' . esc_url( $link ) . '">' . esc_html__( 'Edit', 'masterstudy-lms-learning-management-system-pro' ) . '</a>';
			}
			if ( isset( $actions['view'] ) ) {
				$actions['view'] = '<a href="' . esc_url( $link ) . '">' . esc_html__( 'View', 'masterstudy-lms-learning-management-system-pro' ) . '</a>';
			}
		}

		return $actions;
	},
	10,
	2
);

add_action( 'wp_ajax_stm_get_certificates', array( AjaxController::class, 'get_certificates' ) );
add_action( 'wp_ajax_stm_get_certificate_fields', array( AjaxController::class, 'get_fields' ) );
add_action( 'wp_ajax_stm_save_certificate', array( AjaxController::class, 'save_certificate' ) );
add_action( 'wp_ajax_stm_upload_certificate_images', array( AjaxController::class, 'upload_certificate_images' ) );
add_action( 'wp_ajax_stm_generate_certificates_preview', array( AjaxController::class, 'generate_previews' ) );
add_action( 'wp_ajax_stm_save_default_certificate', array( AjaxController::class, 'save_default_certificate' ) );
add_action( 'wp_ajax_stm_delete_default_certificate', array( AjaxController::class, 'delete_default_certificate' ) );
add_action( 'wp_ajax_stm_save_certificate_category', array( AjaxController::class, 'save_certificate_category' ) );
add_action( 'wp_ajax_stm_delete_certificate_category', array( AjaxController::class, 'delete_certificate_category' ) );
add_action( 'wp_ajax_stm_delete_certificate', array( AjaxController::class, 'delete_certificate' ) );
add_action( 'wp_ajax_stm_get_certificate_categories', array( AjaxController::class, 'get_categories' ) );
add_action( 'wp_ajax_stm_get_certificate', array( AjaxController::class, 'get_certificate' ) );
add_action( 'wp_ajax_nopriv_stm_get_certificate', array( AjaxController::class, 'get_certificate' ) );
