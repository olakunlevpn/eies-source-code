<?php

namespace MasterStudy\Lms\Repositories;

use MasterStudy\Lms\Models\Course;
use MasterStudy\Lms\Plugin\PostType;
use MasterStudy\Lms\Plugin\Taxonomy;
use MasterStudy\Lms\Utility\Sanitizer;

final class CourseRepository extends AbstractRepository {
	/**
	 * course_property => meta_key
	 */
	public const FIELDS_META_MAPPING = array(
		'certificate_id'    => 'course_certificate',
		'current_students'  => 'current_students',
		'duration_info'     => 'duration_info',
		'basic_info'        => 'basic_info',
		'intended_audience' => 'intended_audience',
		'requirements'      => 'requirements',
		'end_time'          => 'end_time',
		'expiration'        => 'expiration_course',
		'level'             => 'level',
		'status'            => 'status',
		'status_date_end'   => 'status_dates_end',
		'status_date_start' => 'status_dates_start',
		'video_duration'    => 'video_duration',
		'views'             => 'views',
		'access_duration'   => 'access_duration',
		'access_devices'    => 'access_devices',
		'certificate_info'  => 'certificate_info',
		'is_featured'       => 'featured',
		'is_lock_lesson'    => 'lock_lesson',
	);

	protected static array $casts = array(
		'is_featured'    => 'bool',
		'is_lock_lesson' => 'bool',
	);

	/**
	 * Sorting mapping for Get Courses
	 */
	public const SORT_MAPPING = array(
		'date_low'   => array(
			'orderby' => 'date',
			'order'   => 'ASC',
		),
		'price_high' => array(
			'meta_query' => array(
				'price' => array(
					'relation' => 'OR',
					array(
						'key'     => 'price',
						'value'   => array( '', 0 ),
						'compare' => '>',
					),
				),
			),
			'meta_key'   => 'price',
			'orderby'    => 'meta_value_num',
			'order'      => 'DESC',
		),
		'price_low'  => array(
			'meta_query' => array(
				'price' => array(
					'relation' => 'OR',
					array(
						'key'     => 'price',
						'value'   => array( '', 0 ),
						'compare' => '>',
					),
				),
			),
			'meta_key'   => 'price',
			'orderby'    => 'meta_value_num',
			'order'      => 'ASC',
		),
		'rating'     => array(
			'meta_key' => 'course_mark_average',
			'orderby'  => 'meta_value_num',
			'order'    => 'DESC',
		),
		'popular'    => array(
			'meta_key' => 'views',
			'orderby'  => 'meta_value_num',
			'order'    => 'DESC',
		),
	);

	/**
	 * Filter mapping for Get Courses
	 */
	public const FILTER_MAPPING = array(
		'availability' => array(
			'coming_soon'   => array(
				'key'     => 'coming_soon_status',
				'value'   => '1',
				'compare' => '=',
			),
			'available_now' => array(
				'relation' => 'OR',
				array(
					'key'     => 'coming_soon_status',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'coming_soon_status',
					'value'   => '',
					'compare' => '=',
				),
			),
		),
		'price'        => array(
			'free_courses' => array(
				'relation' => 'AND',
				array(
					'key'     => 'price',
					'value'   => array( 0, '' ),
					'compare' => 'in',
				),
				array(
					'key'     => 'single_sale',
					'value'   => 'on',
					'compare' => '=',
				),
			),
			'paid_courses' => array(
				'key'     => 'price',
				'value'   => 0,
				'compare' => '>',
			),
			'subscription' => array(
				'relation' => 'AND',
				array(
					'key'     => 'single_sale',
					'value'   => 'on',
					'compare' => '!=',
				),
				array(
					'key'     => 'not_membership',
					'value'   => 'on',
					'compare' => '!=',
				),
			),
		),
	);

	/**
	 * Meta Query mapping for Get Courses
	 */
	public const META_QUERY_MAPPING = array(
		'status' => array(
			'key'     => 'status',
			'compare' => 'IN',
		),
		'level'  => array(
			'key'     => 'level',
			'compare' => 'IN',
		),
		'rating' => array(
			'key'     => 'course_mark_average',
			'compare' => '>=',
		),
	);

	public function exists( $id ): bool {
		return $this->find_post( $id ) !== null;
	}

	public function find( $id, $type = 'default' ): ?Course {
		$post = $this->find_post( $id );

		if ( null === $post ) {
			return null;
		}

		if ( 'grid' === $type ) {
			return $this->hydrate_grid( $post );
		}

		return $this->hydrate( $post );
	}

	/**
	 * @param $id
	 *
	 * @return \WP_Post|null
	 */
	public function find_post( int $id ): ?\WP_Post {
		$post = get_post( $id );

		if ( $post && PostType::COURSE === $post->post_type ) {
			return $post;
		}

		return null;
	}

	/**
	 * Get all Courses
	 *
	 * @param array $request Request data {
	 *    An array of arguments.
	 *    @type int $per_page Posts per page.
	 *    @type int $page Current page. Default is 1.
	 *    @type int $author Author ID.
	 *    @type string $s Search query string.
	 *    @type string $sort Sorting key. Acceptable values: date_low, price_high, price_low, rating, popular.
	 *    @type string $category Category IDs. Comma-separated.
	 * }
	 *
	 * @return array {
	 *    @type array $courses List of courses.
	 *    @type int $total Total number of courses.
	 *    @type int $pages Total number of pages.
	 * }
	 */
	public function get_all( array $request = array() ): array {
		$args = array(
			'post_type'      => ! empty( $request['post_type'] ) && in_array( $request['post_type'], array( PostType::COURSE, PostType::COURSE_BUNDLES ), true )
				? $request['post_type']
				: PostType::COURSE,
			'posts_per_page' => ! empty( $request['per_page'] )
				? intval( $request['per_page'] )
				: \STM_LMS_Options::get_option( 'courses_per_page', get_option( 'posts_per_page' ) ),
			'post_status'    => 'publish',
			'meta_query'     => array(),
		);

		$args['offset'] = $args['posts_per_page'] * ( intval( $request['page'] ?? 1 ) - 1 );

		if ( ! empty( $request['s'] ) ) {
			$args['s'] = sanitize_text_field( $request['s'] );
		}

		if ( ! empty( $request['current_user'] ) ) {
			$args['author'] = get_current_user_id();
		}

		if ( ! empty( $request['author'] ) ) {
			$args['author'] = intval( $request['author'] );
		}

		if ( ! empty( $request['category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => Taxonomy::COURSE_CATEGORY,
					'field'    => 'term_id',
					'terms'    => array_map( 'intval', explode( ',', $request['category'] ) ),
				),
			);
		}

		foreach ( self::META_QUERY_MAPPING as $key => $meta_query ) {
			if ( ! empty( $request[ $key ] ) ) {
				$args['meta_query'][] = array_merge(
					$meta_query,
					array(
						'value' => $request[ $key ],
					)
				);
			}
		}

		foreach ( self::FILTER_MAPPING as $key => $meta_query ) {
			if ( ! empty( $request[ $key ] ) && ! empty( $meta_query[ $request[ $key ] ] ) ) {
				$args['meta_query'][] = $meta_query[ $request[ $key ] ];
			}
		}

		if ( ! empty( $request['paid_only'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'price',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'NUMERIC',
			);
		}

		if ( ! empty( $request['sort'] ) && ! empty( self::SORT_MAPPING[ $request['sort'] ] ) ) {
			$sort_args = self::SORT_MAPPING[ $request['sort'] ];

			if ( ! empty( $sort_args['meta_query'] ) ) {
				$args['meta_query'][] = $sort_args['meta_query'];

				unset( $sort_args['meta_query'] );
			}

			$args = array_merge( $args, $sort_args );
		}

		$query = new \WP_Query( $args );

		return array(
			'courses'      => $this->hydrate_courses( $query->posts ),
			'courses_page' => \STM_LMS_Course::courses_page_url(),
			'total'        => $query->found_posts,
			'pages'        => $query->max_num_pages,
		);
	}

	public function create( array $data ): int {
		$post = array(
			'post_name'  => $data['slug'],
			'post_title' => $data['title'],
			'post_type'  => PostType::COURSE,
		);

		$post_id = wp_insert_post( $post );

		if ( ! empty( $data['category'] ) ) {
			wp_set_post_terms( $post_id, $data['category'], Taxonomy::COURSE_CATEGORY );
		}

		if ( ! empty( $data['level'] ) ) {
			update_post_meta( $post_id, 'level', $data['level'] );
		}

		if ( ! empty( $data['image_id'] ) ) {
			set_post_thumbnail( $post_id, $data['image_id'] );
		}

		update_post_meta( $post_id, 'featured', '' );
		update_post_meta( $post_id, 'coming_soon_status', '' );

		if ( \STM_LMS_Subscriptions::subscription_enabled() && \STM_LMS_Course::course_in_plan( $post_id ) ) {
			update_post_meta( $post_id, 'single_sale', '' );
		}

		do_action( 'masterstudy_lms_course_saved', $post_id, $data );

		return $post_id;
	}

	public function save( Course $course ): void {
		$post = array(
			'ID'           => $course->id,
			'post_content' => apply_filters( 'masterstudy_lms_map_api_data', $course->content, 'post_content' ),
			'post_excerpt' => $course->excerpt,
			'post_name'    => $course->slug,
			'post_title'   => $course->title,
			'post_status'  => $this->moderate_post_status( $course->access_status ),
		);

		wp_update_post( $post );

		wp_set_post_terms( $post['ID'], $course->category, Taxonomy::COURSE_CATEGORY );

		foreach ( self::FIELDS_META_MAPPING as $field => $meta_key ) {
			if ( property_exists( $course, $field ) ) {
				update_post_meta( $post['ID'], $meta_key, $this->convert_to_meta( $field, $course->$field ) );
			}
		}

		if ( null === $course->co_instructor ) {
			delete_post_meta( $post['ID'], 'co_instructor' );
		} else {
			update_post_meta( $post['ID'], 'co_instructor', $course->co_instructor->ID );
		}

		if ( null === $course->image ) {
			delete_post_thumbnail( $post['ID'] );
		} else {
			set_post_thumbnail( $post['ID'], $course->image['id'] );
		}

		if ( ! isset( $course->video_poster ) ) {
			delete_post_meta( $post['ID'], 'video_poster' );
		} else {
			update_post_meta( $post['ID'], 'video_poster', $course->video_poster );
		}

		do_action( 'masterstudy_lms_course_saved', $post['ID'], (array) $course );
	}

	public function update_certificate( int $course_id, $certificate_id ): void {
		update_post_meta( $course_id, self::FIELDS_META_MAPPING['certificate_id'], $certificate_id );
	}

	public function update_course_page_style( int $course_id, $slug ): void {
		update_post_meta( $course_id, 'page_style', $slug );
	}

	public function update_status( int $course_id, array $data ): void {
		wp_update_post(
			array(
				'ID'          => $course_id,
				'post_status' => $this->moderate_post_status( $data['status'] ),
			)
		);
	}

	public function update_access( int $course_id, array $data ): void {
		foreach ( $data as $key => $value ) {
			if ( isset( self::FIELDS_META_MAPPING[ $key ] ) ) {
				update_post_meta( $course_id, self::FIELDS_META_MAPPING[ $key ], $value );
			}
		}

		do_action( 'masterstudy_lms_course_update_access', $course_id, $data );
	}

	public function moderate_post_status( string $post_status ): ?string {
		if ( 'publish' === $post_status
			&& ! current_user_can( 'administrator' )
			&& \STM_LMS_Options::get_option( 'course_premoderation', false ) ) {
			return 'pending';
		}

		return $post_status;
	}

	public function get_announcement( int $course_id ): ?string {
		return get_post_meta( $course_id, 'announcement', true );
	}

	public function update_announcement( int $course_id, string $announcement ): void {
		update_post_meta(
			$course_id,
			'announcement',
			Sanitizer::html(
				$announcement,
				array(
					'img' => array(
						'src'    => array(),
						'width'  => array(),
						'height' => array(),
						'title'  => array(),
						'alt'    => array(),
					),
				)
			)
		);
	}

	public function instructor_courses( array $args ) {
		$courses  = \STM_LMS_Courses::get_all_courses( $args );
		$reviews  = \STM_LMS_Options::get_option( 'course_tab_reviews', true );
		$response = array();

		if ( ! empty( $courses ) ) {
			foreach ( $courses['posts'] as $course ) {
				$response['courses'][] = \STM_LMS_Templates::load_lms_template(
					'components/course/card/default',
					array(
						'course'  => $course,
						'public'  => true,
						'reviews' => $reviews,
					)
				);
			}

			$response['pagination']  = \STM_LMS_Templates::load_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $courses['total_pages'],
					'current_page'      => $args['page'],
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
				)
			);
			$response['total_pages'] = $courses['total_pages'];
			$response['total_posts'] = $courses['total_posts'];
		}

		return $response;
	}

	public function student_courses( array $params ) {
		$courses  = \STM_LMS_Courses::get_student_courses( $params );
		$response = array();

		if ( ! empty( $courses ) ) {
			foreach ( $courses['posts'] as $course ) {
				$response['courses'][] = \STM_LMS_Templates::load_lms_template(
					'components/course/student-card',
					array(
						'course'  => $course,
						'user_id' => $params['user'],
					)
				);
			}

			$response['pagination'] = \STM_LMS_Templates::load_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $courses['total_pages'],
					'current_page'      => $params['page'],
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
				)
			);

			$response['total_pages'] = $courses['total_pages'];
			$response['total_posts'] = $courses['total_posts'];
		}

		return $response;
	}

	private function get_course_image( \WP_Post $post, $size = 'full' ): ?array {
		$attachment_id = get_post_thumbnail_id( $post );

		if ( ! $attachment_id ) {
			return null;
		}

		$attachment = get_post( $attachment_id );
		$image_src  = wp_get_attachment_image_src( $attachment_id, $size );

		if ( $attachment && $image_src ) {
			return array(
				'id'    => $attachment->ID,
				'title' => $attachment->post_title,
				'type'  => get_post_mime_type( $attachment->ID ),
				'url'   => $image_src[0],
			);
		}

		return null;
	}

	private function get_course_marks( int $course_id ): ?array {
		$marks = get_post_meta( $course_id, 'course_marks', true );

		return ! empty( $marks ) ? $marks : array();
	}

	private function get_course_udemy_languages( int $course_id ): ?array {
		$languages = get_post_meta( $course_id, 'udemy_caption_languages', true );

		return ! empty( $languages ) ? $languages : array();
	}

	private function get_course_udemy_objectives( int $course_id ): ?array {
		$objectives = get_post_meta( $course_id, 'udemy_objectives', true );

		return ! empty( $objectives ) ? $objectives : array();
	}

	private function get_course_udemy_rating_distribution( int $course_id ): ?array {
		$rating_distribution = get_post_meta( $course_id, 'udemy_rating_distribution', true );

		return ! empty( $rating_distribution ) ? $rating_distribution : array();
	}

	private function get_course_udemy_instructor( int $course_id ): ?array {
		$instructors = get_post_meta( $course_id, 'udemy_visible_instructors', true );

		return ! empty( $instructors ) ? $instructors[0] : array();
	}

	private function get_course_rate( array $marks ): ?array {
		$rate = array(
			'average' => 0,
			'percent' => 0,
		);

		if ( empty( $marks ) ) {
			return $rate;
		}

		$rate['average'] = round( array_sum( $marks ) / count( $marks ), 1 );
		$rate['percent'] = $rate['average'] * 100 / 5;

		return $rate;
	}

	private function find_user( $id ): ?\WP_User {
		// phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
		return get_user_by( 'id', $id ) ?: null;
	}

	/**
	 * Get post author info
	 */
	private function get_author_info( $author_id ) {
		$author = $this->find_user( $author_id );

		if ( $author ) {
			return (object) array(
				'id'     => $author->ID,
				'name'   => $author->display_name,
				'avatar' => get_avatar_url( $author->ID ),
			);
		}

		return null;
	}

	/**
	 * Get wishlist status
	 */
	private function get_user_wishlist() {
		if ( is_user_logged_in() ) {
			$user_wishlist = get_user_meta( get_current_user_id(), 'stm_lms_wishlist', true );

			return is_array( $user_wishlist ) ? $user_wishlist : array();
		} else {
			return null;
		}
	}

	private function hydrate_courses( array $posts ): array {
		$user_wishlist          = $this->get_user_wishlist();
		$is_coming_soon_enabled = is_ms_lms_addon_enabled( 'coming_soon' ) && function_exists( 'masterstudy_lms_coming_soon_start_time' );
		$subscription_enabled   = \STM_LMS_Subscriptions::subscription_enabled();
		$courses_statuses       = \STM_LMS_Helpers::get_course_statuses();
		$is_featured_enabled    = \STM_LMS_Options::get_option( 'enable_featured_courses', false );

		foreach ( $posts as &$post ) {
			$meta           = get_post_meta( $post->ID );
			$section_ids    = ( new CurriculumSectionRepository() )->get_course_section_ids( $post->ID );
			$is_in_wishlist = is_null( $user_wishlist ) ? 'not-authorized' : in_array( $post->ID, $user_wishlist, true );
			$status_value   = $meta['status'][0] ?? null;
			$status_data    = null;
			$coming_soon_start_time = $is_coming_soon_enabled ? intval( masterstudy_lms_coming_soon_start_time( $post->ID ) ) : false;
			$coming_soon_date_formatted = ! empty( $coming_soon_start_time )
				? \STM_LMS_Helpers::format_date( '@' . $coming_soon_start_time )
				: array();

			if ( ! empty( $status_value ) ) {
				$status_date_start = $meta['status_dates_start'][0] ?? '';
				$status_date_end   = $meta['status_dates_end'][0] ?? '';

				if ( empty( $status_date_start ) && empty( $status_date_end ) ) {
					$status_data = $courses_statuses[ $status_value ] ?? null;
				} else {
					$current_time = time() * 1000;

					if ( $current_time > intval( $status_date_start ) && $current_time < intval( $status_date_end ) ) {
						$status_data = $courses_statuses[ $status_value ] ?? null;
					} else {
						$status_value = null;
					}
				}
			}

			$extra_fields = array(
				'pricing_mode'           => $meta['pricing_mode'][0] ?? '',
				'affiliate_course_price' => $meta['affiliate_course_price'][0] ?? '',
				'price'                  => $meta['price'][0] ?? '',
				'sale_price'             => \STM_LMS_Course::get_sale_price( $post->ID ),
				'single_sale'            => $meta['single_sale'][0] ?? '',
				'symbol'                 => \STM_LMS_Options::get_option( 'currency_symbol', '$' ),
				'rating_visibility'      => \STM_LMS_Options::get_option( 'course_tab_reviews', true ),
				'rating'                 => $meta['course_mark_average'][0] ?? 0,
				'categories'             => wp_get_post_terms( $post->ID, Taxonomy::COURSE_CATEGORY ),
				'image'                  => $this->get_course_image( $post ),
				'lazy_load'              => \STM_LMS_Options::get_option( 'enable_lazyload', false ) ?? '',
				'duration_info'          => $meta['duration_info'][0] ?? '',
				'members'                => $meta['current_students'][0] ?? '',
				'end_time'               => intval( $meta['end_time'][0] ?? 0 ),
				'featured'               => ( $meta['featured'][0] ?? null ) === 'on' && $is_featured_enabled,
				'lock_lesson'            => ( $meta['lock_lesson'][0] ?? null ) === 'on',
				'level'                  => $meta['level'][0] ?? null,
				'status'                 => $status_value,
				'status_data'            => $status_data,
				'views'                  => $meta['views'][0] ?? 0,
				'access_duration'        => $meta['access_duration'][0] ?? '',
				'access_devices'         => $meta['access_devices'][0] ?? '',
				'author'                 => $this->get_author_info( $post->post_author ),
				'lessons'                => ( new CurriculumMaterialRepository() )->count_by_type( $section_ids, PostType::LESSON ),
				'permalink'              => get_permalink( $post->ID ),
				'user_wishlist'          => $is_in_wishlist,
				'user_url'               => \STM_LMS_User::user_page_url(),
				'user_avatar'            => get_user_meta( get_current_user_id(), 'stm_lms_user_avatar', true ),
				'coming_soon_status'     => $meta['coming_soon_status'][0] ?? '',
				'coming_soon_start_time' => $coming_soon_start_time,
				'coming_soon_date_formatted' => $coming_soon_date_formatted,
				'membership'             => $subscription_enabled && ! $meta['not_membership'][0] && ! $meta['single_sale'][0],
				'trial'                  => $meta['shareware'][0] ?? null,
			);

			$post = (object) array_merge( (array) $post, $extra_fields );
		}

		return $posts;
	}

	private function hydrate( \WP_Post $post ): Course {
		$meta = get_post_meta( $post->ID );

		$course                          = new Course();
		$course->access_status           = $post->post_status;
		$course->owner                   = $this->find_user( $post->post_author );
		$course->category                = wp_get_post_terms( $post->ID, Taxonomy::COURSE_CATEGORY, array( 'fields' => 'ids' ) );
		$course->certificate_id          = ( $meta['course_certificate'][0] ?? null ) === 'none' ? $meta['course_certificate'][0] : intval( $meta['course_certificate'][0] ?? null );
		$course->course_page_style       = $meta['page_style'][0] ?? null;
		$course->co_instructor           = isset( $meta['co_instructor'][0] )
			? $this->find_user( $meta['co_instructor'][0] )
			: null;
		$course->current_students        = intval( $meta['current_students'][0] ?? 0 );
		$course->content                 = $post->post_content;
		$course->duration_info           = $meta['duration_info'][0] ?? '';
		$course->end_time                = intval( $meta['end_time'][0] ?? 0 );
		$course->excerpt                 = $post->post_excerpt;
		$course->expiration              = (bool) ( $meta['expiration_course'][0] ?? false );
		$course->coming_soon_date        = $meta['coming_soon_date'][0] ?? '';
		$course->files                   = ( new FileMaterialRepository() )->get_files( $meta['course_files'][0] ?? null, true );
		$course->id                      = $post->ID;
		$course->image                   = $this->get_course_image( $post );
		$course->is_featured             = ( $meta['featured'][0] ?? null ) === 'on';
		$course->is_lock_lesson          = ( $meta['lock_lesson'][0] ?? null ) === 'on';
		$course->level                   = $meta['level'][0] ?? null;
		$course->owner                   = $this->find_user( $post->post_author );
		$course->slug                    = $post->post_name;
		$course->status                  = $meta['status'][0] ?? null;
		$course->status_date_end         = isset( $meta['status_dates_end'][0] ) ? (int) $meta['status_dates_end'][0] : null;
		$course->status_date_start       = isset( $meta['status_dates_start'][0] ) ? (int) $meta['status_dates_start'][0] : null;
		$course->title                   = $post->post_title;
		$course->video_duration          = $meta['video_duration'][0] ?? '';
		$course->requirements            = $meta['requirements'][0] ?? '';
		$course->basic_info              = $meta['basic_info'][0] ?? '';
		$course->intended_audience       = $meta['intended_audience'][0] ?? '';
		$course->views                   = $meta['views'][0] ?? 0;
		$course->access_duration         = $meta['access_duration'][0] ?? '';
		$course->access_devices          = $meta['access_devices'][0] ?? '';
		$course->certificate_info        = $meta['certificate_info'][0] ?? '';
		$course->coming_soon_details     = (bool) ( $meta['coming_soon_show_course_details'][0] ?? false );
		$course->coming_soon_price       = (bool) ( $meta['coming_soon_show_course_price'][0] ?? false );
		$course->coming_soon_preorder    = (bool) ( $meta['coming_soon_preordering'][0] ?? false );
		$course->announcement            = $meta['announcement'][0] ?? '';
		$course->reviews                 = $meta['reviews'][0] ?? array();
		$course->marks                   = $this->get_course_marks( $post->ID );
		$course->rate                    = $this->get_course_rate( $course->marks );
		$course->thumbnail               = $this->get_course_image( $post, 'img-870-440' );
		$course->full_image              = $this->get_course_image( $post, 'full' );
		$course->attachments             = ( new FileMaterialRepository() )->get_files( $meta['course_files'][0] ?? null );
		$course->is_udemy_course         = $meta['udemy_course_id'][0] ?? false;
		$course->price_info              = $meta['price_info'][0] ?? '';
		$course->single_sale_price_info  = $meta['single_sale_price_info'][0] ?? '';
		$course->free_price_info         = $meta['free_price_info'][0] ?? '';
		$course->enterprise_price_info   = $meta['enterprise_price_info'][0] ?? '';
		$course->points_price_info       = $meta['points_price_info'][0] ?? '';
		$course->subscription_price_info = $meta['subscription_price_info'][0] ?? '';
		$course->pricing_mode            = $meta['pricing_mode'][0] ?? '';
		$course->url                     = get_post_permalink( $post->ID );

		if ( $course->is_udemy_course ) {
			$course->udemy_video               = $meta['udemy_content_length_video'][0] ?? '';
			$course->udemy_articles            = $meta['udemy_num_article_assets'][0] ?? '';
			$course->udemy_certificate         = $meta['udemy_has_certificate'][0] ?? '';
			$course->udemy_rate                = floatval( $meta['udemy_avg_rating'][0] ?? 0 );
			$course->udemy_marks               = intval( $meta['udemy_num_reviews'][0] ?? 0 );
			$course->udemy_headline            = $meta['udemy_headline'][0] ?? '';
			$course->udemy_languages           = $this->get_course_udemy_languages( $post->ID );
			$course->udemy_instructor          = $this->get_course_udemy_instructor( $post->ID );
			$course->udemy_objectives          = $this->get_course_udemy_objectives( $post->ID );
			$course->udemy_rating_distribution = $this->get_course_udemy_rating_distribution( $post->ID );
		}

		return apply_filters( 'masterstudy_lms_course_hydrate', $course, $meta );
	}

	private function hydrate_grid( \WP_Post $post ): Course {
		$meta = get_post_meta( $post->ID );

		$course                    = new Course();
		$course->id                = $post->ID;
		$course->slug              = $post->post_name;
		$course->owner             = $this->find_user( $post->post_author );
		$course->title             = $post->post_title;
		$course->single_sale       = (bool) ( $meta['single_sale'][0] ?? true );
		$course->not_in_membership = (bool) ( $meta['not_membership'][0] ?? false );
		$course->price             = floatval( $meta['price'][0] ?? 0 );
		$course->sale_price        = floatval( $meta['sale_price'][0] ?? 0 );
		$course->is_sale_active    = \STM_LMS_Helpers::is_sale_price_active( $post->ID );
		$course->marks             = $this->get_course_marks( $post->ID );
		$course->rate              = $this->get_course_rate( $course->marks );
		$course->is_udemy_course   = $meta['udemy_course_id'][0] ?? false;
		$course->udemy_instructor  = $this->get_course_udemy_instructor( $post->ID );
		$course->udemy_rate        = floatval( $meta['udemy_avg_rating'][0] ?? 0 );
		$course->thumbnail         = $this->get_course_image( $post, 'img-300-225' );

		return apply_filters( 'masterstudy_lms_popular_course_hydrate', $course, $meta );
	}
}
