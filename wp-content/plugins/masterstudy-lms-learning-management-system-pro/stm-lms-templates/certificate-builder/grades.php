<?php
$separator = STM_LMS_Options::get_option( 'grades_scores_separator', '/' );
$exams     = array(
	array(
		'title'   => __( 'Fundamentals of Banking', 'masterstudy-lms-learning-management-system-pro' ),
		'grade'   => 'A+',
		'color'   => 'rgb(34, 122, 255)',
		'value'   => '4.7',
		'max'     => '5.0',
		'percent' => '95%',
		'type'    => 'stm-quizzes',
	),
	array(
		'title'   => __( 'Banking Regulations', 'masterstudy-lms-learning-management-system-pro' ),
		'grade'   => 'A',
		'color'   => 'rgb(97, 204, 47)',
		'value'   => '4.5',
		'max'     => '5.0',
		'percent' => '89%',
		'type'    => 'stm-quizzes',
	),
	array(
		'title'   => __( 'Financial Markets', 'masterstudy-lms-learning-management-system-pro' ),
		'grade'   => 'B',
		'color'   => 'rgb(255, 168, 0)',
		'value'   => '3.0',
		'max'     => '5.0',
		'percent' => '59%',
		'type'    => 'stm-assignments',
	),
	array(
		'title'   => __( 'Risk Management', 'masterstudy-lms-learning-management-system-pro' ),
		'grade'   => 'A+',
		'color'   => 'rgb(255, 57, 69)',
		'value'   => '2.0',
		'max'     => '5.0',
		'percent' => '39%',
		'type'    => 'stm-assignments',
	),
	array(
		'title'       => __( 'Investment Banking', 'masterstudy-lms-learning-management-system-pro' ),
		'not_started' => true,
		'type'        => 'stm-quizzes',
	),
);
?>

<div class="masterstudy-grades-certificate">
	<div class="masterstudy-grades-certificate__main">
		<div class="masterstudy-grades-certificate__badge">
			<div class="masterstudy-grades-certificate__badge-value">B+</div>
			<div class="masterstudy-grades-certificate__badge-label">
				<?php echo esc_html__( 'Grade', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
		</div>
		<div class="masterstudy-grades-certificate__points">
			<div class="masterstudy-grades-certificate__points-value">4.0</div>
			<div class="masterstudy-grades-certificate__points-label">
				<?php echo esc_html__( 'Grade Points', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
		</div>
		<div class="masterstudy-grades-certificate__range">
			<div class="masterstudy-grades-certificate__range-value">78%</div>
			<div class="masterstudy-grades-certificate__range-label">
				<?php echo esc_html__( 'Grade Range', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
		</div>
	</div>
	<div class="masterstudy-grades-certificate__exams">
		<div class="masterstudy-grades-certificate__exams-title">
			<?php echo esc_html__( 'Exams', 'masterstudy-lms-learning-management-system-pro' ); ?>:
		</div>
		<table class="masterstudy-grades-certificate__exams-table">
			<tbody>
				<?php foreach ( $exams as $exam ) { ?>
					<tr class="masterstudy-grades-certificate__exams-row masterstudy-grades-certificate__exams-row-<?php echo esc_attr( $exam['type'] ); ?>">
						<td class="masterstudy-grades-certificate__exams-label">
							<div class="masterstudy-grades-certificate__exams-icon"></div>
							<?php echo esc_html( $exam['title'] ); ?>
						</td>
						<?php if ( ! empty( $exam['not_started'] ) ) { ?>
							<td colspan="3" class="masterstudy-grades-certificate__exams-not-started">
								<?php echo esc_html__( 'Not started', 'masterstudy-lms-learning-management-system-pro' ); ?>
							</td>
						<?php } else { ?>
							<td class="masterstudy-grades-certificate__exams-grade">
								<span class="masterstudy-grades-certificate__exams-badge" style="background:<?php echo esc_attr( $exam['color'] ); ?>">
									<?php echo esc_html( $exam['grade'] ); ?>
								</span>
							</td>
							<td class="masterstudy-grades-certificate__exams-value">
								<?php echo esc_html( $exam['value'] . $separator . $exam['max'] ); ?>
							</td>
							<td class="masterstudy-grades-certificate__exams-percent">
								<?php echo esc_html( $exam['percent'] ); ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
