<?php
/**
 * @var $title
 */

$title    = ( ! empty( $title ) ) ? $title : esc_html__( 'Classrooms', 'masterstudy-lms-learning-management-system-pro' );
$per_page = ( ! empty( $number ) ) ? $number : 4;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$chosen_auditory = ! empty( $_GET['auditory_id'] ) ? intval( $_GET['auditory_id'] ) : '';
$auditories      = STM_LMS_Helpers::get_posts( 'stm-auditory' );

stm_lms_register_style( 'google_classroom/module' );
stm_lms_register_script( 'google_classroom_module', array( 'jquery.cookie' ) );
wp_localize_script(
	'stm-lms-google_classroom_module',
	'google_classroom_data',
	array(
		'chosen_auditory' => $chosen_auditory,
		'per_page'        => $per_page,
		'translations'    => array(
			'course_code'    => esc_html__( 'Course code', 'masterstudy-lms-learning-management-system-pro' ),
			'copy'           => esc_html__( 'Copy', 'masterstudy-lms-learning-management-system-pro' ),
			'copied'         => esc_html__( 'Copied', 'masterstudy-lms-learning-management-system-pro' ),
			'only_logged_in' => esc_html__( 'Only logged in students in a specific classroom can see the code ', 'masterstudy-lms-learning-management-system-pro' ),
			'read_more'      => esc_html__( 'Read more', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);
?>

<?php STM_LMS_Templates::show_lms_template( 'modals/preloader' ); ?>

<div id="stm_lms_google_classroom_grid">
	<div class="row stm_lms_google_classroom_grid__head">
		<div class="col-sm-8">
			<h3><?php echo esc_html( $title ); ?></h3>
		</div>

		<div class="col-sm-4">
			<div class="form-group">
				<select class="disable-select form-control stm_lms_google_classroom_grid__select-auditory">
					<option value=""><?php esc_html_e( 'Select auditory', 'masterstudy-lms-learning-management-system-pro' ); ?></option>
					<?php foreach ( $auditories as $auditory_value => $auditory_label ) : ?>
						<option value="<?php echo esc_attr( $auditory_value ); ?>">
							<?php echo esc_html( $auditory_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>

	<div class="stm_lms_g_courses_wrapper">
		<div class="loading loading-spinner"></div>

		<div class="stm_lms_g_courses"></div>
	</div>

	<div class="asignments_grid__pagination hidden">
		<ul class="page-numbers stm_lms_g_courses__pagination-nums">
			<li v-for="single_page in pages">
				<a class="page-numbers" href="#" v-if="single_page !== page" @click.prevent="page = single_page; getCourses()">
					{{single_page}}
				</a>
				<span v-else class="page-numbers current">{{single_page}}</span>
			</li>
		</ul>
	</div>
</div>
