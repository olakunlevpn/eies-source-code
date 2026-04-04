<?php
function masterstudy_enqueue() {
	masterstudy_global_scripts();

	/*Course player scripts registration*/
	wp_register_script( 'masterstudy-course-player-assignments', STM_LMS_PRO_URL . 'assets/js/course-player/assignments.js', array( 'jquery', 'masterstudy-audio-player' ), STM_LMS_PRO_VERSION, true );

	/*Course player styles registration*/
	wp_register_style( 'masterstudy-course-player-lesson-zoom', STM_LMS_PRO_URL . 'assets/css/course-player/zoom-conference.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-stream', STM_LMS_PRO_URL . 'assets/css/course-player/stream.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-google', STM_LMS_PRO_URL . 'assets/css/course-player/google-meet.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-drip-content', STM_LMS_PRO_URL . 'assets/css/course-player/drip-content.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-assignments', STM_LMS_PRO_URL . 'assets/css/course-player/assignments.css', null, STM_LMS_PRO_VERSION );

	/*Course player fonts styles registration*/
	wp_register_style( 'masterstudy-course-player-lesson-zoom-fonts', STM_LMS_PRO_URL . 'assets/css/course-player/fonts/zoom-conference.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-stream-fonts', STM_LMS_PRO_URL . 'assets/css/course-player/fonts/stream.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-lesson-google-fonts', STM_LMS_PRO_URL . 'assets/css/course-player/fonts/google-meet.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-drip-content-fonts', STM_LMS_PRO_URL . 'assets/css/course-player/fonts/drip-content.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-course-player-assignments-fonts', STM_LMS_PRO_URL . 'assets/css/course-player/fonts/assignments.css', null, STM_LMS_PRO_VERSION );

	/* Certificate page scripts & style registration */
	wp_register_style( 'masterstudy-instructor-certificates', STM_LMS_PRO_URL . 'assets/css/certificate-builder/instructor-certificates.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-instructor-certificates', STM_LMS_PRO_URL . 'assets/js/certificate-builder/instructor-certificates.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-certificate-page', STM_LMS_PRO_URL . 'assets/css/certificate-builder/certificate-page.css', null, STM_LMS_PRO_VERSION );

	/*Single course page styles & scripts registration*/
	wp_register_script( 'masterstudy-single-course-main', STM_LMS_PRO_URL . 'assets/js/course/main.js', array( 'jquery', 'plyr' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-single-course-classic', STM_LMS_PRO_URL . 'assets/css/course/classic.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-modern', STM_LMS_PRO_URL . 'assets/css/course/modern.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-timeless', STM_LMS_PRO_URL . 'assets/css/course/timeless.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-dynamic-sidebar', STM_LMS_PRO_URL . 'assets/css/course/dynamic-sidebar.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-sleek-sidebar', STM_LMS_PRO_URL . 'assets/css/course/sleek-sidebar.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-minimalistic', STM_LMS_PRO_URL . 'assets/css/course/minimalistic.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-dynamic', STM_LMS_PRO_URL . 'assets/css/course/dynamic.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-full-width', STM_LMS_PRO_URL . 'assets/css/course/full-width.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-modern-curriculum', STM_LMS_PRO_URL . 'assets/css/course/modern-curriculum.css', null, STM_LMS_PRO_VERSION );

	/*Components scripts registration*/
	wp_register_script( 'masterstudy-form-builder-fields', STM_LMS_PRO_URL . 'assets/js/components/form-builder-fields.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_localize_script(
		'masterstudy-form-builder-fields',
		'masterstudy_form_builder_data',
		array(
			'ajax_url'          => admin_url( 'admin-ajax.php' ),
			'file_upload_nonce' => wp_create_nonce( 'stm_lms_upload_form_file' ),
			'file_delete_nonce' => wp_create_nonce( 'stm_lms_delete_form_file' ),
			'icon_url'          => STM_LMS_PRO_URL . 'assets/icons/files/',
			'only_one_file'     => __( 'Only one file allowed', 'masterstudy-lms-learning-management-system-pro' ),
		)
	);
	wp_register_script( 'masterstudy-buy-button-membership', STM_LMS_PRO_URL . 'assets/js/components/buy-button/membership.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-buy-button-points', STM_LMS_PRO_URL . 'assets/js/components/buy-button/points.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-buy-button-prerequisites', STM_LMS_PRO_URL . 'assets/js/components/buy-button/prerequisites.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-group-course-trigger', STM_LMS_PRO_URL . 'assets/js/components/modals/group-courses-trigger.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-group-course-add-group', STM_LMS_PRO_URL . 'assets/js/components/modals/group-courses-add-group.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-group-course-add-to-cart', STM_LMS_PRO_URL . 'assets/js/components/modals/group-courses-add-to-cart.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-bundle-button', STM_LMS_PRO_URL . 'assets/js/components/bundle-button/main.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-bundle-button-points', STM_LMS_PRO_URL . 'assets/js/components/bundle-button/points.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-video-questions', STM_LMS_PRO_URL . 'assets/js/components/video-questions.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-bundle-select-courses', STM_LMS_PRO_URL . 'assets/js/components/bundle/select-courses.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );

	/*Components styles registration*/
	wp_register_style( 'masterstudy-buy-button-points', STM_LMS_PRO_URL . 'assets/css/components/buy-button/points.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-buy-button-group-courses', STM_LMS_PRO_URL . 'assets/css/components/buy-button/group-courses.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-buy-button-affiliate', STM_LMS_PRO_URL . 'assets/css/components/buy-button/affiliate.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-buy-button-prerequisites', STM_LMS_PRO_URL . 'assets/css/components/buy-button/prerequisite-button.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-prerequisites-info', STM_LMS_PRO_URL . 'assets/css/components/buy-button/prerequisite-info.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-group-course', STM_LMS_PRO_URL . 'assets/css/components/group-courses.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-form-builder-fields', STM_LMS_PRO_URL . 'assets/css/components/form-builder-fields.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-form-builder-public-fields', STM_LMS_PRO_URL . 'assets/css/components/form-builder-public-fields.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-button', STM_LMS_PRO_URL . 'assets/css/components/bundle-button/main.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-button-points', STM_LMS_PRO_URL . 'assets/css/components/bundle-button/points.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-card-default', STM_LMS_PRO_URL . 'assets/css/components/bundle/card.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-video-questions-list', STM_LMS_PRO_URL . 'assets/css/components/video-questions-list.css', array(), STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-video-questions', STM_LMS_PRO_URL . 'assets/css/components/video-questions.css', array(), STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-course-card', STM_LMS_PRO_URL . 'assets/css/components/bundle/course-card.css', array(), STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-select-courses', STM_LMS_PRO_URL . 'assets/css/components/bundle/select-courses.css', array(), STM_LMS_PRO_VERSION );

	/*Components fonts styles registration*/
	wp_register_style( 'masterstudy-buy-button-points-fonts', STM_LMS_PRO_URL . 'assets/css/components/fonts/buy-button/points.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-buy-button-group-courses-fonts', STM_LMS_PRO_URL . 'assets/css/components/fonts/buy-button/group-courses.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-buy-button-affiliate-fonts', STM_LMS_PRO_URL . 'assets/css/components/fonts/buy-button/affiliate.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-buy-button-prerequisites-fonts', STM_LMS_PRO_URL . 'assets/css/components/fonts/buy-button/prerequisite-button.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-prerequisites-info-fonts', STM_LMS_PRO_URL . 'assets/css/components/fonts/buy-button/prerequisite-info.css', null, STM_LMS_PRO_VERSION );

	/*Bundles scripts & styles registration*/
	wp_register_script( 'masterstudy-add-bundle', STM_LMS_PRO_URL . 'assets/js/account/instructor/add-bundle.js', array( 'jquery', 'vue.js', 'vue-resource.js' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-add-bundle', STM_LMS_PRO_URL . 'assets/css/account/instructor/add-bundle.css', null, STM_LMS_PRO_VERSION );
}
add_action( 'wp_enqueue_scripts', 'masterstudy_enqueue' );

function masterstudy_admin_enqueue() {
	masterstudy_global_scripts();

	wp_enqueue_style( 'fonts', STM_LMS_PRO_URL . 'assets/css/variables/fonts.css', null, STM_LMS_PRO_VERSION );
	wp_enqueue_style( 'masterstudy-admin-certificate', STM_LMS_PRO_URL . 'assets/css/certificate-builder/admin.css', null, STM_LMS_PRO_VERSION );
}
add_action( 'admin_enqueue_scripts', 'masterstudy_admin_enqueue' );

function masterstudy_global_scripts() {
	/*Libraries styles & scripts registration*/
	wp_register_style( 'masterstudy-grade-details', STM_LMS_PRO_URL . 'assets/css/components/grade-details.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-single-course-grade', STM_LMS_PRO_URL . 'assets/css/components/course/grade.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-chartjs-library', STM_LMS_PRO_URL . 'assets/vendors/chart.umd.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-charts-helpers', STM_LMS_PRO_URL . 'assets/js/analytics/helpers/charts.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-stats-blocks-helpers', STM_LMS_PRO_URL . 'assets/js/analytics/helpers/stats-blocks.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );

	/*API provider*/
	wp_register_script( 'masterstudy-api-provider', STM_LMS_PRO_URL . 'assets/js/analytics/api-provider.js', array(), STM_LMS_PRO_VERSION, true );
	wp_localize_script(
		'masterstudy-api-provider',
		'api_data',
		array(
			'rest_url'    => esc_url_raw( rest_url( 'masterstudy-lms/v2/' ) ),
			'wp_rest_url' => esc_url_raw( rest_url( 'wp/v2/' ) ),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
		)
	);

	/*Analytics styles & scripts registration*/
	$analytics_deps = array(
		'jquery',
		'masterstudy-api-provider',
		'masterstudy-chartjs-library',
		'masterstudy-charts-helpers',
		'masterstudy-stats-blocks-helpers',
		'masterstudy-datatables-helpers',
		'masterstudy-datepicker-helpers',
		'masterstudy-date-helpers',
		'masterstudy-loaders-helpers',
		'masterstudy-analytics-main',
	);

	$sales_deps = array(
		'jquery',
		'masterstudy-api-provider',
		'masterstudy-stats-blocks-helpers',
		'masterstudy-datatables-helpers',
		'masterstudy-datepicker-helpers',
		'masterstudy-date-helpers',
		'masterstudy-loaders-helpers',
		'masterstudy-analytics-main',
	);

	wp_register_style( 'masterstudy-analytics-components', STM_LMS_PRO_URL . 'assets/css/components/analytics/main.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-revenue-page', STM_LMS_PRO_URL . 'assets/js/analytics/revenue.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-revenue-page', STM_LMS_PRO_URL . 'assets/css/analytics/revenue.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-engagement-page', STM_LMS_PRO_URL . 'assets/js/analytics/engagement.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-engagement-page', STM_LMS_PRO_URL . 'assets/css/analytics/engagement.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-users-page', STM_LMS_PRO_URL . 'assets/js/analytics/users.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-users-page', STM_LMS_PRO_URL . 'assets/css/analytics/users.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-reviews-page', STM_LMS_PRO_URL . 'assets/js/analytics/reviews.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-reviews-page', STM_LMS_PRO_URL . 'assets/css/analytics/reviews.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-course-page', STM_LMS_PRO_URL . 'assets/js/analytics/course.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-course-page', STM_LMS_PRO_URL . 'assets/css/analytics/course.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-instructor-page', STM_LMS_PRO_URL . 'assets/js/analytics/instructor.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-instructor-page', STM_LMS_PRO_URL . 'assets/css/analytics/instructor.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-student-page', STM_LMS_PRO_URL . 'assets/js/analytics/student.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-student-page', STM_LMS_PRO_URL . 'assets/css/analytics/student.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-bundle-page', STM_LMS_PRO_URL . 'assets/js/analytics/bundle.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-bundle-page', STM_LMS_PRO_URL . 'assets/css/analytics/bundle.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-instructor-students-page', STM_LMS_PRO_URL . 'assets/js/analytics/instructor-students.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-instructor-students-page', STM_LMS_PRO_URL . 'assets/css/analytics/instructor-students.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-analytics-short-report', STM_LMS_PRO_URL . 'assets/js/analytics/short-report.js', $analytics_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-short-report', STM_LMS_PRO_URL . 'assets/css/analytics/short-report.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-woocommerce-orders', STM_LMS_PRO_URL . 'assets/css/woocommerce/orders.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-woocommerce-orders', STM_LMS_PRO_URL . 'assets/js/woocommerce/orders.js', $sales_deps, STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy_membership_pricing', STM_LMS_PRO_URL . 'assets/js/subscriptions/add-to-cart.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );

	/*Grades styles & scripts registration*/
	$grades_deps = array(
		'jquery',
		'masterstudy-api-provider',
		'masterstudy-datatables-helpers',
		'masterstudy-date-helpers',
		'masterstudy-loaders-helpers',
		'masterstudy-grades-main',
	);
	wp_register_script( 'masterstudy-grades-main', STM_LMS_PRO_URL . 'assets/js/grades/main.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	if ( class_exists( 'STM_LMS_Options' ) ) {
		wp_localize_script(
			'masterstudy-grades-main',
			'grades_data',
			array(
				'score_separator' => esc_js( STM_LMS_Options::get_option( 'grades_scores_separator', '/' ) ),
			)
		);
	}

	wp_register_script( 'masterstudy-analytics-main', STM_LMS_PRO_URL . 'assets/js/analytics/main.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-grades-instructor', STM_LMS_PRO_URL . 'assets/js/grades/instructor.js', $grades_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-grades-instructor', STM_LMS_PRO_URL . 'assets/css/grades/instructor.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-grades-student', STM_LMS_PRO_URL . 'assets/js/grades/student.js', $grades_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-grades-student', STM_LMS_PRO_URL . 'assets/css/grades/student.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-single-course-grade', STM_LMS_PRO_URL . 'assets/js/components/course/grades.js', array( 'masterstudy-api-provider' ), STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-analytics-sales-page', STM_LMS_PRO_URL . 'assets/js/analytics/sales.js', $sales_deps, STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-analytics-sales-page', STM_LMS_PRO_URL . 'assets/css/analytics/sales.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-instructor-subscriptions-page', STM_LMS_PRO_URL . 'assets/js/analytics/subscriptions.js', $sales_deps, STM_LMS_PRO_VERSION, true );
	wp_register_script( 'masterstudy-my-subscriptions-page', STM_LMS_PRO_URL . 'assets/js/account/my-subscriptions.js', array( 'jquery', 'masterstudy-datepicker-component', 'masterstudy-datatables-component' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-my-subscriptions-page', STM_LMS_PRO_URL . 'assets/css/account/my-subscriptions.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-my-sales-page', STM_LMS_PRO_URL . 'assets/js/account/instructor/my-sales.js', array( 'jquery', 'masterstudy-datepicker-component', 'masterstudy-datatables-component' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-my-sales-page', STM_LMS_PRO_URL . 'assets/css/account/instructor/my-sales.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-subscription-details-page', STM_LMS_PRO_URL . 'assets/js/account/subscription-details.js', array( 'jquery', 'masterstudy-datepicker-component', 'masterstudy-datatables-component' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-subscription-details-page', STM_LMS_PRO_URL . 'assets/css/account/subscription-details.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-account-bundles', STM_LMS_PRO_URL . 'assets/js/account/bundles.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-account-bundles', STM_LMS_PRO_URL . 'assets/css/account/bundles.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-single', STM_LMS_PRO_URL . 'assets/css/bundle/single.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-bundle-card', STM_LMS_PRO_URL . 'assets/css/bundle/card.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-deprecated-bundles', STM_LMS_PRO_URL . 'assets/css/bundle/deprecated/card.css', null, STM_LMS_PRO_VERSION );
	wp_register_style( 'masterstudy-account-points', STM_LMS_PRO_URL . 'assets/css/account/points.css', null, STM_LMS_PRO_VERSION );
	wp_register_script( 'masterstudy-account-points', STM_LMS_PRO_URL . 'assets/js/account/points.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
	wp_register_style( 'masterstudy-account-points-distribution', STM_LMS_PRO_URL . 'assets/css/account/points-distribution.css', null, STM_LMS_PRO_VERSION );

	if ( class_exists( 'STM_LMS_Options' ) ) {
		wp_localize_script(
			'masterstudy-analytics-main',
			'stats_data',
			array(
				'report_button_title'         => __( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
				'details_title'               => __( 'Details', 'masterstudy-lms-learning-management-system-pro' ),
				'progress_title'              => __( 'Course progress', 'masterstudy-lms-learning-management-system-pro' ),
				'custom_period'               => __( 'Date range', 'masterstudy-lms-learning-management-system-pro' ),
				'locale'                      => function_exists( 'masterstudy_get_locale_info' ) ? masterstudy_get_locale_info() : array(),
				'assignments_addon'           => is_ms_lms_addon_enabled( 'assignments' ),
				'payouts_addon'               => is_ms_lms_addon_enabled( 'statistics' ),
				'upcoming_addon'              => is_ms_lms_addon_enabled( 'coming_soon' ),
				'bundle_addon'                => is_ms_lms_addon_enabled( 'course_bundle' ),
				'user_account_url'            => STM_LMS_User::login_page_url(),
				'courses_page_url'            => STM_LMS_Course::courses_page_url(),
				'bundles_page_url'            => get_home_url() . '/stm-course-bundles/',
				'instructors_payouts'         => STM_LMS_Options::get_option( 'instructors_payouts', true ),
				'instructor_can_add_students' => STM_LMS_Options::get_option( 'instructor_can_add_students', false ),
				'is_user_account'             => ! is_admin(),
				'is_admin'                    => current_user_can( 'administrator' ) ?? 0,
				'button_view'                 => __( 'View', 'masterstudy-lms-learning-management-system-pro' ),
				'is_student'                  => 'stm-lms-enrolled-student' === get_query_var( 'lms_template' ),
			)
		);

		wp_localize_script(
			'masterstudy-stats-blocks-helpers',
			'stats_block_data',
			array(
				'currency_symbol'    => STM_LMS_Options::get_option( 'currency_symbol', '$' ),
				'currency_position'  => STM_LMS_Options::get_option( 'currency_position', 'left' ),
				'currency_thousands' => STM_LMS_Options::get_option( 'currency_thousands', ',' ),
				'currency_decimals'  => STM_LMS_Options::get_option( 'currency_decimals', '.' ),
				'decimals_num'       => STM_LMS_Options::get_option( 'decimals_num', 2 ),
			)
		);
	}
}
