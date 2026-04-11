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
		$errors = array();
		$skipped_random = 0;

		foreach ( $quizzes as $quiz ) {
			if ( $this->get_wp_id( 'quiz', $quiz->id ) ) {
				$quiz_count++;
				continue;
			}

			$wp_course_id = $this->get_wp_id( 'course', $quiz->course );
			$author = $wp_course_id ? get_post_field( 'post_author', $wp_course_id ) : 1;

			$quiz_post_id = wp_insert_post( array(
				'post_type'    => 'stm-quizzes',
				'post_title'   => trim( $quiz->name ),
				'post_content' => $this->clean_moodle_html( $quiz->intro ?? '' ),
				'post_status'  => 'publish',
				'post_author'  => $author,
			) );

			if ( is_wp_error( $quiz_post_id ) || ! $quiz_post_id ) {
				$errors[] = sprintf( 'Quiz %d (%s): %s', $quiz->id, $quiz->name, is_wp_error( $quiz_post_id ) ? $quiz_post_id->get_error_message() : 'Insert failed' );
				continue;
			}

			$duration = $quiz->timelimit > 0 ? ceil( $quiz->timelimit / 60 ) : 0;
			update_post_meta( $quiz_post_id, 'duration', $duration );
			update_post_meta( $quiz_post_id, 'duration_measure', 'minutes' );

			$passing = $quiz->grade > 0 ? 50 : 0;
			update_post_meta( $quiz_post_id, 'passing_grade', $passing );
			update_post_meta( $quiz_post_id, 're_take_cut', '0' );
			update_post_meta( $quiz_post_id, 'correct_answer', '1' );

			$result = $this->migrate_quiz_questions( $quiz->id, $quiz_post_id );
			$question_count += $result['count'];
			$skipped_random += $result['skipped_random'];

			if ( $wp_course_id ) {
				$this->link_quiz_to_course( $quiz->id, $quiz_post_id, $wp_course_id );
			}

			$this->save_mapping( 'quiz', $quiz->id, $quiz_post_id );
			$quiz_count++;
		}

		$msg = sprintf( '%d quizzes and %d questions migrated.', $quiz_count, $question_count );
		if ( $skipped_random > 0 ) {
			$msg .= sprintf( ' %d random question slots skipped.', $skipped_random );
		}
		if ( ! empty( $errors ) ) {
			$msg .= sprintf( ' %d errors: %s', count( $errors ), implode( '; ', array_slice( $errors, 0, 5 ) ) );
		}

		return array( 'success' => true, 'message' => $msg );
	}

	private function migrate_quiz_questions( $moodle_quiz_id, $wp_quiz_id ) {
		$slots_table = $this->moodle_table( 'quiz_slots' );
		$q_table = $this->moodle_table( 'question' );
		$qa_table = $this->moodle_table( 'question_answers' );

		$questions = $this->moodle_db->get_results(
			$this->moodle_db->prepare(
				"SELECT q.id, q.name, q.questiontext, q.qtype, q.defaultmark, qs.slot
				 FROM {$slots_table} qs
				 JOIN {$q_table} q ON qs.questionid = q.id
				 WHERE qs.quizid = %d
				   AND q.qtype != 'description'
				 ORDER BY qs.slot ASC",
				$moodle_quiz_id
			)
		);

		$count = 0;
		$skipped_random = 0;
		$question_ids = array();

		foreach ( $questions as $q ) {
			// Skip random questions with a count
			if ( $q->qtype === 'random' ) {
				$skipped_random++;
				continue;
			}

			$existing = $this->get_wp_id( 'question', $q->id );
			if ( $existing ) {
				$question_ids[] = (int) $existing;
				$count++;
				continue;
			}

			// Get answers from mdl_question_answers
			$answers = $this->moodle_db->get_results(
				$this->moodle_db->prepare(
					"SELECT id, answer, fraction, feedback FROM {$qa_table} WHERE question = %d ORDER BY id ASC",
					$q->id
				)
			);
			if ( ! is_array( $answers ) ) {
				$answers = array();
			}

			// Format answers and determine type
			$formatted = $this->format_answers( $q->qtype, $answers, $q->questiontext, $q->id );
			$stm_type = $formatted['type'];
			$stm_answers = $formatted['answers'];

			if ( ! $stm_type ) continue;

			$q_post_id = wp_insert_post( array(
				'post_type'    => 'stm-questions',
				'post_title'   => trim( $q->name ),
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => get_post_field( 'post_author', $wp_quiz_id ),
			) );

			if ( is_wp_error( $q_post_id ) || ! $q_post_id ) continue;

			// C3 FIX: Store the actual resolved type, not placeholder
			update_post_meta( $q_post_id, 'type', $stm_type );
			update_post_meta( $q_post_id, 'question', $this->clean_moodle_html( $q->questiontext ) );
			// C4 FIX: Store flat answers array, not the wrapper
			update_post_meta( $q_post_id, 'answers', $stm_answers );

			$this->save_mapping( 'question', $q->id, $q_post_id );
			$question_ids[] = $q_post_id;
			$count++;
		}

		if ( ! empty( $question_ids ) ) {
			// MasterStudy expects comma-separated string, NOT array
			update_post_meta( $wp_quiz_id, 'questions', implode( ',', $question_ids ) );
		}

		return array( 'count' => $count, 'skipped_random' => $skipped_random );
	}

	/**
	 * Format answers for MasterStudy LMS.
	 * Returns array with 'type' and 'answers' keys.
	 * 'answers' is always the FLAT array MasterStudy expects.
	 */
	private function format_answers( $qtype, $answers, $question_text, $question_id ) {
		$stm_answers = array();

		switch ( $qtype ) {
			case 'multichoice':
				// C3 FIX: Determine single vs multi and return correct type
				$correct_count = 0;
				foreach ( $answers as $a ) {
					if ( $a->fraction > 0 ) $correct_count++;
				}
				$type = $correct_count > 1 ? 'multi_choice' : 'single_choice';

				foreach ( $answers as $a ) {
					$stm_answers[] = array(
						'text'   => wp_strip_all_tags( $a->answer ),
						// C2 FIX: String '1'/'0' instead of boolean
						'isTrue' => $a->fraction > 0 ? '1' : '0',
					);
				}
				return array( 'type' => $type, 'answers' => $stm_answers );

			case 'truefalse':
				foreach ( $answers as $a ) {
					$stm_answers[] = array(
						'text'   => wp_strip_all_tags( $a->answer ),
						'isTrue' => $a->fraction > 0 ? '1' : '0',
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
						'isTrue' => '1',
					);
				}
				return array( 'type' => 'keywords', 'answers' => $stm_answers );

			case 'match':
				// C5 FIX: Use question_id parameter, not $answers[0]->question
				$match_table = $this->moodle_table( 'qtype_match_subquestions' );
				$pairs = $this->moodle_db->get_results(
					$this->moodle_db->prepare(
						"SELECT questiontext, answertext FROM {$match_table} WHERE questionid = %d AND questiontext != ''",
						$question_id
					)
				);
				if ( ! empty( $pairs ) ) {
					foreach ( $pairs as $p ) {
						$stm_answers[] = array(
							'question' => wp_strip_all_tags( $p->questiontext ),
							// C6 FIX: Use 'text' key, not 'answer'
							'text'     => wp_strip_all_tags( $p->answertext ),
						);
					}
				}
				return array( 'type' => 'item_match', 'answers' => $stm_answers );

			case 'gapselect':
			case 'ddwtos':
				// C1 FIX: Gapselect uses position-based answers, not fraction
				// [[N]] references the Nth answer by row position (1-indexed)
				$text = wp_strip_all_tags( $question_text );
				// Build position map: index 1,2,3... → answer text
				$answer_by_position = array();
				$pos = 1;
				foreach ( $answers as $a ) {
					$answer_by_position[ $pos ] = wp_strip_all_tags( $a->answer );
					$pos++;
				}
				// Replace [[1]], [[2]], etc with |answer|
				$text = preg_replace_callback( '/\[\[(\d+)\]\]/', function( $m ) use ( $answer_by_position ) {
					$n = (int) $m[1];
					$answer = isset( $answer_by_position[ $n ] ) ? $answer_by_position[ $n ] : '___';
					return '|' . $answer . '|';
				}, $text );

				$stm_answers[] = array(
					'text'   => $text,
					'isTrue' => '1',
				);
				return array( 'type' => 'fill_the_gap', 'answers' => $stm_answers );

			case 'ddimageortext':
			case 'ddmarker':
				// I9: These drag-drop types don't map well, skip with empty answers
				return array( 'type' => 'single_choice', 'answers' => array() );

			default:
				return array( 'type' => null, 'answers' => array() );
		}
	}

	private function link_quiz_to_course( $moodle_quiz_id, $wp_quiz_id, $wp_course_id ) {
		global $wpdb;

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

		// I7 FIX: Build a lookup map instead of using array index
		$sections = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, `order` FROM {$wpdb->prefix}stm_lms_curriculum_sections WHERE course_id = %d ORDER BY `order` ASC",
			$wp_course_id
		) );

		$section_table = $this->moodle_table( 'course_sections' );
		$moodle_section_num = (int) $this->moodle_db->get_var(
			$this->moodle_db->prepare(
				"SELECT section FROM {$section_table} WHERE id = %d",
				$cm->section
			)
		);

		// Build order-to-id map
		$section_map = array();
		foreach ( $sections as $idx => $s ) {
			$section_map[ $idx ] = $s->id;
		}

		$target_section = null;
		if ( isset( $section_map[ $moodle_section_num ] ) ) {
			$target_section = $section_map[ $moodle_section_num ];
		} elseif ( ! empty( $section_map ) ) {
			$target_section = end( $section_map );
		}

		if ( $target_section ) {
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
