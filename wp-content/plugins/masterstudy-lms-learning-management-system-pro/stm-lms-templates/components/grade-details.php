<?php
/**
 * @var boolean $only_exams
 * @var boolean $in_popup
 */

wp_enqueue_style( 'masterstudy-grade-details' );

$only_exams = isset( $only_exams ) ? $only_exams : false;
$in_popup   = isset( $in_popup ) ? $in_popup : false;
?>

<div class="masterstudy-grade-details <?php echo $only_exams ? 'masterstudy-grade-details_only-exams' : ''; ?> <?php echo $in_popup ? 'masterstudy-grade-details_popup' : ''; ?>">
	<div class="masterstudy-grade-details__block">
		<div class="masterstudy-grade-details__loader">
			<div class="masterstudy-grade-details__loader-body"></div>
		</div>
		<div class="masterstudy-grade-details__content">
			<div class="masterstudy-grade-details__header">
				<div class="masterstudy-grade-details__title">
					<?php echo esc_html__( 'Grade details', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</div>
				<span class="masterstudy-grade-details__close"></span>
			</div>
			<div class="masterstudy-grade-details__course">
				<div class="masterstudy-grade-details__course-label">
					<?php echo esc_html__( 'Course', 'masterstudy-lms-learning-management-system-pro' ); ?>:
				</div>
				<div class="masterstudy-grade-details__course-title"></div>
			</div>
			<div class="masterstudy-grade-details__student">
				<div class="masterstudy-grade-details__student-label">
					<?php echo esc_html__( 'Student', 'masterstudy-lms-learning-management-system-pro' ); ?>:
				</div>
				<div class="masterstudy-grade-details__student-name"></div>
			</div>
			<div class="masterstudy-grade-details__date">
				<div class="masterstudy-grade-details__date-block">
					<div class="masterstudy-grade-details__date-label">
						<?php echo esc_html__( 'Enrollment date', 'masterstudy-lms-learning-management-system-pro' ); ?>:
					</div>
					<div id="enroll-date" class="masterstudy-grade-details__date-value"></div>
				</div>
				<div class="masterstudy-grade-details__date-block">
					<div class="masterstudy-grade-details__date-label">
						<?php echo esc_html__( 'Course completion date', 'masterstudy-lms-learning-management-system-pro' ); ?>:
					</div>
					<div id="complete-date" class="masterstudy-grade-details__date-value"></div>
				</div>
			</div>
			<div class="masterstudy-grade-details__mark">
				<div class="masterstudy-grade-details__mark-label">
					<?php echo esc_html__( 'Grade', 'masterstudy-lms-learning-management-system-pro' ); ?>:
				</div>
				<div class="masterstudy-grade-details__mark-progress">
					<svg class="masterstudy-grade-details__mark-progress-image">
						<circle class="masterstudy-grade-details__mark-progress-line" cx="90" cy="90" r="80" />
						<circle
							class="masterstudy-grade-details__mark-progress-fill"
							cx="90"
							cy="90"
							r="80"
						/>
					</svg>
				</div>
				<div class="masterstudy-grade-details__mark-badge"></div>
				<div class="masterstudy-grade-details__mark-points">
					<div id="grade-points" class="masterstudy-grade-details__mark-points-block">
						<div class="masterstudy-grade-details__mark-points-value"></div>
						<div class="masterstudy-grade-details__mark-points-label">
							<?php echo esc_html__( 'Grade Points', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</div>
					</div>
					<span class="masterstudy-grade-details__mark-points-separator"></span>
					<div id="grade-percent" class="masterstudy-grade-details__mark-points-block">
						<div class="masterstudy-grade-details__mark-points-value"></div>
						<div class="masterstudy-grade-details__mark-points-label">
							<?php echo esc_html__( 'Grade Range', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="masterstudy-grade-details__regenerate">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'title' => esc_html__( 'Regenerate Grades', 'masterstudy-lms-learning-management-system-pro' ),
						'style' => 'primary',
						'size'  => 'sm',
						'link'  => '#',
						'id'    => 'regenerate-grades',
					)
				);
				?>
			</div>
			<div class="masterstudy-grade-details__exams">
				<div class="masterstudy-grade-details__exams-title">
					<?php echo esc_html__( 'Exams', 'masterstudy-lms-learning-management-system-pro' ); ?>:
				</div>
				<div class="masterstudy-grade-details__exams-list">	</div>
			</div>
		</div>
	</div>
</div>
