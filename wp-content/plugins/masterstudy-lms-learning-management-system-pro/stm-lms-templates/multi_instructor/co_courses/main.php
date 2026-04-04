<?php
use MasterStudy\Lms\Pro\addons\MultiInstructors\Repository\MultiInstructorsRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$repo     = new MultiInstructorsRepository();
$settings = get_option( 'stm_lms_settings', array() );
$url      = STM_LMS_Helpers::get_current_url();
$user_id  = '';

if ( 0 === strpos( $url, get_permalink( $settings['instructor_url_profile'] ?? '' ) ) ) {
	$url     = wp_parse_url( $url );
	$parts   = explode( '/', trim( $url['path'] ?? '', '/' ) );
	$user_id = $parts[1] ?? '';
}

$user_id = absint( $user_id );
if ( empty( $user_id ) ) {
	$user_id = get_current_user_id();
}

$args = $repo->getCoCourses( $user_id, true );

$initial_page     = 1;
$courses_per_page = (int) ( $args['posts_per_page'] ?? 6 );

$args['paged'] = $initial_page;
if ( isset( $args['offset'] ) ) {
	unset( $args['offset'] );
}

$courses = array(
	'posts' => array(),
	'pages' => 0,
	'found' => 0,
);

if ( class_exists( 'STM_LMS_Instructor' ) && method_exists( 'STM_LMS_Instructor', 'get_instructor_courses' ) ) {
	$courses = STM_LMS_Instructor::get_instructor_courses( $args, $courses_per_page );
}

$initial_total_pages = (int) $courses['pages'];
$total_posts         = (int) ( $courses['found'] ?? 0 );
$initial_posts       = (array) $courses['posts'];
$reviews             = STM_LMS_Options::get_option( 'course_tab_reviews', true );

if ( empty( $initial_posts ) ) {
	return;
}

wp_enqueue_script( 'masterstudy-account-instructor-co-courses' );

wp_localize_script(
	'masterstudy-account-instructor-co-courses',
	'masterstudy_instructor_co_courses',
	array(
		'nonce'    => wp_create_nonce( 'wp_rest' ),
		'user_id'  => $user_id,
		'per_page' => $courses_per_page,
		'rest_url' => esc_url_raw( rest_url( 'masterstudy-lms/v2' ) ),
		'strings'  => array(
			'error' => esc_html__( 'Something went wrong. Please try again.', 'masterstudy-lms-learning-management-system' ),
			'edit'  => esc_html__( 'Edit', 'masterstudy-lms-learning-management-system' ),
			'view'  => esc_html__( 'View', 'masterstudy-lms-learning-management-system' ),
		),
	)
);
?>

<div class="masterstudy-instructor-co-courses">
	<div class="masterstudy-instructor-courses__top">
		<span class="masterstudy-instructor-courses__title">
			<?php esc_html_e( 'Co-courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</span>
		<span class="masterstudy-instructor-courses__count">
			<span class="masterstudy-instructor-courses__count-label">
				<?php esc_html_e( 'All', 'masterstudy-lms-learning-management-system' ); ?>
			</span>
			<span class="masterstudy-instructor-courses__count-badge">
				<?php echo esc_html( $total_posts ); ?>
			</span>
		</span>
	</div>

	<div class="masterstudy-instructor-courses__list">
		<?php
		if ( ! empty( $initial_posts ) ) :
			foreach ( $initial_posts as $course ) :
				STM_LMS_Templates::show_lms_template(
					'components/course/card/default',
					array(
						'course'          => $course,
						'public'          => false,
						'reviews'         => (bool) $reviews,
						'student_card'    => false,
						'instructor_card' => true,
					)
				);
			endforeach;
		endif;
		?>
	</div>

	<div class="masterstudy-instructor-courses__pagination">
		<?php
		if ( $initial_total_pages > 1 ) {
			STM_LMS_Templates::show_lms_template(
				'components/pagination',
				array(
					'max_visible_pages' => 5,
					'total_pages'       => $initial_total_pages,
					'current_page'      => $initial_page,
					'dark_mode'         => false,
					'is_queryable'      => false,
					'done_indicator'    => false,
					'is_api'            => true,
					'thin'              => true,
				)
			);
		}
		?>
	</div>

	<div class="masterstudy-instructor-courses__loader">
		<div class="masterstudy-instructor-courses__loader-body"></div>
	</div>

</div>
