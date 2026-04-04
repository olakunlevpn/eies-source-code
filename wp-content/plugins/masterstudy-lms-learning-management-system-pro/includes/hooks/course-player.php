<?php

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;

/**
 * Course Player Template Hooks
 */

/* Get google meet lesson data */
function masterstudy_course_player_lesson_google_data( $item_id, $post_id ) {
	return array(
		'meet_started' => masterstudy_lms_is_google_meet_started( $item_id ),
		'description'  => get_post_meta( $item_id, 'stm_gma_summary', true ),
		'start_date'   => masterstudy_lms_get_google_meet_date_time( $item_id, true ),
		'end_date'     => masterstudy_lms_get_google_meet_date_time( $item_id, false ),
		'start_time'   => masterstudy_lms_google_meet_start_time( $item_id ),
		'author_email' => get_the_author_meta( 'user_email', get_post_field( 'post_author', $post_id ) ),
		'meet_url'     => get_post_meta( $item_id, 'google_meet_link', true ),
	);
}
add_filter( 'masterstudy_course_player_lesson_google_data', 'masterstudy_course_player_lesson_google_data', 10, 2 );

/* Get stream lesson data */
function masterstudy_course_player_lesson_stream_data( $item_id ) {
	$data                = array(
		'stream_url'     => get_post_meta( $item_id, 'lesson_stream_url', true ),
		'stream_started' => STM_LMS_Live_Streams::is_stream_started( $item_id ),
		'start_date'     => STM_LMS_Live_Streams::get_stream_date( $item_id, true ),
		'end_date'       => STM_LMS_Live_Streams::get_stream_date( $item_id, false ),
		'start_time'     => STM_LMS_Live_Streams::stream_start_time( $item_id ),
		'content'        => masterstudy_course_player_get_content( $item_id ),
	);
	$data['video_idx']   = apply_filters( 'ms_plugin_get_youtube_idx', $data['stream_url'] );
	$data['youtube_url'] = 'https://www.youtube.com/embed/' . $data['video_idx'] . '?&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1';

	if ( isset( $_SERVER['SERVER_NAME'] ) ) {
		$data['youtube_chat_url'] = 'https://www.youtube.com/live_chat?v=' . $data['video_idx'] . '&embed_domain=' . str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ) . '&dark_theme=1';
	}

	return $data;
}
add_filter( 'masterstudy_course_player_lesson_stream_data', 'masterstudy_course_player_lesson_stream_data' );

/* Get assignment data */
function masterstudy_course_player_assignment_data( $item_id, $data ) {
	if ( ! is_user_logged_in() ) {
		$data['current_template'] = false;

		return $data;
	}

	$last_attempt    = ( new AssignmentStudentRepository() )->get_last_attempt( $data['post_id'], $item_id, $data['user_id'] );
	$status          = $last_attempt['status'] ?? false;
	$status_messages = masterstudy_lms_get_assignment_status_translations();

	$data = array_merge(
		$data,
		array(
			'current_template' => $status,
			'user_assignment'  => ! empty( $last_attempt['user_assignment_id'] ) ? get_post( $last_attempt['user_assignment_id'] ) : null,
			'status_message'   => $status_messages[ $status ] ?? '',
			'content'          => masterstudy_course_player_get_content( $item_id, true ),
			'actual_link'      => STM_LMS_Assignments::get_current_url(),
			'show_emoji'       => $data['settings']['assignments_quiz_result_emoji_show'] ?? false,
		)
	);

	if ( ! empty( $data['current_template'] ) ) {
		$assignment_settings   = get_option( 'stm_lms_assignments_settings' );
		$emoji_type            = 'not_passed' === $data['current_template'] ? 'assignments_quiz_failed_emoji' : 'assignments_quiz_passed_emoji';
		$data['emoji_name']    = $data['settings'][ $emoji_type ] ?? '';
		$data['assignment_id'] = $last_attempt['user_assignment_id'] ?? null;
		$data['editor_id']     = "masterstudy_course_player_assignments__{$data['assignment_id']}";
		$data['retake']        = STM_LMS_Assignments::get_attempts( $data['post_id'], $item_id, $data['user_id'] );

		if ( $assignment_settings['assignments_allow_upload_attachments'] ?? true ) {
			$data['student_attachments']    = STM_LMS_Assignments::get_draft_attachments( $data['assignment_id'], 'student_attachments' );
			$data['instructor_attachments'] = STM_LMS_Assignments::get_draft_attachments( $data['assignment_id'], 'instructor_attachments' );
		}
	}

	return $data;
}
add_filter( 'masterstudy_course_player_assignment_data', 'masterstudy_course_player_assignment_data', 10, 2 );

function masterstudy_course_player_get_content( $post_id, $str_replace = false ) {
	$post    = get_post( $post_id );
	$content = '';

	if ( $post ) {
		$content = $post->post_content;
	}

	return $str_replace
		? str_replace( '../../', site_url() . '/', stm_lms_filtered_output( $content ) )
		: $content;
}
