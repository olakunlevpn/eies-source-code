<?php
use MasterStudy\Lms\Pro\addons\CourseBundle\Repository\CourseBundleSettings;

wp_enqueue_style( 'masterstudy-bundle-course-card' );
wp_enqueue_style( 'masterstudy-bundle-select-courses' );
wp_enqueue_script( 'masterstudy-bundle-select-courses' );
wp_localize_script(
	'masterstudy-bundle-select-courses',
	'select_courses',
	array(
		'no_found' => esc_html__( 'No courses found', 'masterstudy-lms-learning-management-system-pro' ),
	)
);

$args = array(
	'posts_per_page' => 9,
	'post_status'    => array( 'publish' ),
);

$coming_soon_settings = get_option( 'masterstudy_lms_coming_soon_settings', array() );

if ( empty( $coming_soon_settings['lms_coming_soon_course_bundle_status'] ) ) {
	$args['meta_query'][] = array(
		'relation' => 'OR',
		array(
			'key'     => 'coming_soon_status',
			'value'   => '',
			'compare' => '=',
		),
		array(
			'key'     => 'coming_soon_status',
			'compare' => 'NOT EXISTS',
		),
	);
}

$courses = STM_LMS_Instructor::get_courses( $args, true );
$limit   = ( new CourseBundleSettings() )->get_bundle_courses_limit();
?>

<div class="masterstudy-bundle-select" style="display:none;">
	<div class="masterstudy-bundle-select__block">
		<div class="masterstudy-bundle-select__header">
			<span class="masterstudy-bundle-select__back"></span>
			<div class="masterstudy-bundle-select__title">
				<?php echo esc_html__( 'Select courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
				<span class="masterstudy-bundle-select__limit">
					<?php echo esc_html__( 'You can select up to', 'masterstudy-lms-learning-management-system-pro' ) . ' ' . intval( $limit ) . ' ' . esc_html__( 'courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
			</div>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => __( 'Add', 'masterstudy-lms-learning-management-system-pro' ),
					'link'  => '#',
					'style' => 'primary',
					'size'  => 'sm',
					'id'    => 'add_courses',
				)
			);
			?>
		</div>
		<div class="masterstudy-bundle-select__search">
			<div class="masterstudy-bundle-select__search-wrapper">
				<input name="bundle_courses_search" type="text" class="masterstudy-bundle-select__input" placeholder="<?php echo esc_attr__( 'Search', 'masterstudy-lms-learning-management-system' ); ?>" />
				<span class="masterstudy-bundle-select__search-button"></span>
			</div>
		</div>
		<div class="masterstudy-bundle-select__content">
			<div class="masterstudy-bundle-select__loader">
				<div class="masterstudy-bundle-select__loader-body"></div>
			</div>
			<div class="masterstudy-bundle-select__courses">
				<?php
				if ( ! empty( $courses['posts'] ) ) {
					foreach ( $courses['posts'] as $course ) {
						STM_LMS_Templates::show_lms_template(
							'components/bundle/course-card',
							array(
								'course'     => $course,
								'selectable' => true,
								'removable'  => false,
							)
						);
					}
				} else {
					?>
					<div class="masterstudy-bundle-select__courses-empty">
						<?php echo esc_html__( 'No courses', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</div>
					<?php
				}
				?>
			</div>
			<?php if ( ! $courses['total'] ) { ?>
				<div class="masterstudy-bundle-select__load">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => __( 'Load more', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
							'id'    => 'load_courses',
						)
					);
					?>
				</div>
			<?php } ?>
		</div>
		<div class="masterstudy-bundle-select__footer">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => __( 'Add', 'masterstudy-lms-learning-management-system-pro' ),
					'link'  => '#',
					'style' => 'primary',
					'size'  => 'sm',
					'id'    => 'add_courses',
				)
			);
			?>
		</div>
	</div>
</div>
