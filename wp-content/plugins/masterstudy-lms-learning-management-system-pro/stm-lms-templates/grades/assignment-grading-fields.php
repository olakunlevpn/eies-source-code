<?php
/**
 * @var $user_assignment_id
 */

use MasterStudy\Lms\Pro\addons\assignments\Repositories\AssignmentStudentRepository;
use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

$grades_calculator = GradeCalculator::get_instance();
$default_format    = STM_LMS_Options::get_option( 'grades_display', 'grade' );
$grades_table      = $grades_calculator->get_grades_table();
$passing_grade     = AssignmentStudentRepository::get_passing_grade( $user_assignment_id );
$grade_type        = get_post_meta( $user_assignment_id, 'grade_type', true );
$grade_percent     = ( new AssignmentStudentRepository() )->get_grade( $user_assignment_id );
$grade_values      = $grades_calculator->calculate( $grade_percent );

if ( empty( $grade_type ) ) {
	$grade_type = $default_format;
}
?>
<div class="masterstudy-lms-assignment-grade">
	<select name="grade-type" class="masterstudy-lms-assignment-grade__type disable-select">
		<option value="grade" <?php selected( $grade_type, 'grade' ); ?>>
			<?php echo esc_html__( 'Grade', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</option>
		<option value="point" <?php selected( $grade_type, 'point' ); ?>>
			<?php echo esc_html__( 'Point', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</option>
		<option value="percent" <?php selected( $grade_type, 'percent' ); ?>>
			<?php echo esc_html__( 'Percent (%)', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</option>
	</select>
	<select name="grade" class="masterstudy-lms-assignment-grade__field field-grade disable-select">
		<option value="">N/A</option>
		<?php foreach ( $grades_table as $grade ) : ?>
			<option value="<?php echo esc_attr( $grade['grade'] ); ?>" <?php selected( $grade_values['grade'] ?? '', $grade['grade'] ); ?>>
				<?php echo esc_html( $grade['grade'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<select name="point" class="masterstudy-lms-assignment-grade__field field-point disable-select">
		<option value="">N/A</option>
		<?php foreach ( $grades_table as $grade ) : ?>
			<option value="<?php echo esc_attr( $grade['point'] ); ?>" <?php selected( $grade_values['point'] ?? '', $grade['point'] ); ?>>
				<?php echo esc_html( $grade['point'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<input type="number" name="percent" class="masterstudy-lms-assignment-grade__field field-percent" value="<?php echo esc_attr( $grade_percent ); ?>"
		min="<?php echo esc_attr( $grades_calculator->get_min_range() ); ?>" max="<?php echo esc_attr( $grades_calculator->get_max_range() ); ?>">

	<div class="masterstudy-lms-assignment-grade__hints">
		<?php if ( ! empty( $passing_grade ) ) { ?>
			<small class="masterstudy-lms-assignment-grade__hint hint-grade">
				<?php
				printf(
					/* translators: %s: Grade */
					esc_html__( 'Minimal passing grade is: %s', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( $grades_calculator->get_passing_grade( $passing_grade, 'grade' ) )
				)
				?>
			</small>
			<small class="masterstudy-lms-assignment-grade__hint hint-point">
				<?php
				printf(
					/* translators: %s: Point */
					esc_html__( 'Minimal passing point is: %s', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( $grades_calculator->get_passing_grade( $passing_grade, 'point' ) )
				)
				?>
			</small>
			<small class="masterstudy-lms-assignment-grade__hint hint-percent">
				<?php
				printf(
					/* translators: %s: Percent */
					esc_html__( 'Minimal passing percent is: %s', 'masterstudy-lms-learning-management-system-pro' ),
					esc_html( $grades_calculator->get_passing_grade( $passing_grade, 'percent' ) )
				)
				?>
			</small>
		<?php } else { ?>
			<small class="masterstudy-lms-assignment-grade__empty">
				<?php echo esc_html__( 'Minimum passing grade is not set.', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</small>
		<?php } ?>
	</div>
</div>
