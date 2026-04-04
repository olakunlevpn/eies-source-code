<?php
/**
 * @var object $course
 * @var integer $user_id
 * @var boolean $in_sidebar
 */

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

wp_enqueue_style( 'masterstudy-single-course-grade' );
wp_enqueue_script( 'masterstudy-single-course-grade' );
wp_localize_script(
	'masterstudy-single-course-grade',
	'course_grade',
	array(
		'course_id'       => $course->id,
		'attempts'        => esc_html__( 'attempts', 'masterstudy-lms-learning-management-system-pro' ),
		'grade_separator' => esc_js( STM_LMS_Options::get_option( 'grades_scores_separator', '/' ) ),
		'not_started'     => esc_html__( 'Not finished', 'masterstudy-lms-learning-management-system-pro' ),
	)
);

STM_LMS_Templates::show_lms_template(
	'components/grade-details',
	array(
		'only_exams' => true,
		'in_popup'   => true,
	)
);

$is_logged   = is_user_logged_in();
$user_course = class_exists( 'STM_LMS_Course' ) ? STM_LMS_Course::get_user_course( $user_id, $course->id ) : false;
$in_sidebar  = $in_sidebar ?? false;
$message     = ( $is_logged && empty( $user_course ) )
	? esc_html__( 'Gradebook will be available after enrollment', 'masterstudy-lms-learning-management-system-pro' )
	: esc_html__( 'Sign in to account to see your Grade', 'masterstudy-lms-learning-management-system-pro' );
$is_graded   = intval( $user_course['final_grade'] ?? 0 ) > 0;

if ( $is_graded ) {
	$grade      = GradeCalculator::get_instance()->calculate( $user_course['final_grade'] );
	$grade_data = array(
		'badge'   => $grade['grade'] ?? '',
		'current' => $grade['point'] ?? 0,
		'range'   => $user_course['final_grade'] ?? 0,
		'color'   => $grade['color'] ?? '',
	);
}

if ( $is_logged && ! empty( $user_course ) ) {
	if ( $is_graded ) {
		?>
		<div class="masterstudy-single-course-grades <?php echo $in_sidebar ? 'masterstudy-single-course-grades_sidebar' : ''; ?>">
			<div class="masterstudy-single-course-grades__title">
				<?php echo esc_html__( 'Your Grade', 'masterstudy-lms-learning-management-system-pro' ); ?>:
			</div>
			<div class="masterstudy-single-course-grades__progress">
				<svg class="masterstudy-single-course-grades__progress-image">
					<circle class="masterstudy-single-course-grades__progress-line" cx="90" cy="90" r="80" />
					<circle
						class="masterstudy-single-course-grades__progress-fill"
						cx="90"
						cy="90"
						r="80"
						style="--grade-percent: <?php echo esc_attr( $grade_data['range'] ?? '0' ); ?>; stroke: <?php echo esc_attr( $grade_data['color'] ?? 'rgba(255, 255, 255, 1)' ); ?>;"
					/>
				</svg>
			</div>
			<div class="masterstudy-single-course-grades__badge" style="background:<?php echo esc_attr( $grade_data['color'] ?? 'rgba(238, 241, 247, 1)' ); ?>">
				<?php echo esc_html( $grade_data['badge'] ?? '' ); ?>
			</div>
			<div class="masterstudy-single-course-grades__points">
				<div id="course-grade-points" class="masterstudy-single-course-grades__points-block">
					<div class="masterstudy-single-course-grades__points-value"><?php echo esc_html( $grade_data['current'] ?? 0 ); ?></div>
					<div class="masterstudy-single-course-grades__points-label">
						<?php echo esc_html__( 'Grade Points', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
				</div>
				<span class="masterstudy-single-course-grades__points-separator"></span>
				<div id="course-grade-percent" class="masterstudy-single-course-grades__points-block">
					<div class="masterstudy-single-course-grades__points-value"><?php echo esc_html( $grade_data['range'] ?? 0 ); ?>%</div>
					<div class="masterstudy-single-course-grades__points-label">
						<?php echo esc_html__( 'Grade Range', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
				</div>
			</div>
			<div class="masterstudy-single-course-grades__description">
				<?php echo esc_html__( 'Your grade has been calculated based on your performance in quizzes and assignments.', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-single-course-grades__details-button">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Grade Details', 'masterstudy-lms-learning-management-system-pro' ),
						'style' => 'primary',
						'size'  => 'sm',
						'link'  => '',
						'id'    => 'show-grade-details',
					)
				);
				?>
			</div>
		</div>
	<?php } else { ?>
		<div class="masterstudy-single-course-grades__message masterstudy-single-course-grades__message_regenerate <?php echo $in_sidebar ? 'masterstudy-single-course-grades__message_regenerate_sidebar' : ''; ?>">
			<div class="masterstudy-single-course-grades__message-text">
				<?php echo esc_html__( 'Course Grade is not generated yet', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
			<div class="masterstudy-single-course-grades__message-button">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Generate Grade', 'masterstudy-lms-learning-management-system-pro' ),
						'style' => 'primary',
						'size'  => 'sm',
						'link'  => '',
						'id'    => 'regenerate-course-grade',
					)
				);
				?>
			</div>
		</div>
		<?php
	}
} else {
	?>
	<div class="masterstudy-single-course-grades__message <?php echo $in_sidebar ? 'masterstudy-single-course-grades__message_sidebar' : ''; ?>">
		<div class="masterstudy-single-course-grades__message-text">
			<?php echo esc_html( $message ); ?>
		</div>
		<?php if ( ! $is_logged ) { ?>
			<div class="masterstudy-single-course-grades__message-button">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Sign In', 'masterstudy-lms-learning-management-system-pro' ),
						'style' => 'primary',
						'size'  => 'sm',
						'link'  => '',
						'id'    => 'authorization',
						'login' => true,
					)
				);
				?>
			</div>
		<?php } ?>
	</div>
	<?php
}
