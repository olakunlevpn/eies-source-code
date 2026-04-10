<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_Enrollments extends EIES_Migration_Base {

	public function run() {
		$enrol_table = $this->moodle_table( 'enrol' );
		$ue_table = $this->moodle_table( 'user_enrolments' );

		$enrollments = $this->moodle_db->get_results(
			"SELECT ue.id, ue.userid, e.courseid, ue.status, ue.timestart, ue.timeend
			 FROM {$ue_table} ue
			 JOIN {$enrol_table} e ON ue.enrolid = e.id
			 ORDER BY ue.id ASC"
		);

		if ( empty( $enrollments ) ) {
			return array( 'success' => false, 'message' => 'No enrollments found.' );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'stm_lms_user_courses';
		$count = 0;
		$skipped = 0;

		foreach ( $enrollments as $enrol ) {
			$wp_user_id = $this->get_wp_id( 'user', $enrol->userid );
			$wp_course_id = $this->get_wp_id( 'course', $enrol->courseid );

			if ( ! $wp_user_id || ! $wp_course_id ) {
				$skipped++;
				continue;
			}

			// Check if already enrolled
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT user_course_id FROM {$table} WHERE user_id = %d AND course_id = %d",
				$wp_user_id, $wp_course_id
			) );

			if ( $exists ) {
				$count++;
				continue;
			}

			// Get course completion grade from Moodle
			$progress = $this->get_course_progress( $enrol->userid, $enrol->courseid );

			$start_time = $enrol->timestart > 0 ? $enrol->timestart : time();

			$wpdb->insert( $table, array(
				'user_id'          => (int) $wp_user_id,
				'course_id'        => (int) $wp_course_id,
				'progress_percent' => $progress['percent'],
				'final_grade'      => $progress['grade'],
				'status'           => $progress['percent'] >= 100 ? 'completed' : 'enrolled',
				'start_time'       => $start_time,
				'end_time'         => $enrol->timeend > 0 ? $enrol->timeend : 0,
			) );

			// Update course student count
			$student_count = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE course_id = %d",
				$wp_course_id
			) );
			update_post_meta( (int) $wp_course_id, 'current_students', $student_count );

			$count++;
		}

		return array(
			'success' => true,
			'message' => sprintf( '%d enrollments migrated, %d skipped (missing user/course).', $count, $skipped ),
		);
	}

	private function get_course_progress( $moodle_user_id, $moodle_course_id ) {
		$gi_table = $this->moodle_table( 'grade_items' );
		$gg_table = $this->moodle_table( 'grade_grades' );

		// Get final course grade
		$grade = $this->moodle_db->get_row(
			$this->moodle_db->prepare(
				"SELECT gg.finalgrade, gi.grademax
				 FROM {$gg_table} gg
				 JOIN {$gi_table} gi ON gg.itemid = gi.id
				 WHERE gi.courseid = %d
				   AND gi.itemtype = 'course'
				   AND gg.userid = %d",
				$moodle_course_id, $moodle_user_id
			)
		);

		$percent = 0;
		$final_grade = 0;

		if ( $grade && $grade->grademax > 0 && $grade->finalgrade !== null ) {
			$percent = round( ( $grade->finalgrade / $grade->grademax ) * 100, 2 );
			// C8 FIX: Convert to percentage (0-100), cap at 127 for TINYINT column
			$final_grade = min( round( ( $grade->finalgrade / $grade->grademax ) * 100 ), 127 );
		}

		// Also check completion
		$cc_table = $this->moodle_table( 'course_completions' );
		$completion = $this->moodle_db->get_var(
			$this->moodle_db->prepare(
				"SELECT timecompleted FROM {$cc_table} WHERE course = %d AND userid = %d",
				$moodle_course_id, $moodle_user_id
			)
		);

		if ( $completion && $completion > 0 ) {
			$percent = 100;
		}

		return array(
			'percent' => min( $percent, 100 ),
			'grade'   => $final_grade,
		);
	}
}
