<?php
// phpcs:ignoreFile

$email_templates = array(
// Emails to Admin
	'stm_lms_new_user_register_on_site'               => array(
		'section' => 'emails_to_admin',
		'notice'  => esc_html__(
			'New User Signed Up',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'New User Registered',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when there is a new successfully user registration. It has been sent after the user activates his account if Email Confirmation is enabled.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'A new user has just registered on the site. <br> Here are the details: <br>
			Name: {{user_login}} <br>
			Email: {{user_email}} <br>
			Registration Date: {{registration_date}} <br>
			Please welcome our new member!',
		'vars'    => array(
			'user_login'        => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_email'        => esc_html__(
				'User email',
				'masterstudy-lms-learning-management-system-pro'
			),
			'registration_date' => esc_html__(
				'Registration date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'         => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'          => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_course_added'                            => array(
		'section' => 'emails_to_admin',
		'hint'    => esc_html__( 'Send when an instructor adds a new course.', 'masterstudy-lms-learning-management-system-pro' ),
		'notice'  => esc_html__( 'New Course Action by Instructor', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Course added', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => esc_html__( 'Course {{course_title}} {{action}} by instructor, your ({{user_login}}). Please review this information from the admin Dashboard', 'masterstudy-lms-learning-management-system-pro' ),
		'vars'    => array(
			'action'          => esc_html__( 'Added or updated action made by instructor', 'masterstudy-lms-learning-management-system-pro' ),
			'user_login'      => esc_html__( 'Instructor login', 'masterstudy-lms-learning-management-system-pro' ),
			'course_title'    => esc_html__( 'Course name', 'masterstudy-lms-learning-management-system-pro' ),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'dashboard_url'   => esc_html__(
				'Dashboard URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_edit_url' => esc_html__(
				'Course edit URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_course_added_to_user'                    => array(
		'section' => 'emails_to_admin',
		'notice'  => esc_html__(
			'Course Assigned to Student',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a course is added to a user.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Student enrolled in {{course_title}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => 'Hello admin, <br>
		This is to notify you that a student account {{login}} has been successfully enrolled in the course {{course_title}}.<br>
		Here are the event details:<br>
		<b>Student</b>: {{login}}<br>
		<b>Course</b>: {{course_title}} <b>url</b>: {{course_url}}<br>
		<b>Date</b>: {{date}}<br>
		If everything is OK, no further actions are required.',
		'vars'    => array(
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'login'        => esc_html__(
				'Login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'   => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	),

	'stm_lms_new_order'                               => array(
		'section'               => 'emails_to_admin',
		'notice'                => esc_html__(
			'New Order Notification',
			'masterstudy-lms-learning-management-system-pro'
		),
		'title'                 => esc_html__( 'New order', 'masterstudy-lms-learning-management-system-pro' ),
		'subject'               => esc_html__(
			'New order',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a new order is placed.', 'masterstudy-lms-learning-management-system-pro' ),
		'date_order_render'     => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'order_order_render'    => esc_html__( 'Order ID', 'masterstudy-lms-learning-management-system-pro' ),
		'title_order_render'    => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
		'items_order_render'    => esc_html__( 'Items list', 'masterstudy-lms-learning-management-system-pro' ),
		'customer_order_render' => esc_html__( 'User section', 'masterstudy-lms-learning-management-system-pro' ),
		'button_order_render'   => esc_html__( 'Button', 'masterstudy-lms-learning-management-system-pro' ),
		'message'               => 'An order has been placed on your site from the user {{user_login}}. Log in to your admin dashboard to view the sale and check your earnings.',
		'vars'                  => array(
			'user_login'    => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'student_email' => esc_html__(
				'Student Email',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'     => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'      => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'          => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_membership_course_available_for_admin'   => array(
		'section' => 'emails_to_admin',
		'notice'  => esc_html__( 'Course Assigned to Student with Membership', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( '{{user_login}} Added to {{course_title}} via {{membership_plan}}', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'A student {{user_login}} has been added to the course {{course_title}} under the {{membership_plan}} membership plan.<br>
		<b>Course URL</b>: {{course_url}}<br>
		<b>Enrollment Date</b>: {{date}}',
		'hint'    => esc_html__( 'Send when a course is added via membership plan.', 'masterstudy-lms-learning-management-system-pro' ),
		'vars'    => array(
			'course_title'    => esc_html__( 'Course title', 'masterstudy-lms-learning-management-system-pro' ),
			'membership_plan' => esc_html__( 'Plan name', 'masterstudy-lms-learning-management-system-pro' ),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'      => esc_html__(
				'User Login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'student_email'   => esc_html__(
				'Student Email',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_become_instructor_email'                 => array(
		'section' => 'emails_to_admin',
		'notice'  => esc_html__(
			'Instructor Application Submitted',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a user submits the Become an Instructor form.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'New Instructor Application',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => esc_html__( 'You have received a new instructor application from ', 'masterstudy-lms-learning-management-system-pro' ) . ' {{user_login}}, <br/>' . // phpcs:disable
		             esc_html__( 'Here are the details:', 'masterstudy-lms-learning-management-system-pro' ) . ' <br/>' .
		             '<b>' . esc_html__( 'Name: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{user_login}} <br>' .
		             '<b>' . esc_html__( 'ID: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{user_id}} <br>' .
		             '<b>' . esc_html__( 'Email: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{user_email}} <br>' .
		             '<b>' . esc_html__( 'Degree: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{degree}} <br>' .
		             '<b>' . esc_html__( 'Expertize: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{expertize}} <br>' .
		             '<b>' . esc_html__( 'Application Date: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{date}} <br><br>' .
		             esc_html__( 'Please review the application at your earliest convenience.', 'masterstudy-lms-learning-management-system-pro' ) . '</a> <br/><br/>',
		// phpcs:enable
		'vars'    => array(
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_id'    => esc_html__(
				'User ID',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_email' => esc_html__(
				'User email',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'degree'     => esc_html__(
				'Degree',
				'masterstudy-lms-learning-management-system-pro'
			),
			'expertize'  => esc_html__(
				'Expertize',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_enterprise'                              => array(
		'section' => 'emails_to_admin',
		'notice'  => esc_html__(
			'New Enterprise Request',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'New Enterprise Inquiry',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a user submits an enterprise request form.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => esc_html__( 'You have received a new enterprise inquiry from the "For Enterprise" form.', 'masterstudy-lms-learning-management-system-pro' ) . ' <br/>' . // phpcs:disable
		             esc_html__( 'Here are the details:', 'masterstudy-lms-learning-management-system-pro' ) . ' <br/>' .
		             '<b>' . esc_html__( 'Name: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{name}} <br>' .
		             '<b>' . esc_html__( 'Email: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{email}} <br>' .
		             '<b>' . esc_html__( 'Message: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{text}} <br>' .
		             '<b>' . esc_html__( 'Submission Date: ', 'masterstudy-lms-learning-management-system-pro' ) . '</b>' . ' {{date}} <br><br/>' .
		             esc_html__( 'Please review this inquiry and follow up as needed.', 'masterstudy-lms-learning-management-system-pro' ) . '</a> <br/>',
		// phpcs:enable
		'vars'    => array(
			'name'      => esc_html__( 'Name', 'masterstudy-lms-learning-management-system-pro' ),
			'email'     => esc_html__(
				'Email',
				'masterstudy-lms-learning-management-system-pro'
			),
			'text'      => esc_html__( 'Text', 'masterstudy-lms-learning-management-system-pro' ),
			'date'      => esc_html__( 'Submission Date', 'masterstudy-lms-learning-management-system-pro' ),
			'blog_name' => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'  => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_course_published'                        => array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'Instructor\'s Course Published',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a course created by the instructor is published.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Course published',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => esc_html__(
			'Your course - {{course_title}} was approved, and is now live on the website',
			'masterstudy-lms-learning-management-system-pro'
		),
		'vars'    => array(
			'course_title'    => esc_html__(
				'Course Title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_edit_url' => esc_html__(
				'Course edit URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_lesson_comment'                          => array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'New Q&A Message from Student',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a new comment is posted in the lesson\'s Discussion section by a student. ', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => 'New Comment on {{lesson_title}} in {{course_title}}',
		'message' => 'Hi {{instructor_name}},<br>
				{{user_login}} has left a new comment on the lesson {{lesson_title}} in your course {{course_title}}. <br>
				You can view and respond to the comment here: {{lesson_url}}. <br>
				Thank you for engaging with your students!',
		'vars'    => array(
			'user_login'      => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'comment_content' => esc_html__(
				'Comment content',
				'masterstudy-lms-learning-management-system-pro'
			),
			'lesson_title'    => esc_html__(
				'Lesson title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title'    => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_lesson_qeustion_ask_answer'              => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Q&A Message Answered',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'New Reply to Your Comment in {{lesson_title}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when there is a new reply to the question in the lesson\'s Discussion section', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'Hi {{user_login}} <br>
			You have received a new reply to your comment in the lesson {{lesson_title}} of the course {{course_title}}.<br>
			{{comment_content}}<br>
			You can view the full conversation and reply here: <a href="{{lesson_url}}">Lesson URL</a>. <br>
			Keep the discussion going!'
		),
		'vars'    => array(
			'user_login'      => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'comment_content' => esc_html__(
				'Comment content',
				'masterstudy-lms-learning-management-system-pro'
			),
			'lesson_title'    => esc_html__(
				'Lesson title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'lesson_url'      => esc_html__(
				'Lesson URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title'    => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_account_premoderation'                   => array(
		'section' => 'system_notifications',
		'notice'  => esc_html__(
			'Account Approval Required',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'Activate your account',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Sends email notifications with the special URL-link to users to activate their newly registered accounts. Works  when Email Confirmation is enabled.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => esc_html__( 'Hi', 'masterstudy-lms-learning-management-system-pro' ) . ' {{user_login}},<br/>' .
		             esc_html__( 'Welcome to', 'masterstudy-lms-learning-management-system-pro' ) . ' {{blog_name}}! ' .
		             esc_html__( 'To start using your account, please activate it by clicking the link below:', 'masterstudy-lms-learning-management-system-pro' ) . '<br/>' .
		             esc_html__( 'Activation Link:', 'masterstudy-lms-learning-management-system-pro' ) . ' <a href="{{reset_url}}">' .
		             esc_html__( 'Activate here', 'masterstudy-lms-learning-management-system-pro' ) . '</a> <br/><br/>' .
		             esc_html__( 'We look forward to seeing you on', 'masterstudy-lms-learning-management-system-pro' ) . ' {{blog_name}}!',
		'vars'    => array(
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'reset_url'  => esc_html__(
				'Reset URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_user_registered_on_site'                 => array(
		'section' => 'system_notifications',
		'notice'  => esc_html__(
			'Welcome Email After Registration',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Sends a welcome message to users  after the succesfull registration on your platform.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'You have successfully registered on the website.',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => 'Hi {{user_login}},<br> Welcome to {{blog_name}}! Your registration was successful.<br> You can now log in to your account using the following link: <br>Login URL: <a href="{{login_url}}">Login url</a> <br><br>We are thrilled to have you on board!',
		'vars'    => array(
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_id'    => esc_html__(
				'User ID',
				'masterstudy-lms-learning-management-system-pro'
			),
			'login_url'  => esc_html__(
				'Login URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_email_user_reset_password'               => array(
		'section' => 'system_notifications',
		'notice'  => esc_html__(
			'Password Reset Request',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Sends when user clicks on the Forgot Password link on the sign-in/up page. ', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Password Reset Request',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => 'Dear  {{user_login}},<br> There has been a request to reset your password for your account on {{blog_name}}.
					<br> To reset your password and set a new one, click on the link below: <br>
					<a href="{{reset_url}}" target="_blank">Reset url</a>
					<br>If you did not request this change, please ignore this email.',
		'vars'    => array(
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'reset_url'  => esc_html__(
				'Reset URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_user_added_via_manage_students'          => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Students Manually Enrolled (via Manage Students)',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when student has been added to the course using Manage Students tab by admin/instructor.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Welcome to {{blog_name}}. Your student account is ready',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => '
					Hello {{username}},
					Welcome to {{blog_name}}<br><br>
					Your instructor has created an account for you so you can access your courses and start learning.<br>
					Here are your login details:<br>
					<b>Username</b>: {{username}}<br>
					<b>Password</b>: {{password}}<br>
					<b>Login page</b>: {{login_url}}<br>
					To get started, visit the login page and sign in using the credentials above. For security reasons, we recommend changing your password after your first login.<br>
					If you have any questions or experience issues accessing your account, please contact your instructor or the site administrator.<br>
					We are glad to have you with us and wish you a great learning experience.<br><br>
					Best regards,<br>
					The {{blog_name}} Team',
		'vars'    => array(
			'username'  => esc_html__(
				'Username',
				'masterstudy-lms-learning-management-system-pro'
			),
			'password'  => esc_html__(
				'Password',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'  => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name' => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'      => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'login_url' => esc_html__(
				'Login URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_password_change'                         => array(
		'section' => 'system_notifications',
		'notice'  => esc_html__(
			'Password Successfully Changed',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'Password change',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Sends when user changed his password.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Password changed successfully.',
		'vars'    => array(
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_new_order_instructor'                    => array(
		'section'               => 'emails_to_instructors',
		'notice'                => esc_html__(
			'New Order Notification',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send to instructor when students buys his course or course bundle succesfully.', 'masterstudy-lms-learning-management-system-pro' ),
		'title'                 => esc_html__( 'You made a Sale!', 'masterstudy-lms-learning-management-system-pro' ),
		'subject'               => esc_html__(
			'You made a Sale!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'date_order_render'     => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'order_order_render'    => esc_html__( 'Order ID', 'masterstudy-lms-learning-management-system-pro' ),
		'title_order_render'    => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
		'items_order_render'    => esc_html__( 'Items list', 'masterstudy-lms-learning-management-system-pro' ),
		'customer_order_render' => esc_html__( 'User section', 'masterstudy-lms-learning-management-system-pro' ),
		'button_order_render'   => esc_html__( 'Button', 'masterstudy-lms-learning-management-system-pro' ),
		'message'               => 'Congratulations! A new purchase has been made. Open your instructor dashboard to check the new Order from the user {{user_login}}.',
		'vars'                  => array(
			'user_login'    => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'     => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'      => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'student_email' => esc_html__(
				'Student email',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'          => esc_html__(
				'Purchase date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_new_order_accepted'                      => array(
		'section'             => 'emails_to_students',
		'notice'              => esc_html__(
			'Order Notification',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a user places a new order.', 'masterstudy-lms-learning-management-system-pro' ),
		'title'               => esc_html__( 'Thank you for purchase!', 'masterstudy-lms-learning-management-system-pro' ),
		'subject'             => esc_html__(
			'Thank you for purchase!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'date_order_render'   => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'order_order_render'  => esc_html__( 'Order ID', 'masterstudy-lms-learning-management-system-pro' ),
		'title_order_render'  => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
		'items_order_render'  => esc_html__( 'Items list', 'masterstudy-lms-learning-management-system-pro' ),
		'button_order_render' => esc_html__( 'Button', 'masterstudy-lms-learning-management-system-pro' ),
		'message'             => 'Your access is now ready. Dive in and start your journey toward new skills and achievements today!',
		'vars'                => array(
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_course_available_for_user'               => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Course Assigned',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a course is added to a student\'s account.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'You have Been Added to {{course_title}}!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => 'Hello {{user_login}},<br>
		We\'re excited to inform you that you have added to the course {{course_title}}.<br>
		You can access the course content and resources using the following link:<br>
		<b>Course Link</b>:  {{course_url}} <br>
		Enjoy your learning journey!',
		'vars'    => array(
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'   => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_email_remove_student_from_course'        => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Removed from Course',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when student has been removed from the course.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Your Enrollment Has Been Cancelled',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post( 'Dear {{user_login}},<br>{{instructor_name}} has removed you from the course - {{course_title}}. <br>  Now you don’t have access to the course content.' ),
		'vars'    => array(
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title'    => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'      => esc_html__(
				'Student name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_email_inactivity_students'        => array(
		'section'       => 'emails_to_students',
		'notice'        => esc_html__(
			'Reminder to Inactive Students',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'          => esc_html__( 'Send reminder emails to students who have not accessed the course for a set number of days', 'masterstudy-lms-learning-management-system-pro' ),
		'inactive_days' => esc_html__( 'Send After (Days of Inactivity)', 'masterstudy-lms-learning-management-system-pro' ),
		'subject'       => wp_kses_post(
			'We miss you! It\'s been {{inactivity_period}} days since your last visit.'
		),
		'message'       => wp_kses_post( 'Hey {{user_login}}, <br><br>
			We noticed you\'ve been away from {{blog_name}} for {{inactivity_period}} days. <br>
			Your learning journey is important, and we\'re here to support you every step of the way.  <br>      <br>                                                                                                                                                   
			<b>Here are the details:</b> <br>
			Student Username: {{user_login}} <br>
			Inactive Days: {{inactivity_period}} <br>
			Site Name: {{blog_name}}<br>
			Current Date: {{date}}<br><br>
			<a href="{{login_url}}" target="_blank">Sign in</a> and continue learning where you left off ' ),
		'vars'          => array(
			'inactivity_period' => esc_html__(
				'Inactivity Period',
				'masterstudy-lms-learning-management-system-pro'
			),
			'login_url'         => esc_html__(
				'Login URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'        => esc_html__(
				'Student name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'          => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'              => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'         => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),

	'stm_lms_course_quiz_completed_for_user'          => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Student Completed Quiz',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a student completes a quiz.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'You’ve Completed the Quiz in {{course_title}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'Hi {{user_login}}, <br>
				You’ve just completed the quiz "{{quiz_name}}" in the course "{{course_title}}". Great work!<br>
				<b>Here’s a summary of your attempt:</b><br>
				<ul style="text-align: left;">
					<li> <b>Course:</b> {{course_title}}  </li>
					<li> <b>Quiz:</b> {{quiz_name}}  </li>
					<li> <b>Your Result:</b> {{quiz_result}}  </li>
					<li> <b>Passing Grade:</b> {{quiz_passing_grade}}  </li>
					<li> <b>Completion Date:</b> {{quiz_completion_date}}  </li>
				</ul>
				Keep it up - each step brings you closer to your learning goals!'
		),
		'vars'    => array(
			'user_login'           => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title'         => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_name'            => esc_html__(
				'Quiz name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_result'          => esc_html__(
				'Quiz result',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_passing_grade'   => esc_html__(
				'Quiz passing grade',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_completion_date' => esc_html__(
				'Quiz Completion Date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'            => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'             => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_url'             => esc_html__(
				'Quiz URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'attempt_url'          => esc_html__(
				'Attempt URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'attempt_number'       => esc_html__(
				'Attempt Number',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_course_quiz_completed_for_instructor'    => array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'Quiz Completed',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a student completes a quiz.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'{{user_login}} has completed the quiz {{quiz_name}} in {{course_title}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'We\'re pleased to inform you that {{user_login}} has completed the quiz "{{quiz_name}}" in the course {{course_title}}.<br>
			Quiz Result: {{quiz_result}}<br>
			Completion Date: {{quiz_completion_date}} <br>'
		),
		'vars'    => array(
			'user_login'           => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title'         => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_name'            => esc_html__(
				'Quiz name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_result'          => esc_html__(
				'Quiz result',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_completion_date' => esc_html__(
				'Quiz Completion Date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'            => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'             => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'quiz_url'             => esc_html__(
				'Quiz URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_course_completed_for_user'               => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Course Completed',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when student comletes the course succesfully.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Congratulations on completing {{course_title}}!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'We want to congratulate you on successfully completing the <b>{{course_title}}</b>!
			<br> The link to course: <a href="{{course_url}}" target="_blank">{{course_url}}</a>
			<br> We wish you all the best in your future endeavors.'
		),
		'vars'    => array(
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'   => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_course_completed_for_instructor'         => array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'A Student Completed Course',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when student completes the course succesfully.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'Congratulations! {{user_login}} Completed {{course_title}}!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'{{user_login}} has successfully completed your {{course_title}} with great dedication and achievement.
			<br> The link to course: <a href="{{course_url}}" target="_blank">{{course_url}}</a>
			<br> Your support has made all the difference. Thank you for your dedication to student’s success.'
		),
		'vars'    => array(
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'   => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_course_expiration_for_students'         => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Course Enrollment Expired',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__('Send an email when a student’s course access expires','masterstudy-lms-learning-management-system-pro'),
		'subject' => esc_html__(
			'Your Access to {{course_title}} Has Ended',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'Hi {{user_login}},<br>
			We hope you enjoyed learning with us! Unfortunately, your access to the {{course_title}} course ended on {{course_expiration_date}}.
			Happy learning,<br>
			{{blog_name}}<br>
			{{site_url}}'
		),
		'vars'    => array(
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url' => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_expiration_date'   => esc_html__(
				'Course Expiration Date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_student_lesson_completed_for_instructor' => array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'Lesson completion notification',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__( '{{user_login}} Has Completed a Lesson in {{course_title}}', 'masterstudy-lms-learning-management-system' ),
		'message' => wp_kses_post(
			'We are pleased to inform you that {{user_login}} has completed the lesson {{lesson_title}} in the course {{course_title}}.'
		),
		'hint'    => esc_html__('Automatically notify the instructor when a student completes a lesson', 'masterstudy-lms-learning-management-system-pro'),
		'vars'    => array(
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'lesson_title' => esc_html__(
				'Lesson title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'   => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',

			),
		),
	),
	'stm_lms_membership_course_available_for_user'    => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Course Assigned with Membership', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Welcome to {{course_title}} via {{membership_plan}}', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'    => esc_html__( 'Send when a user receives a course through a membership plan.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'We are excited to inform you that you have been added to the course {{course_title}} under the {{membership_plan}} membership plan. <br>
			You can access the course and start learning using the following link: {{course_title}}',
		'vars'    => array(
			'course_title'    => esc_html__( 'Course title', 'masterstudy-lms-learning-management-system-pro' ),
			'membership_plan' => esc_html__( 'Plan name', 'masterstudy-lms-learning-management-system-pro' ),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'      => esc_html__(
				'User Login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'student_email'   => esc_html__(
				'Student Email',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_announcement_from_instructor'            => array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'New Announcement Posted',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when the instructor posts a new course announcement.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'New Announcement in {{course_title}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'Hi {{user_login}}, <br>
				Exciting news! {{instructor_name}} has just published a new announcement for your course, {{course_title}}.<br>
				Announcement: {{mail}} <br>
				Stay tuned for more updates!'
		),
		'vars'    => array(
			'course_title'    => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'      => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'mail'            => esc_html__(
				'Instructor message',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
	'stm_lms_student_enrollment_in_course_to_author'  => array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'New Course Enrollment',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when student enrolled to the instructor\'s course', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'New Enrollment in {{course_title}}!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'Great news! <br>
			{{user_login}} has just enrolled in your course {{course_title}} on {{date}}.<br>
			Thank you for your valuable contribution to our platform. Keep up the fantastic work!'
		),
		'vars'    => array(
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Purchase date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	),
);

// Conditionally add notifications for Pro Plus users

if ( STM_LMS_Options::get_option( 'course_premoderation', false ) ) {

	$email_templates['stm_lms_course_rejected'] = array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'Course rejection notification',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__('Send an email to the instructor if a course is rejected during moderation', 'masterstudy-lms-learning-management-system-pro'),
		'subject' => esc_html__(
			'Your Course {{course_title}} Has Been Rejected',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'We regret to inform you that your course {{course_title}} has been rejected by the admin.<br> 
			We encourage you to get feedback from the admin and make the necessary adjustments to meet our guidelines.
			If you want to get feedback and have any questions, please contact at {{admin_email}}.<br>
			Thank you for your understanding and cooperation.'
		),
		'vars'    => array(
			'course_title'    => esc_html__(
				'Course Title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'admin_email' => esc_html__(
				'Admin email',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
}

if ( is_ms_lms_addon_enabled( 'assignments' ) ) {
	$email_templates['stm_lms_new_assignment'] = array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__(
			'New Assignment Submission',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'New Assignment Submitted by {{user_login}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a new assignment is submitted by a student.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'We wanted to let you know that {{user_login}} has submitted their assignment, {{assignment_title}}, for the course {{course_title}}. <br>
			Submission Date: {{date}} <br>
			You can review the submitted assignment here: <br>
			 <a href="{{assignment_url}}" target="_blank">Assignment URL</a>',
		),
		'vars'    => array(
			'user_login'       => esc_html__( 'User Login', 'masterstudy-lms-learning-management-system-pro' ),
			'course_title'     => esc_html__( 'Course title', 'masterstudy-lms-learning-management-system-pro' ),
			'assignment_title' => esc_html__( 'Assignment title', 'masterstudy-lms-learning-management-system-pro' ),
			'assignment_url'   => esc_html__( 'Assignment URL', 'masterstudy-lms-learning-management-system-pro' ),
			'blog_name'        => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'         => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'             => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);

	$email_templates['stm_lms_assignment_checked'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Assignment Graded',
			'masterstudy-lms-learning-management-system-pro'
		),
		'subject' => esc_html__(
			'Your Assignment on {{course_title}} Has Been Reviewed',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when the status of a student\'s assignment is updated.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			' Dear {{user_login}}, <br>
			Your assignment "{{assignment_title}}" in the course "{{course_title}}" has just been reviewed by {{instructor_name}}.<br>
			Result: {{assignment_result}} <br>
			Assignment Mark: {{assignment_mark}}<br>
			Instructor\'s Comment: <br>
			{{assignment_comment}} <br>
			Detailed Feedback and Assignment <a href="{{assignment_url}}" target="_blank">here</a> <br>
			Keep going — you\'re making great progress! If you have questions or need clarification, feel free to reach out to your instructor.'
		),
		'vars'    => array(
			'blog_name'          => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'           => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'               => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'assignment_result'  => esc_html__(
				'Assignment result',
				'masterstudy-lms-learning-management-system-pro'
			),
			'assignment_comment' => esc_html__(
				'Assignment comment',
				'masterstudy-lms-learning-management-system-pro'
			),
			'assignment_mark'    => esc_html__(
				'Assignment mark',
				'masterstudy-lms-learning-management-system-pro'
			),
			'assignment_url'     => esc_html__(
				'Assignment URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'assignment_title'   => esc_html__(
				'Assignment title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name'    => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title'       => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'         => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
}

if ( is_ms_lms_addon_enabled( 'enterprise_courses' ) ) {

	$email_templates['stm_lms_new_group_invite']                        = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'New Group Invitation',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a user is invited to join a new group.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'{{user_login}}, You Have Been Invited to a Group!',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'Dear {{user_login}},<br>
				You’ve just been added to the enterprise group {{group_name}} by {{admin_login}}.<br>
				As a member of this group, you now have full access to all courses purchased under it.<br>
				If you believe this was a mistake or have any questions, please don’t hesitate to contact us.'
		),
		'vars'    => array(
			'blog_name'   => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'    => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'        => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'admin_login' => esc_html__(
				'Admin login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'group_name'  => esc_html__(
				'Group name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'  => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['stm_lms_new_user_creds']                          = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Enterprise Group Credentials Sent',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a user is added to a group with login credentials.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'New user credentials for enterprise group',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => esc_html__(
			'Login: {{username}} Password: {{password}} Site URL: {{site_url}}',
			'masterstudy-lms-learning-management-system-pro'
		),
		'vars'    => array(
			'username'    => esc_html__(
				'Username',
				'masterstudy-lms-learning-management-system-pro'
			),
			'password'    => esc_html__(
				'Password',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'   => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'    => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'login_url'   => esc_html__(
				'Login URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'        => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'admin_login' => esc_html__(
				'Admin login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'group_name'  => esc_html__(
				'Group name',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['stm_lms_enterprise_new_group_course']             = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'New Course Assigned for Enterprise Group',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when a new course becomes available in the group.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'{{user_login}}, New Course is Available for Your Group',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => wp_kses_post(
			'Congratulations {{user_login}} <br>
				You just got access to a course {{course_title}} within your group - {{group_name}}.'
		),
		'vars'    => array(
			'admin_login'  => esc_html__(
				'Admin login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'group_name'   => esc_html__(
				'Group name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_title' => esc_html__(
				'Course title',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_url'     => esc_html__(
				'User url',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	$email_templates['stm_lms_email_enterprise_remove_user_from_group'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__(
			'Removed from Enterprise Group',
			'masterstudy-lms-learning-management-system-pro'
		),
		'hint'    => esc_html__( 'Send when group admin removes the student from the enterprise group.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__(
			'You’ve Been Removed from the Group',
			'masterstudy-lms-learning-management-system-pro'
		),
		'message' => __(
		// phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'Dear {{user_login}},<br>{{admin_login}} has removed you from the group - “{{group_name}}”.<br> Now you don’t have access to the courses assigned to this group.',
			'masterstudy-lms-learning-management-system-pro'
		),
		'vars'    => array(
			'user_login'  => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'admin_login' => esc_html__(
				'Admin login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'group_name'  => esc_html__(
				'Group name',
				'masterstudy-lms-learning-management-system-pro'
			),

			'site_url'  => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'      => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name' => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
}

if ( STM_LMS_Helpers::is_pro_plus() ) {
	$email_templates['stm_lms_reports_student_checked'] = array(
		'section'   => 'emails_to_students',
		'notice'    => esc_html__( 'Course Progress Reports', 'masterstudy-lms-learning-management-system-pro' ),
		'frequency' => esc_html__( 'Frequency', 'masterstudy-lms-learning-management-system-pro' ),
		'period'    => esc_html__( 'Day of the week to send', 'masterstudy-lms-learning-management-system-pro' ),
		'time'      => esc_html__( 'Time', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'      => esc_html__( 'Automatically send weekly or monthly course progress reports to students', 'masterstudy-lms-learning-management-system-pro' ),
		'title'     => esc_html__( 'Your Weekly Report', 'masterstudy-lms-learning-management-system-pro' ),
		'message'   => esc_html__( 'Great job so far! Here is just a quick heads-up on your progress:', 'masterstudy-lms-learning-management-system-pro' ),
		'vars'      => array(
			'blog_name'  => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'   => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'       => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login' => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);

	$email_templates['stm_lms_reports_instructor_checked'] = array(
		'section'   => 'emails_to_instructors',
		'notice'    => esc_html__( 'Weekly/Monthly Summary Reports', 'masterstudy-lms-learning-management-system-pro' ),
		'frequency' => esc_html__( 'Frequency', 'masterstudy-lms-learning-management-system-pro' ),
		'period'    => esc_html__( 'Day of the week to send', 'masterstudy-lms-learning-management-system-pro' ),
		'time'      => esc_html__( 'Time', 'masterstudy-lms-learning-management-system-pro' ),
		'title'     => esc_html__( 'Your Weekly Report', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'      => esc_html__( 'Automatically send weekly or monthly LMS activity reports', 'masterstudy-lms-learning-management-system-pro' ),
		'message'   => esc_html__( 'Your dedication to creating valuable learning experiences is evident in the numbers. Here\'s your latest update on your courses:', 'masterstudy-lms-learning-management-system-pro' ),
		'vars'      => array(
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'analytics_url'   => esc_html__(
				'Analytics URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	$email_templates['stm_lms_reports_admin_checked']      = array(
		'section'   => 'emails_to_admin',
		'notice'    => esc_html__( 'Weekly/Monthly Summary Reports', 'masterstudy-lms-learning-management-system-pro' ),
		'frequency' => esc_html__( 'Frequency', 'masterstudy-lms-learning-management-system-pro' ),
		'period'    => esc_html__( 'Day of the week to send', 'masterstudy-lms-learning-management-system-pro' ),
		'time'      => esc_html__( 'Time', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'      => esc_html__( 'Automatically send weekly or monthly LMS activity reports', 'masterstudy-lms-learning-management-system-pro' ),
		'title'     => esc_html__( 'Your Weekly Report', 'masterstudy-lms-learning-management-system-pro' ),
		'message'   => esc_html__( 'Here\'s your comprehensive report summarizing the activity across the entire LMS platform:', 'masterstudy-lms-learning-management-system-pro' ),
		'vars'      => array(
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'analytics_url'   => esc_html__(
				'Analytics URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
}

if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'coming_soon' ) ) {
	$email_templates['masterstudy_lms_coming_soon_availability'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Course Now Available for Completion', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'    => esc_html__( 'Send when a course becomes available for completion/enroll.', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( '{{course_title}} is Now Available!', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'We have great news! The course {{course_title}} that you subscribed to is now available for completion.<br>
		You can start learning right away by accessing the course here:<br>
		{{course_url}}<br>
		Happy learning!'
		),
		'vars'    => array(
			'course_title' => esc_html__( 'Course title', 'masterstudy-lms-learning-management-system-pro' ),
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'   => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['masterstudy_lms_coming_soon_pre_sale']     = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Course Available for Pre-Sale', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Pre-Sale for {{course_title}} Starts Now!', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'    => esc_html__( 'Send when a course is available for pre-sale.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'Exciting news! The pre-sale for the course {{course_title}} has just started.<br>
		Be among the first to enroll and secure your spot.<br>
		You can check out the pre-sale details and enroll here:<br>
		Course Pre-Sale Link: {{course_url}}<br>
		Don’t miss out on this opportunity!' ),
		'vars'    => array(
			'course_title' => esc_html__( 'Course title', 'masterstudy-lms-learning-management-system-pro' ),

			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'      => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'upcoming_date'   => esc_html__(
				'Upcoming Date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	$email_templates['masterstudy_lms_coming_soon_start_date']   = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Course Start Date Updated', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Course start date has been changed', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'    => esc_html__( 'Send when the start date of a course is changed.', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'We wanted to let you know that the start date for the course {{course_title}} has changed. <br>
		New Start Date: {{upcoming_date}} <br>
		You can find more details about the course and the updated schedule here: <br>
		Course Link: {{course_url}} <br>
		We apologize for any inconvenience this may cause. <br>
		Thank you for your understanding.'
		),
		'vars'    => array(
			'course_title' => esc_html__( 'Course title', 'masterstudy-lms-learning-management-system-pro' ),

			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'course_url'      => esc_html__(
				'Course URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'      => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'upcoming_date'   => esc_html__(
				'Upcoming Date',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
}

$email_templates['stm_lms_certificates_preview_checked'] = array(
	'section'      => 'emails_to_students',
	'notice'       => esc_html__( 'Course Certificate Issued', 'masterstudy-lms-learning-management-system-pro' ),
	'subject'      => esc_html__( 'You have received a certificate!', 'masterstudy-lms-learning-management-system-pro' ),
	'hint'         => esc_html__( 'Send when students earns a certificate by completing required progress in the course.', 'masterstudy-lms-learning-management-system-pro' ),
	'message'      => wp_kses_post( '<h2>You have received a certificate!</h2>{{date}}<br>{{certificate_preview}}<br>{{button}}' ),
	'vars'         => array(
		'date'                => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'certificate_preview' => esc_html__( 'Certificate preview', 'masterstudy-lms-learning-management-system-pro' ),
		'button'              => esc_html__( 'Button ', 'masterstudy-lms-learning-management-system-pro' ),
		'blog_name'           => esc_html__(
			'Blog name',
			'masterstudy-lms-learning-management-system-pro'
		),
		'site_url'            => esc_html__(
			'Site URL',
			'masterstudy-lms-learning-management-system-pro'
		),
		'course_title'        => esc_html__(
			'Course title',
			'masterstudy-lms-learning-management-system-pro'
		),
		'course_url'          => esc_html__(
			'Course URL',
			'masterstudy-lms-learning-management-system-pro'
		),
		'instructor_name'     => esc_html__(
			'Instructor name',
			'masterstudy-lms-learning-management-system-pro'
		),
	),
	'notice_setup' => esc_html__( 'To receive certificates, make sure the certificate page is set up properly.', 'masterstudy-lms-learning-management-system-pro' ),
);
$email_templates['stm_lms_time_limit_expiration_reminder_email'] = array(
	'section'           => 'emails_to_students',
	'notice'            => esc_html__( 'Course Access Expiration Reminder', 'masterstudy-lms-learning-management-system-pro' ),
	'subject'           => esc_html__( 'Your access to {{course_title}} is expiring soon', 'masterstudy-lms-learning-management-system-pro' ),
	'send_email_before' => esc_html__( 'Send email before', 'masterstudy-lms-learning-management-system-pro' ),
	'hint'              => esc_html__( 'Sends a reminder email to students when their course access is about to expire and informs them that they can restore access by repurchasing the course.', 'masterstudy-lms-learning-management-system-pro' ),
	'message'           => wp_kses_post(
		'Hello {{user_login}}, <br>
		This is a friendly reminder that your access to the course {{course_title}}  {{course_url}} will expire on {{course_expiration_date}}.<br>
		You can continue accessing the course content until the expiration date.<br>
		After your access expires, the course content will no longer be available. If you wish to continue learning, you can restore access at any time by purchasing the course again.<br>
		If you have any questions, please feel free to contact us.'
	),
	'vars'              => array(
		'course_title'           => esc_html__(
			'Course title',
			'masterstudy-lms-learning-management-system-pro'
		),
		'course_url'             => esc_html__(
			'Course URL',
			'masterstudy-lms-learning-management-system-pro'
		),
		'user_login'             => esc_html__(
			'User login',
			'masterstudy-lms-learning-management-system-pro'
		),
		'date'                   => esc_html__( 'Date', 'masterstudy-lms-learning-management-system-pro' ),
		'blog_name'              => esc_html__(
			'Blog name',
			'masterstudy-lms-learning-management-system-pro'
		),
		'site_url'               => esc_html__(
			'Site URL',
			'masterstudy-lms-learning-management-system-pro'
		),
		'course_expiration_date' => esc_html__(
			'Course expiration date',
			'masterstudy-lms-learning-management-system-pro'
		),
	),
);

$register_as_instructor = STM_LMS_Options::get_option( 'register_as_instructor', false );

if ( $register_as_instructor ) {
	$email_templates['stm_lms_email_update_user_status_approved'] = array(
		'section' => 'emails_to_instructors',
		'notice'  => esc_html__( 'Instructor Application Approved', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Instructor Application Approved', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'Hi {{instructor_name}},<br>
			Congratulations! Your application to become an instructor on {{blog_name}} has been approved. <br>
			You can now log in to your instructor account using the following link:<br>
			Login URL: <a href="{{login_url}}" target="_blank">Login URL</a><br>
			We are excited to have you on board and look forward to your contributions!'
		),
		'hint'    => esc_html__( 'Send when admin approves the become instructor form submitted by user', 'masterstudy-lms-learning-management-system-pro' ),
		'vars'    => array(
			'instructor_name' => esc_html__(
				'Instructor name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'login_url'       => esc_html__(
				'Login URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'       => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'        => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'            => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'admin_comment'   => esc_html__(
				'Admin comment',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	$email_templates['stm_lms_email_update_user_status_rejected'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Instructor Application Rejected', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Update on Your Instructor Application', 'masterstudy-lms-learning-management-system-pro' ),
		'hint'    => esc_html__( 'Send when admin rejects the become instructor form submitted by user ', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => wp_kses_post(
			'Hi {{user_login}},<br>
			Thank you for your interest in becoming an instructor on {{blog_name}} <br>
			After careful review, we regret to inform you that your application has not been approved at this time.
			We appreciate the time and effort you put into your submission. 
			You\'re welcome to update your application and reapply in the future. 
			If you have any questions or would like feedback, feel free to reach out to our team.<br>
			Best regards.'
		),
		'vars'    => array(
			'user_login'    => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'blog_name'     => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'      => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'          => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'admin_comment' => esc_html__(
				'Admin comment',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
}

if ( STM_LMS_Helpers::is_pro_plus() && is_ms_lms_addon_enabled( 'subscriptions' ) ) {
	$email_templates['masterstudy_lms_subscription_trial_access'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Trial Access Starts Today', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your trial access for {{plan_name}} has started', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}},<br>
		Your trial access for {{plan_name}} has started today. <br>
		Enjoy exploring all available features and courses included in your plan. <br>
		Your trial will remain active until {{expiration_date}}. <br>
		Make the most of it and start learning today. <br>
		Log in [{{site_url}}] anytime to continue your learning journey.',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'expiration_date'   => esc_html__(
				'Expiration date',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['masterstudy_lms_subscription_state_activated'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Subscription Activated', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your subscription {{plan_name}} is now active', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}},<br>
		Good news. Your subscription for {{plan_name}} on {{blog_name}} is now active. <br>
		You can start accessing all courses and materials included in your plan. <br>
		Log in [{{site_url}}] anytime to continue your learning journey.',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['masterstudy_lms_subscription_state_on_hold'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Subscription on Hold', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your subscription {{plan_name}} is currently on hold', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}},<br>
		Your subscription for {{plan_name}} on {{blog_name}} has been placed on hold. <br>
		During this period, access to course content may be limited. <br>
		If this change was unexpected, please check your account status or contact us for assistance.',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	$email_templates['masterstudy_lms_subscription_state_renewed'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Subscription Renewed', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your subscription {{plan_name}} has been renewed', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}}, <br>
		Your subscription for {{plan_name}} on {{blog_name}} has been successfully renewed on {{date}}.<br>
		Thank you for staying with us. <br>
		You can continue [{{site_url}}] enjoying all your courses without interruption.',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),
		),
	);
	$email_templates['masterstudy_lms_subscription_state_expired'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Subscription Expired', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your subscription {{plan_name}} has expired', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}}, <br>
		Your subscription for {{plan_name}} on {{blog_name}} has expired. <br>
		Access to courses in your plan is now paused. <br>
		You can renew your subscription at any time to regain full access and continue your learning journey. Renew now at {{site_url}}.',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['masterstudy_lms_subscription_state_expires_soon'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Subscription Expires Soon', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your subscription expires on {{expiration_date}}', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}}, <br>
		This is a friendly reminder that your subscription for {{plan_name}} will expire on {{expiration_date}}. <br>
		To avoid interruption in your learning progress, consider renewing your subscription before it ends. <br>
		You can manage or renew your plan anytime from here [{{site_url}}].',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'expiration_date'   => esc_html__(
				'Expiration date',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
	$email_templates['masterstudy_lms_subscription_state_cancelled'] = array(
		'section' => 'emails_to_students',
		'notice'  => esc_html__( 'Subscription Cancelled', 'masterstudy-lms-learning-management-system-pro' ),
		'subject' => esc_html__( 'Your subscription {{plan_name}} has been cancelled', 'masterstudy-lms-learning-management-system-pro' ),
		'message' => 'Hello {{user_login}}, <br>
		Your subscription for {{plan_name}} on {{blog_name}} has been cancelled as of {{date}}.<br>
		If you cancelled it yourself, no further action is needed. If this was a mistake, you can easily reactivate your plan from your account.<br>
		Visit here [{{site_url}}] to view your subscription options.',
		'vars'    => array(
			'blog_name'    => esc_html__(
				'Blog name',
				'masterstudy-lms-learning-management-system-pro'
			),
			'site_url'     => esc_html__(
				'Site URL',
				'masterstudy-lms-learning-management-system-pro'
			),
			'date'         => esc_html__(
				'Application date',
				'masterstudy-lms-learning-management-system-pro'
			),
			'user_login'   => esc_html__(
				'User login',
				'masterstudy-lms-learning-management-system-pro'
			),
			'plan_name'   => esc_html__(
				'Plan name',
				'masterstudy-lms-learning-management-system-pro'
			),

		),
	);
}

return $email_templates;
