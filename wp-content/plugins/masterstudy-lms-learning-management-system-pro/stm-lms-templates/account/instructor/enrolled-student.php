<?php
if (
	( ! STM_LMS_Instructor::instructor_show_list_students() && ! is_admin() ) ||
	( ! STM_LMS_Instructor::is_instructor() ) ||
	( absint( get_query_var( 'student_id' ) ) && ! current_user_can( 'administrator' ) && ! masterstudy_lms_is_instructors_student( absint( get_query_var( 'student_id' ) ), get_current_user_id() ) )
) {
	STM_LMS_User::js_redirect( STM_LMS_User::login_page_url() );
	die;
}

STM_LMS_Templates::show_lms_template( 'analytics/student' );
