<?php
function masterstudy_lms_coming_soon_course_available( $course_id ) {
	masterstudy_lms_coming_soon_notify_students_by_course_id( $course_id, 'notify' );
	update_post_meta( $course_id, 'coming_soon_status', false );
	update_post_meta( $course_id, 'coming_soon_show_course_price', true );
	update_post_meta( $course_id, 'coming_soon_show_course_details', true );
	update_post_meta( $course_id, 'coming_soon_preordering', true );
}
add_action( 'masterstudy_lms_coming_soon_course', 'masterstudy_lms_coming_soon_course_available' );

function meta_fields_saving( $course_id, $meta_fields ) {
	if ( ! $meta_fields['coming_soon_status'] || ! $meta_fields['coming_soon_date'] || ! $meta_fields['coming_soon_time'] ) {
		if ( get_post_meta( $course_id, 'coming_soon_status', false ) && ! $meta_fields['coming_soon_status'] ) {
			update_post_meta( $course_id, 'coming_soon_status', false );
		}
		return;
	}
	// checking pre_sale option for upcoming course and notify students
	$preodering = get_post_meta( $course_id, 'coming_soon_preordering', true );
	if ( isset( $meta_fields['coming_soon_preordering'] ) && $meta_fields['coming_soon_preordering'] !== $preodering && $meta_fields['coming_soon_preordering'] ) {
		masterstudy_lms_coming_soon_notify_students_by_course_id( $course_id, 'preordering' );
	}

	if ( isset( $meta_fields['coming_soon_date'] ) && isset( $meta_fields['coming_soon_time'] ) ) {
		$start_date = (int) get_post_meta( $course_id, 'coming_soon_date', true );
		$start_time = get_post_meta( $course_id, 'coming_soon_time', true );

		$offset            = ( get_option( 'gmt_offset' ) * 60 * 60 );
		$course_start_date = strtotime( 'today', ( $meta_fields['coming_soon_date'] / 1000 ) ) - $offset;
		if ( ! empty( $meta_fields['coming_soon_time'] ) ) {
			$time = explode( ':', $meta_fields['coming_soon_time'] );
			if ( is_array( $time ) && count( $time ) === 2 ) {
				$course_start_time = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $course_start_date );
			}
		}

		// upcoming course date changed or not
		if ( $start_date !== (int) $meta_fields['coming_soon_date'] || $meta_fields['coming_soon_time'] !== $start_time ) {
			masterstudy_lms_coming_soon_notify_students_by_course_id( $course_id, 'date_changed' );

			$scheduled_timestamp = wp_next_scheduled( 'masterstudy_lms_coming_soon_course', array( $course_id ) );
			if ( $scheduled_timestamp ) {
				wp_unschedule_event( $scheduled_timestamp, 'masterstudy_lms_coming_soon_course', array( $course_id ) );
			}
			wp_schedule_single_event( $course_start_time, 'masterstudy_lms_coming_soon_course', array( $course_id ) );
		}
	}
}
add_action( 'masterstudy_lms_course_coming_soon_before_save', 'meta_fields_saving', 10, 2 );
