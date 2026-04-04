<?php

function masterstudy_lms_coming_soon_start_time( $course_id ) {
	$start_date = get_post_meta( $course_id, 'coming_soon_date', true );
	$start_time = get_post_meta( $course_id, 'coming_soon_time', true );

	if ( empty( $start_date ) ) {
		return '';
	}

	$offset = ( get_option( 'gmt_offset' ) * 60 * 60 );
	$result = strtotime( 'today', ( $start_date / 1000 ) ) - $offset;

	if ( ! empty( $start_time ) ) {
		$time = explode( ':', $start_time );
		if ( is_array( $time ) && count( $time ) === 2 ) {
			$result = strtotime( "+{$time[0]} hours +{$time[1]} minutes", $result );
		}
	}

	return $result;
}

function masterstudy_lms_coming_soon_notify_students_by_course_id( $course_id, $key ) {
	$coming_soon_emails = get_post_meta( $course_id, 'coming_soon_student_emails', true );
	$email_manager      = \MasterStudy\Lms\Pro\addons\email_manager\EmailManagerSettings::get_all();
	if ( ! empty( $email_manager ) && ! empty( $coming_soon_emails ) ) {
		$availability = $email_manager['masterstudy_lms_coming_soon_availability_enable'];
		$preordering  = $email_manager['masterstudy_lms_coming_soon_pre_sale_enable'];
		$start_date   = $email_manager['masterstudy_lms_coming_soon_start_date_enable'];

		if ( $availability && 'notify' === $key ) {
			$subject = $email_manager['masterstudy_lms_coming_soon_availability_subject'];
			$message = $email_manager['masterstudy_lms_coming_soon_availability'];
		} elseif ( $preordering && 'preordering' === $key ) {
			$subject = $email_manager['masterstudy_lms_coming_soon_pre_sale_subject'];
			$message = $email_manager['masterstudy_lms_coming_soon_pre_sale'];
		} elseif ( $start_date && 'date_changed' === $key ) {
			$subject = $email_manager['masterstudy_lms_coming_soon_start_date_subject'];
			$message = $email_manager['masterstudy_lms_coming_soon_start_date'];
		}

		if ( isset( $message ) ) {
			foreach ( $coming_soon_emails as $email ) {
				$email_data = array(
					'user_login'      => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( \STM_LMS_Helpers::masterstudy_lms_get_user_by_email( $email ) ),
					'instructor_name' => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( \STM_LMS_Helpers::masterstudy_lms_get_post_author_id_by_post_id( $course_id ) ),
					'upcoming_date'   => gmdate( 'Y-m-d H:i:s', masterstudy_lms_coming_soon_start_time( $course_id ) ),
					'course_title'    => get_the_title( $course_id ),
					'course_url'      => \MS_LMS_Email_Template_Helpers::link( get_the_permalink( $course_id ) ),
					'blog_name'       => STM_LMS_Helpers::masterstudy_lms_get_site_name(),
					'site_url'        => \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() ),
					'date'            => gmdate( 'Y-m-d H:i:s' ),
				);

				$message = \MS_LMS_Email_Template_Helpers::render( $message, $email_data );
				$subject = \MS_LMS_Email_Template_Helpers::render( $subject, $email_data );

				STM_LMS_Helpers::send_email(
					$email['email'],
					$subject,
					$message,
					'stm_lms_filter_email_data',
					$email_data
				);
			}
		}
	}
}
