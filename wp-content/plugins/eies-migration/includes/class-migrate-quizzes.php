<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EIES_Migrate_Quizzes extends EIES_Migration_Base {

	public function run() {
		$quiz_table = $this->moodle_table( 'quiz' );
		$quizzes = $this->moodle_db->get_results(
			"SELECT id, course, name, intro, timeopen, timeclose, timelimit, grade, attempts
			 FROM {$quiz_table}
			 ORDER BY id ASC"
		);

		if ( empty( $quizzes ) ) {
			return array( 'success' => false, 'message' => 'No quizzes found.' );
		}

		$quiz_count = 0;
		$question_count = 0;

		foreach ( $quizzes as $quiz ) {
			// Skip if already migrated
			if ( $this->get_wp_id( 'quiz', $quiz->id ) ) {
				$quiz_count++;
				continue;
			}

			$wp_course_id = $this->get_wp_id( 'course', $quiz->course );
			$author = $wp_course_id ? get_post_field( 'post_author', $wp_course_id ) : 1;

			// Create quiz post
			$quiz_post_id = wp_insert_post( array(
				'post_type'    => 'stm-quizzes',
				'post_title'   => trim( $quiz->name ),
				'post_content' => wp_kses_post( $quiz->intro ?? '' ),
				'post_status'  => 'publish',
				'post_author'  => $author,
			) );

			if ( is_wp_error( $quiz_post_id ) || ! $quiz_post_id ) {
				continue;
			}

			// Set quiz meta
			$duration = $quiz->timelimit > 0 ? ceil( $quiz->timelimit / 60 ) : 0;
			update_post_meta( $quiz_post_id, 'duration', $duration );
			update_post_meta( $quiz_post_id, 'duration_measure', 'minutes' );

			$passing = $quiz->grade > 0 ? 50 : 0; // Default 50% pass mark
			update_post_meta( $quiz_post_id, 'passing_grade', $passing );
			update_post_meta( $quiz_post_id, 're_take_cut', '0' );
			update_post_meta( $quiz_post_id, 'correct_answer', '1' );

			// Migrate questions for this quiz
			$q_count = $this->migrate_quiz_questions( $quiz->id, $quiz_post_id );
			$question_count += $q_count;

			// Link quiz to course curriculum if course exists
			if ( $wp_course_id ) {
				$this->link_quiz_to_course( $quiz->id, $quiz_post_id, $wp_course_id );
			}

			$this->save_mapping( 'quiz', $quiz->id, $quiz_post_id );
			$quiz_count++;
		}

		return array(
			'success' => true,
			'message' => sprintf( '%d quizzes and %d questions migrated.', $quiz_count, $question_count ),
		);
	}

	private function migrate_quiz_questions( $moodle_quiz_id, $wp_quiz_id ) {
		$slots_table = $this->moodle_table( 'quiz_slots' );
		$q_table = $this->moodle_table( 'question' );
		$qa_table = $this->moodle_table( 'question_answers' );

		// Get questions via quiz slots
		$questions = $this->moodle_db->get_results(
			$this->moodle_db->prepare(
				"SELECT q.id, q.name, q.questiontext, q.qtype, q.defaultmark, qs.slot
				 FROM {$slots_table} qs
				 JOIN {$q_table} q ON qs.questionid = q.id
				 WHERE qs.quizid = %d
				   AND q.qtype != 'random'
				   AND q.qtype != 'description'
				 ORDER BY qs.slot ASC",
				$moodle_quiz_id
			)
		);

		$count = 0;
		$question_ids = array();

		foreach ( $questions as $q ) {
			// Skip if already migrated
			$existing = $this->get_wp_id( 'question', $q->id );
			if ( $existing ) {
				$question_ids[] = (int) $existing;
				$count++;
				continue;
			}

			$stm_type = $this->map_question_type( $q->qtype );
			if ( ! $stm_type ) continue;

			// Get answers
			$answers = $this->moodle_db->get_results(
				$this->moodle_db->prepare(
					"SELECT id, answer, fraction, feedback FROM {$qa_table} WHERE question = %d ORDER BY id ASC",
					$q->id
				)
			);

			$stm_answers = $this->format_answers( $q->qtype, $answers, $q->questiontext );

			// Create question post
			$q_post_id = wp_insert_post( array(
				'post_type'    => 'stm-questions',
				'post_title'   => trim( $q->name ),
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => get_post_field( 'post_author', $wp_quiz_id ),
			) );

			if ( is_wp_error( $q_post_id ) || ! $q_post_id ) continue;

			// Set question meta
			update_post_meta( $q_post_id, 'type', $stm_type );
			update_post_meta( $q_post_id, 'question', wp_kses_post( $q->questiontext ) );
			update_post_meta( $q_post_id, 'answers', $stm_answers );

			$this->save_mapping( 'question', $q->id, $q_post_id );
			$question_ids[] = $q_post_id;
			$count++;
		}

		// Save question list on quiz
		if ( ! empty( $question_ids ) ) {
			update_post_meta( $wp_quiz_id, 'questions', $question_ids );
		}

		return $count;
	}

	private function map_question_type( $moodle_type ) {
		$map = array(
			'multichoice'   => null, // Determined per-question (single vs multi)
			'truefalse'     => 'single_choice',
			'essay'         => 'keywords',
			'match'         => 'item_match',
			'gapselect'     => 'fill_the_gap',
			'shortanswer'   => 'keywords',
			'numerical'     => 'keywords',
			'ddimageortext' => 'single_choice',
			'ddmarker'      => 'single_choice',
			'ddwtos'        => 'fill_the_gap',
		);

		if ( $moodle_type === 'multichoice' ) {
			return 'multichoice_placeholder';
		}

		return $map[ $moodle_type ] ?? null;
	}

	private function format_answers( $qtype, $answers, $question_text = '' ) {
		$stm_answers = array();

		switch ( $qtype ) {
			case 'multichoice':
				// Check if single or multi answer
				$correct_count = 0;
				foreach ( $answers as $a ) {
					if ( $a->fraction > 0 ) $correct_count++;
				}
				$is_multi = $correct_count > 1;

				foreach ( $answers as $a ) {
					$stm_answers[] = array(
						'text'    => wp_strip_all_tags( $a->answer ),
						'isTrue'  => $a->fraction > 0,
					);
				}

				// Update the type meta later based on this
				return array(
					'type'    => $is_multi ? 'multi_choice' : 'single_choice',
					'answers' => $stm_answers,
				);

			case 'truefalse':
				foreach ( $answers as $a ) {
					$stm_answers[] = array(
						'text'    => wp_strip_all_tags( $a->answer ),
						'isTrue'  => $a->fraction > 0,
					);
				}
				return array( 'type' => 'single_choice', 'answers' => $stm_answers );

			case 'essay':
				return array( 'type' => 'keywords', 'answers' => array() );

			case 'shortanswer':
			case 'numerical':
				foreach ( $answers as $a ) {
					$stm_answers[] = array(
						'text'   => wp_strip_all_tags( $a->answer ),
						'isTrue' => true,
					);
				}
				return array( 'type' => 'keywords', 'answers' => $stm_answers );

			case 'match':
				$match_table = $this->moodle_table( 'qtype_match_subquestions' );
				// Match questions store pairs in a separate table
				$pairs = $this->moodle_db->get_results(
					"SELECT questiontext, answertext FROM {$match_table} WHERE questionid = " . (int) $answers[0]->question . " AND questiontext != ''"
				);
				foreach ( $pairs as $p ) {
					$stm_answers[] = array(
						'question' => wp_strip_all_tags( $p->questiontext ),
						'answer'   => wp_strip_all_tags( $p->answertext ),
					);
				}
				return array( 'type' => 'item_match', 'answers' => $stm_answers );

			case 'gapselect':
			case 'ddwtos':
				// Extract gaps from question text
				$stm_answers = array();
				if ( preg_match_all( '/\[\[(\d+)\]\]/', $question_text, $matches ) ) {
					foreach ( $answers as $a ) {
						$stm_answers[] = array(
							'text'   => wp_strip_all_tags( $a->answer ),
							'isTrue' => $a->fraction > 0,
						);
					}
				}
				return array( 'type' => 'fill_the_gap', 'answers' => $stm_answers );

			default:
				return array( 'type' => 'single_choice', 'answers' => array() );
		}
	}

	private function link_quiz_to_course( $moodle_quiz_id, $wp_quiz_id, $wp_course_id ) {
		global $wpdb;

		// Find the quiz's course module to locate which section it belongs to
		$cm_table = $this->moodle_table( 'course_modules' );
		$modules_table = $this->moodle_table( 'modules' );

		$cm = $this->moodle_db->get_row(
			$this->moodle_db->prepare(
				"SELECT cm.id, cm.section
				 FROM {$cm_table} cm
				 JOIN {$modules_table} m ON cm.module = m.id
				 WHERE m.name = 'quiz' AND cm.instance = %d
				 LIMIT 1",
				$moodle_quiz_id
			)
		);

		if ( ! $cm ) return;

		// Find the corresponding WP section
		$sections = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, `order` FROM {$wpdb->prefix}stm_lms_curriculum_sections WHERE course_id = %d ORDER BY `order` ASC",
			$wp_course_id
		) );

		// Get Moodle section index
		$section_table = $this->moodle_table( 'course_sections' );
		$moodle_section = $this->moodle_db->get_var(
			$this->moodle_db->prepare(
				"SELECT section FROM {$section_table} WHERE id = %d",
				$cm->section
			)
		);

		$target_section = null;
		if ( isset( $sections[ (int) $moodle_section ] ) ) {
			$target_section = $sections[ (int) $moodle_section ]->id;
		} elseif ( ! empty( $sections ) ) {
			$target_section = end( $sections )->id;
		}

		if ( $target_section ) {
			// Check if already linked
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}stm_lms_curriculum_materials WHERE post_id = %d AND section_id = %d",
				$wp_quiz_id, $target_section
			) );

			if ( ! $exists ) {
				$max_order = (int) $wpdb->get_var( $wpdb->prepare(
					"SELECT MAX(`order`) FROM {$wpdb->prefix}stm_lms_curriculum_materials WHERE section_id = %d",
					$target_section
				) );

				$wpdb->insert(
					$wpdb->prefix . 'stm_lms_curriculum_materials',
					array(
						'post_id'    => $wp_quiz_id,
						'post_type'  => 'stm-quizzes',
						'section_id' => $target_section,
						'order'      => $max_order + 1,
					)
				);
			}
		}
	}
}
