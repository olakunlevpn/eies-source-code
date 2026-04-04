<?php
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentTeacherRepository;

require_once STM_LMS_PRO_ADDONS . '/assignments/user_assignment.class.php';
require_once STM_LMS_PRO_ADDONS . '/assignments/actions.php';
require_once STM_LMS_PRO_ADDONS . '/assignments/filters.php';
require_once STM_LMS_PRO_ADDONS . '/assignments/ajax-action.php';
require_once STM_LMS_PRO_ADDONS . '/assignments/helpers.php';

new STM_LMS_Assignments();

class STM_LMS_Assignments {
	const POST_STATUSES = array(
		'passed'    => array( 'publish', 'draft', 'pending' ),
		'reviewing' => array( 'pending' ),
		'draft'     => array( 'draft' ),
		'unpassed'  => array( 'publish', 'draft', 'pending' ),
	);

	public function __construct() {
		/*ACTIONS*/
		add_action( 'init', array( $this, 'stm_lms_start_assignment' ) );

		/*AJAX*/
		add_action( 'wp_ajax_stm_lms_accept_draft_assignment', array( $this, 'stm_lms_accept_draft_assignment' ) );
		add_action( 'wp_ajax_stm_lms_start_assignment_timer', array( $this, 'stm_lms_start_assignment_timer' ) );

		/*FILTERS*/
		add_filter( 'stm_lms_curriculum_post_types', array( $this, 'assignment_stm_lms_curriculum_post_types' ), 5, 1 );
		add_filter( 'stm_lms_post_types', array( $this, 'assignment_stm_lms_post_types' ), 5, 1 );
		add_filter( 'stm_lms_completed_label', array( $this, 'stm_lms_completed_label' ), 5, 2 );
		add_filter( 'stm_lms_course_item_content', array( $this, 'course_item_content' ), 10, 4 );

		/*Filters*/
		add_filter( 'upload_mimes', array( $this, 'enable_extended_upload' ), 1 );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'ms_lms_disable_real_mime_check' ), 10, 4 );

		add_filter( 'wpcfto_options_page_setup', array( $this, 'stm_lms_settings_page' ) );

		add_filter(
			'stm_lms_header_messages_counter',
			function ( $counter ) {
				$user_id = get_current_user_id();
				return $counter + AssignmentTeacherRepository::total_pending_assignments( $user_id ) + STM_LMS_User_Assignment::my_assignments_statuses( $user_id );
			}
		);

		add_filter(
			'stm_lms_menu_items',
			function ( $menus ) {
				if ( STM_LMS_Instructor::is_instructor() ) {
					$current_slug = masterstudy_get_current_account_slug();

					$menus[] = array(
						'order'        => 40,
						'id'           => 'assignments',
						'slug'         => 'assignments',
						'lms_template' => 'stm-lms-assignments',
						'menu_title'   => esc_html__( 'Assignments', 'masterstudy-lms-learning-management-system-pro' ),
						'menu_icon'    => 'stmlms-menu-assignments',
						'menu_url'     => ms_plugin_user_account_url( 'assignments' ),
						'badge_count'  => AssignmentTeacherRepository::total_pending_assignments( get_current_user_id() ),
						'menu_place'   => 'main',
						'is_active'    => 'assignments' === $current_slug,
						'section'      => 'progress',
					);
				}

				return $menus;
			}
		);
	}

	public static function course_item_content( $content, $post_id, $item_id, $data ) {
		if ( self::is_assignment( $item_id ) ) {
			ob_start();
			STM_LMS_Templates::show_lms_template( 'course-player/assignments/main', compact( 'post_id', 'item_id', 'data' ) );
			return ob_get_clean();
		}

		return $content;
	}

	public static function uploaded_attachments( $draft_id, $whose = 'student_attachments' ) {
		$data        = self::get_draft_attachments( $draft_id, $whose );
		$attachments = array();
		if ( ! empty( $data ) ) {
			foreach ( $data as $attachment ) {
				$attachments[] = array(
					'data' => array(
						'name'   => $attachment->post_title,
						'id'     => $attachment->ID,
						'status' => 'uploaded',
						'error'  => false,
						'link'   => wp_get_attachment_url( $attachment->ID ),
					),
				);
			}
		}

		return $attachments;
	}

	public static function get_draft_attachments( $draft_id, $whose ) {
		$attachment_ids = get_post_meta( $draft_id, $whose, true );

		if ( ! empty( $attachment_ids ) && is_array( $attachment_ids ) ) {
			return get_posts(
				array(
					'post_type'      => 'attachment',
					'posts_per_page' => -1,
					'post_parent'    => $draft_id,
					'post__in'       => $attachment_ids,
					'order'          => 'asc',
				)
			);
		}

		return array();
	}

	public static function limit_files() {
		$settings = self::stm_lms_get_settings();

		return ( ! empty( $settings['max_files'] ) ) ? $settings['max_files'] : 5;
	}

	public static function attempts_num( $item_id ) {
		$settings = self::stm_lms_get_settings();

		$attempt_tries = ( ! empty( $settings['attempt_tries'] ) ) ? $settings['attempt_tries'] : 0;

		$item_attempts = get_post_meta( $item_id, 'assignment_tries', true );

		if ( ! empty( $item_attempts ) ) {
			$attempt_tries = $item_attempts;
		}

		return $attempt_tries;
	}

	public static function get_current_url() {
		return ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	public static function is_assignment( $post_id ) {
		return ( get_post_type( $post_id ) === 'stm-assignments' );
	}

	public static function number_of_assignments( $item_id ) {
		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			wp_safe_redirect( remove_query_arg( array( 'start_assignment', 'course_id' ), self::get_current_url() ) );
		}

		$args = array(
			'post_type'   => 'stm-user-assignment',
			'post_status' => array(
				'publish',
				'draft',
				'pending',
			),
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'     => 'assignment_id',
					'value'   => $item_id,
					'compare' => '=',
				),
				array(
					'key'     => 'student_id',
					'value'   => $user['id'],
					'compare' => '=',
				),
			),
		);

		$q = new WP_Query( $args );

		return $q->found_posts;
	}

	public static function has_passed_assignment( $item_id, $student_id = '' ) {
		$args = array(
			'post_type'      => 'stm-user-assignment',
			'posts_per_page' => 1,
			'post_status'    => self::POST_STATUSES['passed'],
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'assignment_id',
					'value'   => $item_id,
					'compare' => '=',
				),
				array(
					'key'     => 'student_id',
					'value'   => $student_id,
					'compare' => '=',
				),
				array(
					'key'     => 'status',
					'value'   => 'passed',
					'compare' => '=',
				),
			),
		);

		$query = new WP_Query( $args );

		return $query->have_posts();
	}

	public static function is_draft_assignment( $item_id, $user_id = '' ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return false;
		}

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT ID
				FROM {$wpdb->posts}
				WHERE post_type = %s
				AND post_status = %s
				AND ID IN (
					SELECT post_id
					FROM {$wpdb->postmeta}
					WHERE (meta_key = 'assignment_id' AND meta_value = %s)
					AND post_id IN (
						SELECT post_id
						FROM {$wpdb->postmeta}
						WHERE (meta_key = 'student_id' AND meta_value = %s)
					)
					AND post_id IN (
						SELECT post_id
						FROM {$wpdb->postmeta}
						WHERE (meta_key = 'status' AND meta_value = '')
					)
				)
				LIMIT 1
				",
				'stm-user-assignment',
				'draft',
				$item_id,
				$user_id
			)
		);

		return null !== $result;
	}

	/*Settings*/
	public function stm_lms_settings_page( $setups ) {
		$setups[] = array(
			'page'        => array(
				'parent_slug' => 'stm-lms-settings',
				'page_title'  => 'Assignments Settings',
				'menu_title'  => 'Assignments Settings',
				'menu_slug'   => 'assignments_settings',
			),
			'fields'      => $this->stm_lms_settings(),
			'option_name' => 'stm_lms_assignments_settings',
		);

		return $setups;
	}

	public function stm_lms_settings() {
		return apply_filters(
			'stm_lms_assignments_settings',
			array(
				'credentials' => array(
					'name'   => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ),
					'fields' => array(
						'attempt_tries' => array(
							'type'  => 'number',
							'label' => esc_html__( 'Number of allowed attempts to pass assignment', 'masterstudy-lms-learning-management-system-pro' ),
							'value' => false,
							'hint'  => esc_html__( 'Choose the maximum number of times a student can attempt to pass an assignment or leave it empty for unlimited attempts', 'masterstudy-lms-learning-management-system-pro' ),
						),
						'assignments_allow_upload_attachments' => array(
							'type'        => 'checkbox',
							'label'       => esc_html__( 'File attachments for assignments', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Decide if students can attach files to their assignments', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => true,
						),
						'max_files'     => array(
							'type'        => 'number',
							'label'       => esc_html__( 'Number of allowed attachments', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Specify the maximum number of attachments allowed per assignment', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => false,
							'dependency'  => array(
								'key'   => 'assignments_allow_upload_attachments',
								'value' => 'not_empty',
							),
						),
						'max_file_size' => array(
							'type'        => 'number',
							'label'       => esc_html__( 'Max file size (MB)', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Set the maximum file size allowed for each attachment in MB', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => false,
							'dependency'  => array(
								'key'   => 'assignments_allow_upload_attachments',
								'value' => 'not_empty',
							),
						),
						'files_ext'     => array(
							'type'        => 'textarea',
							'label'       => esc_html__( 'File extensions allowed to upload', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Specify the file extensions allowed for attachments, separated by commas without spaces', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => 'jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,ppt,pptx,pps,ppsx,xls,xlsx,psd,mp3,ogg,wav,mp4,m4v,mov,wmv,avi,mpg,zip',
							'dependency'  => array(
								'key'   => 'assignments_allow_upload_attachments',
								'value' => 'not_empty',
							),
						),
						'assignments_allow_audio_recording' => array(
							'type'        => 'checkbox',
							'label'       => esc_html__( 'Allow audio recording for assignments', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Decide if students can record audio for their assignments', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => false,
						),
						'assignments_audio_recording_maxsize' => array(
							'type'        => 'number',
							'label'       => esc_html__( 'Audio recording max size in MB', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Set the maximum size allowed for audio recordings in MB', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => 200,
							'dependency'  => array(
								'key'   => 'assignments_allow_audio_recording',
								'value' => 'not_empty',
							),
						),
						'assignments_allow_video_recording' => array(
							'type'        => 'checkbox',
							'label'       => esc_html__( 'Video recording for assignments', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Determine if students can record video for their assignments', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => false,
						),
						'assignments_video_recording_maxsize' => array(
							'type'        => 'number',
							'label'       => esc_html__( 'Video recording max size in MB', 'masterstudy-lms-learning-management-system-pro' ),
							'description' => esc_html__( 'Set the maximum size allowed for video recordings in MB', 'masterstudy-lms-learning-management-system-pro' ),
							'value'       => 200,
							'dependency'  => array(
								'key'   => 'assignments_allow_video_recording',
								'value' => 'not_empty',
							),
						),

					),
				),
			)
		);
	}

	public static function stm_lms_get_settings() {
		return get_option( 'stm_lms_assignments_settings', array() );
	}

	public function assignment_stm_lms_post_types( $post_types ) {
		$post_types[] = 'stm-assignments';
		$post_types[] = 'stm-user-assignment';

		return $post_types;
	}

	public function assignment_stm_lms_curriculum_post_types( $post_types ) {
		$post_types[] = 'stm-assignments';

		return $post_types;
	}

	public function stm_lms_completed_label( $label, $item_id ) {
		if ( ! self::is_assignment( $item_id ) ) {
			return $label;
		}

		return '';
	}

	/*ACTIONS*/
	public function stm_lms_start_assignment() {
		if ( ! empty( $_GET['start_assignment'] ) && ! empty( $_GET['course_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$user = STM_LMS_User::get_current_user();
			if ( empty( $user['id'] ) ) {
				wp_safe_redirect( remove_query_arg( array( 'start_assignment', 'course_id' ), self::get_current_url() ) );
				die;
			}

			$user_id         = $user['id'];
			$course_id       = intval( $_GET['course_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$item_id         = intval( $_GET['start_assignment'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$assignment_name = get_the_title( $item_id );

			$has_access = STM_LMS_User::has_course_access( $course_id, $item_id, false );
			if ( ! $has_access ) {
				wp_safe_redirect( remove_query_arg( array( 'start_assignment', 'course_id' ), self::get_current_url() ) );
				die;
			}

			$last_attempt = ( new AssignmentStudentRepository() )->get_last_attempt( $course_id, $item_id, $user_id );
			if ( ! empty( $last_attempt ) && AssignmentStudentRepository::STATUS_NOT_PASSED !== $last_attempt['status'] ) {
				wp_safe_redirect( remove_query_arg( array( 'start_assignment', 'course_id' ), self::get_current_url() ) );
				die;
			}

			$assignment_try = self::number_of_assignments( $item_id ) + 1;

			$new_assignment = array(
				'post_type'   => 'stm-user-assignment',
				'post_status' => 'draft',
				'post_title'  => "{$user['login']} on \"{$assignment_name}\"",
			);

			$assignment_id = wp_insert_post( $new_assignment );

			update_post_meta( $assignment_id, 'try_num', $assignment_try );
			update_post_meta( $assignment_id, 'status', '' );
			update_post_meta( $assignment_id, 'assignment_id', $item_id );
			update_post_meta( $assignment_id, 'student_id', $user_id );

			do_action( 'stm_lms_assignment_before_drafting', $assignment_id );

			wp_safe_redirect( remove_query_arg( array( 'start_assignment', 'course_id' ), self::get_current_url() ) );
		}
	}

	/*FILTERS*/
	public function enable_extended_upload( $mime_types ) {
		$mime_types['pdf']  = 'application/pdf';
		$mime_types['doc']  = 'application/msword';
		$mime_types['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		$mime_types['ppt']  = 'application/mspowerpoint';
		$mime_types['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
		$mime_types['pps']  = 'application/vnd.ms-powerpoint';
		$mime_types['pot']  = 'application/vnd.ms-powerpoint';
		$mime_types['xla']  = 'application/vnd.ms-excel';
		$mime_types['xlt']  = 'application/vnd.ms-excel';
		$mime_types['xls']  = 'application/vnd.ms-excel';
		$mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		$mime_types['mp3']  = 'audio/mpeg';
		$mime_types['ogg']  = 'audio/ogg';
		$mime_types['zip']  = 'application/zip';
		$mime_types['psd']  = 'application/octet-stream';
		$mime_types['jpg']  = 'image/jpeg';
		$mime_types['jpeg'] = 'image/jpeg';
		$mime_types['jpe']  = 'image/jpeg';
		$mime_types['gif']  = 'image/gif';
		$mime_types['png']  = 'image/png';
		$mime_types['bmp']  = 'image/bmp';
		$mime_types['webp'] = 'image/webp';
		$mime_types['mpeg'] = 'video/mpeg';
		$mime_types['mpg']  = 'video/mpeg';
		$mime_types['mpe']  = 'video/mpeg';
		$mime_types['mp4']  = 'video/webm';
		$mime_types['m4v']  = 'video/webm';
		$mime_types['webm'] = 'video/webm';
		$mime_types['mov']  = 'video/quicktime';
		$mime_types['qt']   = 'video/quicktime';
		$mime_types['m4a']  = 'audio/mpeg';
		$mime_types['m4b']  = 'audio/mpeg';
		$mime_types['wav']  = 'audio/webm';
		$mime_types['rar']  = 'application/x-rar';
		$mime_types['js']   = 'application/javascript';
		$mime_types['yaml'] = 'text/yaml';
		$mime_types['yml']  = 'text/yaml';
		$mime_types['txt']  = 'text/plain';

		return $mime_types;
	}

	public function ms_lms_disable_real_mime_check( $data, $file, $filename, $mimes ) {
		$ext = pathinfo( $filename, PATHINFO_EXTENSION );

		$custom_mimes = array(
			'yaml' => 'text/yaml',
			'yml'  => 'text/yaml',
			'js'   => 'application/javascript',
			'txt'  => 'text/plain',
		);

		if ( isset( $custom_mimes[ $ext ] ) ) {
			$data['ext']  = $ext;
			$data['type'] = $mimes[ $ext ] ?? $custom_mimes[ $ext ];
		}

		return $data;
	}

	public function stm_lms_accept_draft_assignment() {
		check_ajax_referer( 'stm_lms_accept_draft_assignment', 'nonce' );

		$draft_id = isset( $_POST['draft_id'] ) ? absint( wp_unslash( $_POST['draft_id'] ) ) : 0;
		$course_id = isset( $_POST['course_id'] ) ? absint( wp_unslash( $_POST['course_id'] ) ) : 0;
		$item_id = isset( $_POST['item_id'] ) ? absint( wp_unslash( $_POST['item_id'] ) ) : 0;

		if ( 0 === $draft_id || 0 === $course_id || 0 === $item_id ) {
			$return = array(
				'message' => 'Failed',
			);
			wp_send_json( $return );
		}

		$content             = ! empty( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
		$is_draft            = rest_sanitize_boolean( wp_unslash( $_POST['is_draft'] ?? false ) );
		$is_timeout_submit   = rest_sanitize_boolean( wp_unslash( $_POST['is_timeout_submit'] ?? false ) );
		$status              = ( new AssignmentStudentRepository() )->get_status( $draft_id );
		$is_deadline_expired = masterstudy_lms_assignment_is_deadline_expired( $course_id, $item_id );
		$has_answer          = self::has_meaningful_answer( $draft_id, $content );

		if ( empty( $status ) || AssignmentStudentRepository::STATUS_DRAFT !== $status ) {
			$return = array(
				'message' => 'Failed',
			);
			wp_send_json( $return );
		}

		if ( $is_deadline_expired ) {
			// On timeout we allow final submit only if answer has text or attachments.
			if ( ! $is_timeout_submit || ! $has_answer ) {
				$return = array(
					'message' => 'Failed',
				);
				wp_send_json( $return );
			}
		}

		wp_send_json( self::stm_lms_accept_draft_assignment_static( $draft_id, $course_id, $content, $is_draft ) );
	}

	private static function has_meaningful_answer( int $draft_id, string $content = '' ): bool {
		$raw_content   = '' !== $content ? $content : (string) get_post_field( 'post_content', $draft_id );
		$plain_content = trim( wp_strip_all_tags( $raw_content ) );

		if ( '' !== $plain_content ) {
			return true;
		}

		$attachments = get_post_meta( $draft_id, AssignmentStudentRepository::ATTACHMENT_META, true );

		return is_array( $attachments ) && ! empty( array_filter( array_map( 'intval', $attachments ) ) );
	}

	public static function stm_lms_accept_draft_assignment_static( $draft_id = '', $course_id = '', $content = '', $is_draft = false ): string {
		$course_id = intval( $course_id );
		$draft_id  = intval( $draft_id );

		$user = STM_LMS_User::get_current_user();
		if ( empty( $user['id'] ) ) {
			return 'Failed';
		}

		$user_id = $user['id'];

		$assignment_student_id = intval( get_post_meta( $draft_id, 'student_id', true ) );
		$post_author_id        = get_post_field( 'post_author', get_post_meta( $draft_id, 'assignment_id', true ) );
		$instructor            = STM_LMS_User::get_current_user( $post_author_id );

		if ( $user_id === $assignment_student_id ) {
			$status = $is_draft ? AssignmentStudentRepository::STATUS_DRAFT : AssignmentStudentRepository::STATUS_PENDING;

			update_post_meta( $draft_id, 'status', $status );

			wp_update_post(
				array(
					'ID'           => $draft_id,
					'post_type'    => PostType::USER_ASSIGNMENT,
					'post_status'  => $status,
					'post_title'   => get_the_title( $draft_id ),
					'post_content' => $content,
				)
			);

			update_post_meta( $draft_id, 'course_id', $course_id );

			$user_login       = $user['login'];
			$course_title     = get_the_title( $course_id );
			$assignment_title = get_the_title( $draft_id );
			$assignment_meta  = get_post_meta( $draft_id );

			if ( ! empty( $assignment_meta ) && $assignment_meta['assignment_id'] ) {
				$assignment_title = get_the_title( $assignment_meta['assignment_id'][0] );
			}

			$message = sprintf(
			/* translators: %1$s Course Title, %2$s User Login */
				esc_html__( 'Check the new assignment that was submitted by the student. Assignment on %1$s sent by %2$s in the course %3$s', 'masterstudy-lms-learning-management-system-pro' ),
				$assignment_title,
				$user_login,
				$course_title,
			);

			$email_data = array(
				'user_login'       => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
				'course_title'     => $course_title,
				'assignment_title' => $assignment_title,
				'assignment_url'   => \MS_LMS_Email_Template_Helpers::link( STM_LMS_Lesson::get_lesson_url( $course_id, $draft_id ) ),
				'blog_name'        => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
				'site_url'         => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
				'date'             => gmdate( 'Y-m-d H:i:s' ),
			);

			if ( ! $is_draft ) {
				STM_LMS_Helpers::send_email(
					$instructor['email'],
					esc_html__( 'New Assignment Submitted by {{user_login}}', 'masterstudy-lms-learning-management-system-pro' ),
					$message,
					'stm_lms_new_assignment',
					$email_data
				);

				if ( $assignment_meta['assignment_id'][0] && masterstudy_lms_assignment_has_time_limit( $assignment_meta['assignment_id'][0] ) ) {
					$timer_data = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_assignments_time( $user['id'], $draft_id, array( 'end_time' ) ) );

					stm_lms_delete_user_assignment_time( $user_id, $draft_id );

					masterstudy_lms_create_assignment_timer(
						$user_id,
						$draft_id,
						time(),
						! empty( $timer_data['end_time'] ) ? masterstudy_lms_assignment_parse_time_to_timestamp( $timer_data['end_time'] ) : time()
					);
				}
			}
		}

		return 'OK';
	}

	public static function get_attempts( $course_id, $item_id, $user_id ): array {
		$total_attempts = self::attempts_num( $item_id );

		if ( empty( $total_attempts ) ) {
			return array(
				'can_attempt' => 1,
			);
		}

		$attempts = ( new AssignmentStudentRepository() )->get_attempts_count( $course_id, $item_id, $user_id, 'not_passed' );

		return array(
			'total'       => $total_attempts,
			'attempts'    => $attempts,
			'can_attempt' => ( intval( $total_attempts ) - $attempts > 0 ),
		);
	}

	public static function student_view_update( $item_id ) {
		$item_id    = intval( $item_id );
		$student_id = get_post_meta( $item_id, 'student_id', true );

		if ( get_current_user_id() === intval( $student_id ) ) {
			update_post_meta( $item_id, 'who_view', 1 );
		}
	}

	public static function decline_assignment( int $user_assignment_id, int $course_id, int $user_id = null ) {
		$user_id = $user_id ?? STM_LMS_User::get_current_user()['id'] ?? null;

		update_post_meta( $user_assignment_id, 'course_id', $course_id );
		$assignment_repository = new AssignmentStudentRepository();
		$assignment_repository->update_status( $user_assignment_id, AssignmentStudentRepository::STATUS_NOT_PASSED );
		if ( is_ms_lms_addon_enabled( 'grades' ) ) {
			$assignment_repository->update_grade( $user_assignment_id, 0 );
		}

		STM_LMS_Course::update_course_progress( $user_id, $course_id );
	}

	public static function update_timer( int $assignment_id, array $data ): array {
		if ( AssignmentStudentRepository::STATUS_DRAFT !== $data['current_template'] ) {
			return $data;
		}

		if ( ! masterstudy_lms_assignment_has_time_limit( $assignment_id ) ) {
			return $data;
		}

		$user         = STM_LMS_User::get_current_user();
		$user_id      = $user['id'] ?? null;
		$last_attempt = ( new AssignmentStudentRepository() )->get_last_attempt( $data['post_id'], $assignment_id, $user_id );
		$timer        = STM_LMS_Helpers::simplify_db_array( stm_lms_get_user_assignments_time( $user_id, $last_attempt['user_assignment_id'] ) );

		if ( empty( $timer ) ) {
			return $data;
		}

		if ( ! empty( $timer['end_time'] ) ) {
			$end_ts = masterstudy_lms_assignment_parse_time_to_timestamp( $timer['end_time'] ); // 'YYYY-mm-dd HH:ii:ss' → int

			if ( 0 !== $end_ts && $end_ts < time() ) {
				if ( self::has_meaningful_answer( (int) $data['assignment_id'] ) ) {
					$content = (string) get_post_field( 'post_content', (int) $data['assignment_id'] );
					self::stm_lms_accept_draft_assignment_static( (int) $data['assignment_id'], (int) $data['post_id'], $content, false );

					$data['current_template'] = AssignmentStudentRepository::STATUS_PENDING;
					$data['status_message']   = masterstudy_lms_get_assignment_status_translations()[ AssignmentStudentRepository::STATUS_PENDING ];

					return $data;
				}

				self::decline_assignment( $data['assignment_id'], $data['post_id'] );

				$data['current_template'] = AssignmentStudentRepository::STATUS_NOT_PASSED;
				$data['status_message']   = masterstudy_lms_get_assignment_status_translations()[ AssignmentStudentRepository::STATUS_NOT_PASSED ];
			}
		}

		return $data;
	}

	public static function stm_lms_start_assignment_timer() {
		check_ajax_referer( 'stm_lms_start_assignment_timer', 'nonce' );

		$assignment_id = isset( $_GET['assignment_id'] ) ? (int) $_GET['assignment_id'] : 0;
		if ( ! $assignment_id ) {
			wp_send_json_error( array( 'message' => 'Invalid assignment_id' ) );
		}

		$course_id = isset( $_GET['course_id'] ) ? (int) $_GET['course_id'] : 0;

		$user    = STM_LMS_User::get_current_user();
		$user_id = isset( $user['id'] ) ? (int) $user['id'] : 0;
		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => 'User not authenticated' ) );
		}

		$now      = time();
		$item_id  = (int) get_post_meta( $assignment_id, 'assignment_id', true );
		$duration = (int) masterstudy_lms_get_assignment_duration( $item_id );

		$response = static function ( int $timer_end_ts, int $server_now_ts ): array {
			return array(
				'timer_end_ts'  => $timer_end_ts,
				'server_now_ts' => $server_now_ts,
			);
		};

		// No time limit – no timer needed.
		if ( $duration <= 0 ) {
			wp_send_json_success( $response( 0, $now ) );
		}

		// Try to find an existing active timer for this assignment.
		$timer = STM_LMS_Helpers::simplify_db_array(
			stm_lms_get_user_assignments_time(
				$user_id,
				$assignment_id,
				array( 'end_time' )
			)
		);

		// If a timer already exists – just return its end timestamp.
		if ( ! empty( $timer['end_time'] ) ) {
			$end_ts = masterstudy_lms_assignment_parse_time_to_timestamp( $timer['end_time'] );
			if ( 0 !== $end_ts ) {
				wp_send_json_success( $response( $end_ts, $now ) );
			}
		}

		// No active timer – we need to create one.
		$count_to = $now + $duration;

		$repo         = new AssignmentStudentRepository();
		$last_attempt = $repo->get_last_attempt( $course_id, $item_id, $user_id, $assignment_id );

		$retake_limit_reset = (bool) get_post_meta( $item_id, 'assignment_retake_limit_reset', true );

		// First attempt or retake with reset – start full duration timer.
		if ( empty( $last_attempt ) || $retake_limit_reset ) {
			if ( false === masterstudy_lms_create_assignment_timer( $user_id, $assignment_id, $now, $count_to ) ) {
				wp_send_json_error( array( 'message' => 'Unable to create timer' ) );
			}
			wp_send_json_success( $response( $count_to, $now ) );
		}

		// There is a previous attempt – try to restore remaining time.
		$prev_timer = STM_LMS_Helpers::simplify_db_array(
			stm_lms_get_user_assignments_time(
				$user_id,
				$last_attempt['user_assignment_id'],
				array( 'start_time', 'end_time' )
			)
		);

		$remaining = masterstudy_lms_assignment_calculate_remaining_time( $prev_timer );
		if ( $remaining > 0 ) {
			$count_to = $now + $remaining;
			if ( false === masterstudy_lms_create_assignment_timer( $user_id, $assignment_id, $now, $count_to ) ) {
				wp_send_json_error( array( 'message' => 'Unable to create timer' ) );
			}
			wp_send_json_success( $response( $count_to, $now ) );
		}

		// Fallback: if remaining cannot be calculated, just start a full Timer.
		if ( false === masterstudy_lms_create_assignment_timer( $user_id, $assignment_id, $now, $count_to ) ) {
			wp_send_json_error( array( 'message' => 'Unable to create timer' ) );
		}
		wp_send_json_success( $response( $count_to, $now ) );
	}
}
