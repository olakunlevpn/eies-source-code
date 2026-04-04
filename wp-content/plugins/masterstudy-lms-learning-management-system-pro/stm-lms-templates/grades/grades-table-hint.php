<?php

use MasterStudy\Lms\Pro\AddonsPlus\Grades\Services\GradeCalculator;

$grades_table = GradeCalculator::get_instance()->get_grades_table();

if ( empty( $grades_table ) ) { ?>
	<p class="masterstudy-assignment__attachments_empty">
		<?php echo esc_html__( 'Please set up the Grades Table', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</p>
	<?php

	return;
}

$grades_hint = ! empty( $grades_table[1] ) ? $grades_table[1] : $grades_table[0];
?>
<div class="masterstudy-grades-hint">
	<div class="masterstudy-grades-hint__title">
		<strong><?php echo esc_html__( 'Hint:', 'masterstudy-lms-learning-management-system-pro' ); ?></strong>
		<?php
		echo sprintf(
			/* translators: %1$s: Point, %2$s: Range Start, %3$s: Range End, %4$s: Grade */
			esc_html__( '%1$s Points = %2$s%% - %3$s%% or "%4$s" grade.', 'masterstudy-lms-learning-management-system-pro' ),
			esc_html( $grades_hint['point'] ),
			esc_html( $grades_hint['range'][0] ),
			esc_html( $grades_hint['range'][1] ),
			esc_html( $grades_hint['grade'] )
		);
		?>
		<br/>
		<?php echo esc_html__( 'The highest percentage for chosen Grade or Point will be saved in the system.', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</div>
	<div class="masterstudy-grades-hint__button">
		<?php echo esc_html__( 'See Table', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</div>
</div>

<div class="masterstudy-alert masterstudy-grades-table">
	<div class="masterstudy-alert__wrapper">
		<div class="masterstudy-alert__container">
			<div class="masterstudy-alert__header">
				<span class="masterstudy-alert__header-title">
					<?php echo esc_html__( 'Grades Table', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
				<span class="masterstudy-alert__header-close"></span>
			</div>
			<div class="masterstudy-alert__text">
				<table class="masterstudy-grades-table-hint__table">
					<thead>
					<tr>
						<th><?php echo esc_html__( 'Grade', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
						<th><?php echo esc_html__( 'Point', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
						<th><?php echo esc_html__( 'Range', 'masterstudy-lms-learning-management-system-pro' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $grades_table as $grade ) : ?>
						<tr>
							<td>
								<span class="grade-badge" style="background-color: <?php echo esc_attr( $grade['color'] ); ?>"><?php echo esc_html( $grade['grade'] ); ?></span>
							</td>
							<td><?php echo esc_html( $grade['point'] ); ?></td>
							<td>
								<?php echo esc_html( $grade['range'][0] ); ?>% - <?php echo esc_html( $grade['range'][1] ); ?>%
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
