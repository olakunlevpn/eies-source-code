<?php
/**
 * Assignment actions.
 */

use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentTeacherRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeDisplay;
use MasterStudy\Lms\Repositories\CurriculumRepository;

/**
 * Enqueues frontend scripts for instructor assignments.
 *
 * @return void
 */
function stm_lms_assignments_enqueue_frontend_scripts() {
	wp_register_style( 'masterstudy-account-instructor-assignments-table', STM_LMS_PRO_URL . 'assets/css/account/instructor/assignments/instructor-assignments-table.css', array(), STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-account-student-assignments-table', STM_LMS_PRO_URL . 'assets/css/account/instructor/assignments/student-assignments-table.css', array(), STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-account-instructor-assignments-table', STM_LMS_PRO_URL . 'assets/js/account/instructor/assignments/instructor-assignments-table.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-account-student-assignments-list', STM_LMS_PRO_URL . 'assets/js/account/instructor/assignments/student-assignments-list.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'stm_lms_assignments_enqueue_frontend_scripts' );

/**
 * Enqueues admin scripts for instructor assignments.
 *
 * @return void
 */
function stm_lms_assignments_enqueue_scripts() {
	$screen = get_current_screen();

	if ( PostType::ASSIGNMENT === $screen->post_type ) {
		wp_enqueue_style(
			'stm_assignments',
			STM_LMS_PRO_URL . 'assets/css/assignments/instructor-assignments.css',
			array(),
			STM_LMS_PRO_VERSION
		);
	}
}
add_action( 'admin_enqueue_scripts', 'stm_lms_assignments_enqueue_scripts' );

/**
 * Fires for each custom column of a specific post type in the Posts list table.
 *
 * @param  array   $columns       - The name of the column to display.
 * @param  integer $assignment_id - The current post/assignment ID.
 * @return void
 */
function stm_lms_assignments_column_fields( $columns, $assignment_id ) {
	switch ( $columns ) {
		case 'lms_course':
			$course_ids = ( new CurriculumRepository() )->get_lesson_course_ids( $assignment_id );

			if ( ! empty( $course_ids ) ) {
				foreach ( $course_ids as $course_id ) {
					echo '<p><a href="' . esc_url( ms_plugin_manage_course_url( $course_id ) ) . '">' . esc_html( get_the_title( $course_id ) ) . '</a></p>';
				}
			} else {
				echo esc_html__( 'No linked courses', 'masterstudy-lms-learning-management-system-pro' );
			}

			break;
		case 'lms_total':
			echo esc_html__( 'Total', 'masterstudy-lms-learning-management-system-pro' );
			echo ': <strong>' . esc_html( AssignmentTeacherRepository::user_assignments_count( $assignment_id ) ) . '</strong>';
			break;
		case 'lms_passed':
		case 'lms_not_passed':
		case 'lms_pending':
			$status = str_replace( 'lms_', '', $columns );
			echo wp_kses_post(
				( new AssignmentStudentRepository() )->get_status_html( $status )
			);
			echo ': <strong>' . esc_html( AssignmentTeacherRepository::user_assignments_count( $assignment_id, $status ) ) . '</strong>';
			break;
		case 'lms_view':
			if ( current_user_can( 'administrator' ) ) {
				$link = admin_url( 'edit.php?post_type=stm-user-assignment&assignment_id=' . $assignment_id );
				echo '<a class="button action" href="' . esc_url( $link ) . '">' . esc_html__( 'More', 'masterstudy-lms-learning-management-system-pro' ) . '</a>';
			}
			break;
	}
}
add_action( 'manage_stm-assignments_posts_custom_column', 'stm_lms_assignments_column_fields', 10, 2 );

/**
 * Enqueue scripts for student assignments.
 *
 * @return void
 */
function stm_lms_student_assignments_enqueue_scripts() {
	$screen = get_current_screen();

	if ( 'stm-user-assignment' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style(
		'stm_assignments',
		STM_LMS_PRO_URL . 'assets/css/assignments/student-assignments.css',
		array( 'masterstudy-audio-player', 'masterstudy-video-player' ),
		STM_LMS_PRO_VERSION
	);

	wp_enqueue_media();

	wp_enqueue_script(
		'stm_assignments',
		STM_LMS_PRO_URL . 'assets/js/assignments/student-assignments.js',
		array( 'jquery', 'masterstudy-video-recorder', 'masterstudy-audio-recorder', 'masterstudy-audio-player' ),
		STM_LMS_PRO_VERSION,
		true
	);

	wp_localize_script(
		'stm_assignments',
		'stm_student_assignments',
		array(
			'mediaAPI' => rest_url( 'masterstudy-lms/v2/media' ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'message'  => array(
				'error' => array(
					'title' => esc_html__( 'Error', 'masterstudy-lms-learning-management-system-pro' ),
					'text'  => esc_html__( 'Oops, something went wrong. Please try again later.', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'audio' => array(
					'permission' => esc_html__( 'Please allow browser microphone access.', 'masterstudy-lms-learning-management-system-pro' ),
					'size_error' => esc_html__( 'Audio file is too big.', 'masterstudy-lms-learning-management-system-pro' ),
					'download'   => esc_html__( 'Download audio file.', 'masterstudy-lms-learning-management-system-pro' ),
				),
				'video' => array(
					'permission' => esc_html__( 'Please allow browser camera & microphone access.', 'masterstudy-lms-learning-management-system-pro' ),
					'size_error' => esc_html__( 'Video file is too big.', 'masterstudy-lms-learning-management-system-pro' ),
					'download'   => esc_html__( 'Download video file.', 'masterstudy-lms-learning-management-system-pro' ),
				),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'stm_lms_student_assignments_enqueue_scripts' );

/**
 * Adds metaboxes for student assignments.
 *
 * @return void
 */
function stm_lms_add_student_assignments_metaboxes() {
	add_meta_box(
		'stm_lms_student_assignment_attachments',
		esc_html__( 'Students attachments', 'masterstudy-lms-learning-management-system-pro' ),
		'stm_lms_student_assignment_attachments',
		'stm-user-assignment',
		'normal'
	);
	add_meta_box(
		'stm_lms_assignment_instructor_review',
		esc_html__( 'Instructor Review', 'masterstudy-lms-learning-management-system-pro' ),
		'stm_lms_student_assignment_review',
		'stm-user-assignment',
		'normal'
	);
}
add_action( 'add_meta_boxes', 'stm_lms_add_student_assignments_metaboxes' );

/**
 * Student assignment attachment metabox callback.
 *
 * @param  WP_Post $post - Post object.
 * @return void
 */
function stm_lms_student_assignment_attachments( $post ) {
	?>
	<div class="masterstudy-assignment__metafields">
		<div class="masterstudy-assignment__student-attachments">
			<h2><?php echo esc_html__( 'Student attachments', 'masterstudy-lms-learning-management-system-pro' ); ?>:</h2>
			<?php
			$attachments = STM_LMS_Assignments::get_draft_attachments( $post->ID, 'student_attachments' );
			if ( ! empty( $attachments ) ) :
				STM_LMS_Templates::show_lms_template(
					'components/file-attachment',
					array(
						'attachments' => $attachments,
						'dark_mode'   => false,
					)
				);

			else :
				?>
				<p class="masterstudy-assignment__attachments_empty"><?php echo esc_html__( 'No attachements yet', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Student assignment review.
 *
 * @param  WP_Post $post - Post object.
 * @return void
 */
function stm_lms_student_assignment_review( $post ) {
	wp_nonce_field( 'stm_lms_assignment_instructor_review_save', 'stm_lms_assignment_instructor_review' );
	$status = ( new AssignmentStudentRepository() )->get_status( $post->ID );
	$review = get_post_meta( $post->ID, 'editor_comment', true );
	?>
	<div class="masterstudy-assignment__metafields">
		<div class="masterstudy-assignment__instructor-review">
			<h2><?php echo esc_html__( 'Add review', 'masterstudy-lms-learning-management-system-pro' ); ?>:</h2>
			<?php

			if ( ! is_ms_lms_addon_enabled( 'grades' ) ) {
				STM_LMS_Templates::show_lms_template(
					'components/radio-buttons',
					array(
						'name'  => 'status',
						'items' => array(
							array(
								'value'   => 'passed',
								'label'   => esc_html__( 'Passed', 'masterstudy-lms-learning-management-system-pro' ),
								'checked' => 'passed' === $status,
								'style'   => 'success',
							),
							array(
								'value'   => 'not_passed',
								'label'   => esc_html__( 'Failed', 'masterstudy-lms-learning-management-system-pro' ),
								'checked' => 'not_passed' === $status,
								'style'   => 'danger',
							),
						),
					)
				);
			}

			do_action( 'masterstudy_lms_admin_assignment_review', $post->ID );

			STM_LMS_Templates::show_lms_template(
				'components/wp-editor',
				array(
					'id'        => 'editor_comment',
					'dark_mode' => false,
					'content'   => $review,
					'settings'  => array(
						'wpautop'       => false,
						'quicktags'     => false,
						'media_buttons' => false,
						'textarea_rows' => 13,
					),
				)
			);
			?>
			<div class="masterstudy-assignment__instructor-attachments">
				<?php
					$attachment_ids     = get_post_meta( $post->ID, 'instructor_attachments', true );
					$attachment_ids     = ! empty( $attachment_ids ) ? $attachment_ids : array();
					$review_attachments = STM_LMS_Assignments::get_draft_attachments( $post->ID, 'instructor_attachments' );
					STM_LMS_Templates::show_lms_template(
						'components/file-attachment',
						array(
							'attachments' => $review_attachments,
							'download'    => false,
							'deletable'   => true,
						)
					);
				?>
				<input name="instructor_attachments" type="hidden" value="<?php echo esc_attr( implode( ',', $attachment_ids ) ); ?>">
			</div>
			<div class="masterstudy-assignment__instructor-review__controls">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'            => 'masterstudy-file-upload-field',
					'title'         => esc_html__( 'Attach file', 'masterstudy-lms-learning-management-system-pro' ),
					'link'          => '',
					'icon_position' => 'left',
					'icon_name'     => 'plus',
					'style'         => 'tertiary',
					'size'          => 'sm',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'            => 'masterstudy-audio-recorder',
					'title'         => esc_html__( 'Record audio', 'masterstudy-lms-learning-management-system-pro' ),
					'link'          => '',
					'icon_position' => 'left',
					'icon_name'     => 'mic',
					'style'         => 'tertiary',
					'size'          => 'sm',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'            => 'masterstudy-video-recorder',
					'title'         => esc_html__( 'Record video', 'masterstudy-lms-learning-management-system-pro' ),
					'link'          => '',
					'icon_position' => 'left',
					'icon_name'     => 'camera',
					'style'         => 'tertiary',
					'size'          => 'sm',
				)
			);
			?>
			</div>
			<div class="masterstudy-assignment__instructor-review__controls-items">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/audio-recorder',
					array(
						'preloader' => false,
					)
				);

				STM_LMS_Templates::show_lms_template(
					'components/video-recorder',
					array(
						'preloader' => false,
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/progress',
					array(
						'title'     => esc_html__( 'Processing', 'masterstudy-lms-learning-management-system-pro' ),
						'is_hidden' => true,
						'progress'  => 0,
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/alert',
					array(
						'id'                  => 'assignment_file_alert',
						'title'               => esc_html__( 'Delete file', 'masterstudy-lms-learning-management-system-pro' ),
						'text'                => esc_html__( 'Are you sure you want to delete this file?', 'masterstudy-lms-learning-management-system-pro' ),
						'submit_button_text'  => esc_html__( 'Delete', 'masterstudy-lms-learning-management-system-pro' ),
						'cancel_button_text'  => esc_html__( 'Cancel', 'masterstudy-lms-learning-management-system-pro' ),
						'submit_button_style' => 'danger',
						'cancel_button_style' => 'tertiary',
						'dark_mode'           => false,
					)
				);
				STM_LMS_Templates::show_lms_template(
					'components/message',
					array(
						'id'          => 'message-box',
						'bg'          => 'danger',
						'color'       => 'danger',
						'icon'        => 'warning',
						'show_header' => true,
						'link_url'    => '#',
						'is_vertical' => true,
					)
				);
				?>
				<div class="masterstudy-file-attachment" data-id="masterstudy-file-attachment__template">
					<div class="masterstudy-file-attachment__info">
						<img src="" class="masterstudy-file-attachment__image masterstudy-file-attachment__image_preview">
						<div class="masterstudy-file-attachment__wrapper">
							<div class="masterstudy-file-attachment__title-wrapper">
								<span class="masterstudy-file-attachment__title"></span>
							</div>
							<span class="masterstudy-file-attachment__size"></span>
							<a class="masterstudy-file-attachment__delete" href="#" data-id=""></a>
						</div>
					</div>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/audio-player',
						array(
							'hidden' => true,
						)
					);
					STM_LMS_Templates::show_lms_template(
						'components/video-player',
						array(
							'hidden' => true,
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Fires for each custom column of a specific post type in the Posts list table.
 *
 * @param  array   $columns       - The name of the column to display.
 * @param  integer $assignment_id - The current post/assignment ID.
 * @return void
 */
function stm_lms_student_assignments_column_fields( $columns, $assignment_id ) {
	switch ( $columns ) {
		case 'lms_student':
			$student_id = get_post_meta( $assignment_id, 'student_id', true );
			$student    = STM_LMS_User::get_current_user( $student_id );

			echo '<a href="' . esc_url( add_query_arg( 'lms_student_id', $student_id ) ) . '">' . esc_html( $student['login'] ) . '</a>';
			break;
		case 'lms_course':
			$course_id = get_post_meta( $assignment_id, 'course_id', true );
			if ( empty( $course_id ) ) {
				echo esc_html__( 'No linked course', 'masterstudy-lms-learning-management-system-pro' );
			} else {
				echo '<a href="' . esc_url( add_query_arg( 'lms_course_id', $course_id ) ) . '">' . esc_html( get_the_title( $course_id ) ) . '</a>';
			}
			break;
		case 'lms_date':
			$date_format = get_option( 'date_format', 'd.m.Y' );

			echo esc_html( get_the_date( $date_format, $assignment_id ) );
			break;
		case 'lms_attempt':
			echo esc_html( get_post_meta( $assignment_id, 'try_num', true ) );
			break;
		case 'lms_status':
			echo wp_kses_post( ( new AssignmentStudentRepository() )->get_user_assignment_status_html( $assignment_id ) );
			break;
		case 'lms_review':
			$link = admin_url( 'post.php?post=' . $assignment_id . '&action=edit' );
			echo '<a class="button action" href="' . esc_url( $link ) . '">' . esc_html__( 'Review', 'masterstudy-lms-learning-management-system-pro' ) . '</a>';
			break;
	}
}
add_action( 'manage_stm-user-assignment_posts_custom_column', 'stm_lms_student_assignments_column_fields', 10, 2 );

function stm_lms_student_assignments_reset_filters( $post_type ) {
	if ( PostType::USER_ASSIGNMENT === $post_type ) {
		$students = AssignmentStudentRepository::get_all_students();
		if ( ! empty( $students ) ) {
			?>
			<select name="lms_student_id">
				<option value="">
					<?php echo esc_html__( 'All students', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</option>
				<?php foreach ( $students as $student_id ) : ?>
					<?php
					$student = STM_LMS_User::get_current_user( $student_id );
					if ( ! empty( $student['login'] ) ) {
						?>
						<option value="<?php echo esc_attr( $student_id ); ?>" <?php selected( $_GET['lms_student_id'] ?? '', $student_id ); // phpcs:ignore ?>>
							<?php echo esc_html( $student['login'] ); ?>
						</option>
						<?php
					}
					endforeach;
				?>
			</select>
			<?php
		}

		global $wpdb;

		$courses = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT c.ID, c.post_title
				FROM {$wpdb->posts} c
				WHERE c.post_type = %s
				  AND c.post_status = %s
				  AND EXISTS (
					SELECT 1
					FROM {$wpdb->postmeta} pm
					INNER JOIN {$wpdb->posts} ua
					  ON ua.ID = pm.post_id
					WHERE pm.meta_key = %s
					  AND pm.meta_value = c.ID
					  AND ua.post_type = %s
					  AND ua.post_status <> %s
				  )
				ORDER BY c.post_title ASC
				",
				PostType::COURSE,
				'publish',
				'course_id',
				PostType::USER_ASSIGNMENT,
				'trash'
			)
		);

		if ( ! empty( $courses ) ) {
			?>
			<select name="lms_course_id">
				<option value="">
					<?php echo esc_html__( 'All courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</option>
				<?php foreach ( $courses as $course ) : ?>
					<option value="<?php echo esc_attr( $course->ID ); ?>" <?php selected( $_GET['lms_course_id'] ?? '', $course->ID ); // phpcs:ignore ?>>
						<?php echo esc_html( $course->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php
		}
	}
}
add_action( 'restrict_manage_posts', 'stm_lms_student_assignments_reset_filters' );

function stm_lms_student_assignments_assignment_saved( $post_id, $post, $update ) {
	if ( PostType::USER_ASSIGNMENT !== $post->post_type ) {
		return;
	}

	$assignment_repository = new AssignmentStudentRepository();

	$status_meta    = get_post_meta( $post_id, 'status', true );
	$post_status    = ! empty( $status_meta ) ? $status_meta : get_post_status( $post_id );
	$current_status = $assignment_repository->get_status( $post_id );
	$status         = in_array( $post_status, array( 'pending', 'passed', 'draft', 'not_passed' ), true )
		? $post_status
		: $current_status;

	/* Update Assignment Attachments */
	if ( isset( $_POST['stm_lms_assignment_instructor_review'] ) && wp_verify_nonce( $_POST['stm_lms_assignment_instructor_review'], 'stm_lms_assignment_instructor_review_save' ) ) {
		$status                 = sanitize_text_field( $_POST['status'] ?? $status );
		$instructor_attachments = get_post_meta( $post_id, 'instructor_attachments', true );
		$attachment_ids         = explode( ',', sanitize_text_field( $_POST['instructor_attachments'] ) );
		$instructor_attachments = ! empty( $instructor_attachments ) ? array_merge( $instructor_attachments, $attachment_ids ) : $attachment_ids;
		$instructor_attachments = array_map( 'intval', array_filter( $instructor_attachments ) );

		update_post_meta( $post_id, 'instructor_attachments', array_unique( $instructor_attachments ) );
		update_post_meta( $post_id, 'editor_comment', wp_kses_post( $_POST['editor_comment'] ) );
	}

	// Update Assignment Grade
	if ( is_ms_lms_addon_enabled( 'grades' ) ) {
		$status = masterstudy_lms_update_user_assignment_grade( $post_id, $_POST, $status );
	}

	if ( $update ) {
		$assignment_repository->update_status(
			$post_id,
			! empty( $status ) ? $status : 'pending'
		);
	} elseif ( ! empty( $_GET['start_assignment'] ) ) {
		$assignment_repository->add_assignment(
			get_current_user_id(),
			intval( $_GET['course_id'] ?? 0 ),
			intval( $_GET['start_assignment'] ),
			$post_id,
			$status
		);
	}

	// Send email notification to student
	if ( ! empty( $status ) && $current_status !== $status && ( 'draft' !== $current_status && ! empty( $current_status ) ) ) {
		$student = STM_LMS_User::get_current_user( get_post_meta( $post_id, 'student_id', true ) );
		$message = esc_html__( 'Your assignment has been checked', 'masterstudy-lms-learning-management-system-pro' );

		$assignment_id = get_post_meta( $post_id, 'assignment_id', true );
		$student_id    = get_post_meta( $post_id, 'student_id', true );
		$course_id     = get_post_meta( $post_id, 'course_id', true );
		$grade         = ( new AssignmentStudentRepository() )->get_grade( $post_id );

		$last_attempt    = ( new AssignmentStudentRepository() )->get_last_attempt( $course_id, $assignment_id, $student_id );
		$status          = $last_attempt['status'] ?? false;
		$status_messages = array(
			'passed'     => __( 'You passed assignment.', 'masterstudy-lms-learning-management-system-pro' ),
			'pending'    => __( 'Your assignment pending review.', 'masterstudy-lms-learning-management-system-pro' ),
			'not_passed' => __( 'You failed assignment.', 'masterstudy-lms-learning-management-system-pro' ),
		);

		$email_data = array(
			'message'            => $message,
			'blog_name'          => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
			'site_url'           => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
			'assignment_result'  => $status_messages[ $status ],
			'assignment_comment' => get_post_meta( $post_id, 'editor_comment', true ),
			'assignment_mark'    => is_ms_lms_addon_enabled( 'grades' ) ? GradeDisplay::get_instance()->simple_render( $grade ) : $grade,
			'assignment_url'     => \MS_LMS_Email_Template_Helpers::link( STM_LMS_Lesson::get_lesson_url( $course_id, $assignment_id ) ),
			'assignment_title'   => get_the_title( $assignment_id ),
			'course_title'       => get_the_title( $course_id ),
			'user_login'         => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $student_id ),
			'instructor_name'    => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( \STM_LMS_Helpers::masterstudy_lms_get_post_author_id_by_post_id( $assignment_id ) ),
			'date'               => gmdate( 'Y-m-d H:i:s' ),
		);

		STM_LMS_Helpers::send_email(
			$student['email'],
			esc_html__( 'Assignment status change.', 'masterstudy-lms-learning-management-system-pro' ),
			$message,
			'stm_lms_assignment_checked',
			$email_data
		);
	}

	/* Update Course Progress */
	$student_id = get_post_meta( $post_id, 'student_id', true );
	$course_id  = get_post_meta( $post_id, 'course_id', true );
	STM_LMS_Course::update_course_progress( $student_id, $course_id );
}
add_action( 'save_post', 'stm_lms_student_assignments_assignment_saved', 99999, 3 );

function stm_lms_student_assignments_assignment_related_posts( $query ) {
	global $pagenow;

	// phpcs:ignore WordPress.Security.NonceVerification
	$is_user_assignment = PostType::USER_ASSIGNMENT === ( $_GET['post_type'] ?? '' );

	// phpcs:ignore WordPress.Security.NonceVerification
	if ( is_admin() && $is_user_assignment && 'edit.php' === $pagenow && ! empty( $_GET['assignment_id'] ) ) {
		$query->set( 'meta_key', 'assignment_id' );
		$query->set( 'meta_value', $_GET['assignment_id'] ); // phpcs:ignore WordPress.Security.NonceVerification
	}
}
add_action( 'pre_get_posts', 'stm_lms_student_assignments_assignment_related_posts' );

function stm_lms_change_assignments_label() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $_GET['post_type'] ) && ! empty( $_GET['assignment_id'] ) && PostType::USER_ASSIGNMENT === $_GET['post_type'] ) {
		global $wp_post_types;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$wp_post_types[ PostType::USER_ASSIGNMENT ]->labels->name .= ': ' . get_the_title( intval( $_GET['assignment_id'] ) );
	}
}
add_action( 'admin_init', 'stm_lms_change_assignments_label' );

// Delete user assignment records when user assignment is deleted
function stm_lms_delete_user_assignments( $post_id ) {
	if ( PostType::USER_ASSIGNMENT !== get_post_type( $post_id ) ) {
		return;
	}

	global $wpdb;

	$wpdb->delete(
		$wpdb->prefix . 'stm_lms_user_assignments',
		array(
			'user_assignment_id' => $post_id,
		),
		array( '%d' )
	);
}
add_action( 'before_delete_post', 'stm_lms_delete_user_assignments' );

// Restore user assignment records when user assignment is restored
function stm_lms_restore_user_assignments( $post_id ) {
	if ( PostType::USER_ASSIGNMENT === get_post_type( $post_id ) ) {
		return;
	}

	$assignment_repository = new AssignmentStudentRepository();

	$assignment_repository->update_status(
		$post_id,
		$assignment_repository->get_old_status( $post_id )
	);
}
add_action( 'untrash_post', 'stm_lms_restore_user_assignments' );

function masterstudy_lms_hide_menu_for_instructors() {
	if ( STM_LMS_Instructor::is_instructor() ) {
		remove_menu_page( 'edit.php?post_type=stm-user-assignment' );
	}
}

add_action( 'admin_menu', 'masterstudy_lms_hide_menu_for_instructors', 100 );
