<?php
function stm_set_lms_options( $layout ) {
	global $wp_filesystem;

	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	/*Import LMS Options*/
	$lms_options = STM_POST_TYPE_PATH . '/importer/demos/' . $layout . '/lms_options/options.json';

	if ( file_exists( $lms_options ) ) {
		$encode_options = $wp_filesystem->get_contents( $lms_options );
		$import_options = json_decode( $encode_options, true );

		$courses_page = array(
			'Courses',
		);

		foreach ( $courses_page as $courses_page_name ) {
			$course_page = get_posts(
				array(
					'title'          => $courses_page_name,
					'post_type'      => 'page',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			if ( ! empty( $course_page ) ) {
				$import_options['courses_page'] = $course_page[0];
			}
		}

		$courses_page = array(
			'Instructors',
		);

		foreach ( $courses_page as $courses_page_name ) {
			$course_page = get_posts(
				array(
					'title'          => $courses_page_name,
					'post_type'      => 'page',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			if ( ! empty( $course_page ) ) {
				$import_options['instructors_page'] = $course_page[0];
			}
		}

		update_option( 'stm_lms_settings', $import_options );
	}

	/*Update Header 3 categories*/
	$courses_categories = array( 'art-photography', 'business-marketing', 'health-fitness', 'multimedia' );

	$courses_terms = array();
	foreach ( $courses_categories as $courses_category ) {
		$term = get_term_by( 'slug', $courses_category, 'stm_lms_course_taxonomy' );
		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			$courses_terms[] = $term->term_id;
		}
	}

	$options                             = get_option( 'stm_option', array() );
	$options['header_course_categories'] = $courses_terms;

	if ( 'udemy' === $layout ) {
		/*Update Header 2 categories*/
		$courses_categories = array( 'business', 'design', 'development', 'health-fitness' );
		$courses_terms      = array();
		foreach ( $courses_categories as $courses_category ) {
			$term = get_term_by( 'slug', $courses_category, 'stm_lms_course_taxonomy' );
			if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
				$courses_terms[] = $term->term_id;
			}
		}

		$options['header_course_categories_online'] = $courses_terms;
	}

	update_option( 'stm_option', $options );

	/*UPDATE COURSES META*/
	$post_meta  = json_decode( $wp_filesystem->get_contents( STM_POST_TYPE_PATH . '/importer/demos/' . $layout . '/lms_options/posts.json' ), true );
	$serialized = array( '_vc_post_settings', 'course_marks' );

	$args = array(
		'post_type'      => 'stm-courses',
		'posts_per_page' => -1,
	);

	$q = new WP_Query( $args );
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$post = get_the_ID();

			foreach ( $post_meta as $meta_key => $meta_value ) {
				if ( '_thumbnail_id' === $meta_key ) {
					continue;
				}
				if ( ! empty( $meta_value[0] ) ) {
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
					$value = ( in_array( $meta_key, $serialized, true ) ) ? unserialize( $meta_value[0] ) : $meta_value[0];

					if ( 'price' === $meta_key ) {
						if ( wp_rand( 0, 1 ) ) {
							update_post_meta( $post, $meta_key, wp_rand( 60, 80 ) );
						}
					} elseif ( 'sale_price' === $meta_key ) {
						if ( wp_rand( 0, 1 ) ) {
							update_post_meta( $post, $meta_key, wp_rand( 20, 50 ) );
						}
					} else {
						update_post_meta( $post, $meta_key, $value );
					}
				}
			}
		}
	}

	wp_reset_postdata();

	/* Import Curriculum */
	if ( function_exists( 'stm_lms_update_curriculum' ) ) {
		stm_lms_update_curriculum();
	}

	/*Get Questions*/

	$questions = array();
	$args      = array(
		'post_type'      => 'stm-questions',
		'posts_per_page' => -1,
	);

	$answers = array(
		array(
			'text'   => 'Answer 1 (Correct)',
			'isTrue' => '1',
		),
		array(
			'text'   => 'Answer 2',
			'isTrue' => '0',
		),
		array(
			'text'   => 'Answer 3',
			'isTrue' => '0',
		),
	);

	$q = new WP_Query( $args );
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$id          = get_the_ID();
			$questions[] = $id;

			update_post_meta( $id, 'answers', $answers );
		}
	}

	wp_reset_postdata();

	/*Quizzes*/
	$args = array(
		'post_type'      => 'stm-quizzes',
		'posts_per_page' => -1,
	);

	$q = new WP_Query( $args );
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$id = get_the_ID();

			update_post_meta( $id, 'questions', implode( ',', $questions ) );
		}
	}

	wp_reset_postdata();

	/*Set PMPRO PAGES*/
	$pmpro_pages = array(
		'account_page_id'      => 'Membership Account',
		'billing_page_id'      => 'Membership Billing',
		'cancel_page_id'       => 'Membership Cancel',
		'checkout_page_id'     => 'Membership Checkout',
		'confirmation_page_id' => 'Membership Confirmation',
		'invoice_page_id'      => 'Membership Invoice',
		'levels_page_id'       => 'Membership Plans',
	);

	foreach ( $pmpro_pages as $option_id => $pmpro_page ) {
		$pmpro_page = get_posts(
			array(
				'title'          => $pmpro_page,
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $pmpro_page ) ) {
			update_option( 'pmpro_' . $option_id, $pmpro_page[0] );
		}
	}
}
