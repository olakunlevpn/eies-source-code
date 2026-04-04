<?php
/**
 * @var object $current_user
 */

use MasterStudy\Lms\Plugin\Addons;

wp_enqueue_style( 'masterstudy-analytics-short-report' );
wp_enqueue_style( 'masterstudy-analytics-components' );
wp_enqueue_script( 'masterstudy-analytics-short-report' );

$stats_types = array(
	'revenue',
	'orders',
	'courses',
	'enrollments',
	'students',
);

if ( STM_LMS_Options::get_option( 'course_tab_reviews', true ) ) {
	array_splice( $stats_types, 5, 0, 'reviews' );
}

if ( is_ms_lms_addon_enabled( 'certificate_builder' ) ) {
	array_splice( $stats_types, 6, 0, 'certificates_created' );
}

if ( is_ms_lms_addon_enabled( Addons::COURSE_BUNDLE ) ) {
	array_splice( $stats_types, 7, 0, 'bundles' );
}
?>

<div class="masterstudy-analytics-short-report-page">
	<h3 class="masterstudy-analytics-short-report-page__title">
		<?php echo esc_html__( 'Analytics', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</h3>
	<div class="masterstudy-analytics-short-report-page__select">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/select',
			array(
				'select_id'    => 'period',
				'select_width' => '50px',
				'select_name'  => 'period',
				'placeholder'  => esc_html__( 'All time', 'masterstudy-lms-learning-management-system-pro' ),
				'is_queryable' => false,
				'options'      => array(
					'this_year'  => esc_html__( 'Year', 'masterstudy-lms-learning-management-system-pro' ),
					'this_month' => esc_html__( 'Month', 'masterstudy-lms-learning-management-system-pro' ),
					'this_week'  => esc_html__( 'Week', 'masterstudy-lms-learning-management-system-pro' ),
					'today'      => esc_html__( 'Day', 'masterstudy-lms-learning-management-system-pro' ),
				),
			)
		);
		?>
	</div>
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/button',
		array(
			'title' => esc_html__( 'Detailed reports', 'masterstudy-lms-learning-management-system-pro' ),
			'link'  => STM_LMS_User::login_page_url() . 'analytics',
			'style' => 'secondary',
			'size'  => 'sm',
			'id'    => 'user-detailed-report',
		)
	);
	?>
</div>
<?php
STM_LMS_Templates::show_lms_template(
	'analytics/partials/stats-section',
	array(
		'page_slug'   => 'short-report',
		'stats_types' => $stats_types,
	)
);
?>
