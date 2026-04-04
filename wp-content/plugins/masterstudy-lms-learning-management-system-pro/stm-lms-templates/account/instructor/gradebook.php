<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

wp_enqueue_style( 'masterstudy-account-gradebook' );
wp_enqueue_script( 'masterstudy-account-gradebook' );

$students_table_columns = array(
	array(
		'title' => esc_html__( 'Student', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'student',
	),
	array(
		'title' => esc_html__( 'Lessons:', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'lessons',
	),
	array(
		'title' => esc_html__( 'Quizzes:', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'quizzes',
	),
	array(
		'title' => esc_html__( 'Assignments:', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'assignments',
	),
	array(
		'title' => esc_html__( 'Progress:', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'progress',
	),
	array(
		'title' => esc_html__( 'Started:', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'started',
	),
);

wp_localize_script(
	'masterstudy-account-gradebook',
	'gradebook_data',
	array(
		'student_table_columns' => $students_table_columns,
	)
);
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-gradebook">
			<h1 class="masterstudy-account-gradebook__title"><?php echo esc_html__( 'The Gradebook', 'masterstudy-lms-learning-management-system-pro' ); ?></h1>
			<div class="masterstudy-account-gradebook__course">
				<div class="masterstudy-account-gradebook__course-select">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/selects/instructor-courses-select',
						array(
							'select_id'      => 'masterstudy-account-gradebook__course-select-input',
							'select_on_load' => true,
							'select_options' => array(
								'placeholder' => esc_html__( 'Select course', 'masterstudy-lms-learning-management-system-pro' ),
								'clearable'   => false,
							),
						)
					);
					?>
				</div>
				<div class="masterstudy-account-gradebook__course-stats">
					<div class="masterstudy-account-gradebook__stat">
						<span class="masterstudy-account-gradebook__stat-title"><?php echo esc_html__( 'All time course students', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
						<span class="masterstudy-account-gradebook__stat-value masterstudy-account-gradebook__stat-students-count"></span>
					</div>
					<div class="masterstudy-account-gradebook__stat">
						<span class="masterstudy-account-gradebook__stat-title"><?php echo esc_html__( 'Course average progress', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
						<span class="masterstudy-account-gradebook__stat-value masterstudy-account-gradebook__stat-avg-progress"></span>
					</div>
					<div class="masterstudy-account-gradebook__stat">
						<span class="masterstudy-account-gradebook__stat-title"><?php echo esc_html__( 'Course passed quizzes', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
						<span class="masterstudy-account-gradebook__stat-value masterstudy-account-gradebook__stat-quizzes"></span>
					</div>
					<div class="masterstudy-account-gradebook__stat">
						<span class="masterstudy-account-gradebook__stat-title"><?php echo esc_html__( 'Course passed lessons', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
						<span class="masterstudy-account-gradebook__stat-value masterstudy-account-gradebook__stat-lessons"></span>
					</div>
					<div class="masterstudy-account-gradebook__stat">
						<span class="masterstudy-account-gradebook__stat-title"><?php echo esc_html__( 'Course enrolled by subscription', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
						<span class="masterstudy-account-gradebook__stat-value masterstudy-account-gradebook__stat-subs-enroll"></span>
					</div>
					<div class="masterstudy-account-gradebook__stat">
						<span class="masterstudy-account-gradebook__stat-title"><?php echo esc_html__( 'Course passed assignments', 'masterstudy-lms-learning-management-system-pro' ); ?>:</span>
						<span class="masterstudy-account-gradebook__stat-value masterstudy-account-gradebook__stat-assignments"></span>
					</div>
				</div>
			</div>

			<div class="masterstudy-account-gradebook__students">
				<?php
				STM_LMS_Templates::show_lms_template( 'components/skeleton-loader', array( 'loader_type' => 'table-loader' ) );
				STM_LMS_Templates::show_lms_template(
					'components/analytics/datatable',
					array(
						'id'      => 'students',
						'columns' => $students_table_columns,
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
