<?php
/**
 * @var string $page_slug
 * @var string $default_title
 * @var string $previous_page
 * @var boolean $is_user_account
 * @var int $user_id
 */

$user_info  = null;
$registered = '';

if ( ! empty( $user_id ) ) {
	$user_info  = get_userdata( $user_id );
	$registered = $user_info->user_registered;
}

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
?>

<div class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__header">
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/back-link',
		array(
			'id'  => $page_slug,
			'url' => ! empty( $previous_page ) ? $previous_page : masterstudy_get_current_url( array( 'user_id', 'role' ) ),
		)
	);
	?>
	<h1 class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__title">
		<?php
		$default_title_class = "masterstudy-analytics-{$page_slug}-page__title-role_self";

		if ( ! empty( $user_info ) && ! empty( $user_info->display_name ) ) {
			$default_title_class = '';
			?>
			<span class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__name">
				<?php echo esc_html( $user_info->display_name ); ?>
			</span>
			<?php
		}
		?>
		<span class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__role <?php echo esc_attr( $default_title_class ); ?>">
			<?php echo wp_kses_post( $default_title ); ?>
		</span>
	</h1>
	<?php if ( 'student' === $page_slug && ! empty( $user_info ) ) : ?>
		<div class="masterstudy-analytics-student-page__info">
			<div class="masterstudy-analytics-student-page__email">
				<div class="masterstudy-analytics-student-page__email--title">
					<?php echo esc_html__( 'Email:', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</div>
				<div class="masterstudy-analytics-student-page__email--value">
					<?php echo esc_html( stm_lms_minimize_word( $user_info->user_email, 45 ) ); ?>
				</div>
			</div>
			<div class="masterstudy-analytics-student-page__joined">
				<div class="masterstudy-analytics-student-page__joined--title">
					<?php echo esc_html__( 'Joined:', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</div>
				<div class="masterstudy-analytics-student-page__joined--value">
					<?php echo esc_html( date_i18n( $date_format . ' - ' . $time_format, strtotime( $registered ) ) ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php STM_LMS_Templates::show_lms_template( 'components/analytics/date-field' ); ?>
</div>
<?php if ( $is_user_account ) { ?>
	<div class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__separator">
		<span class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__separator-short"></span>
		<span class="masterstudy-analytics-<?php echo esc_html( $page_slug ); ?>-page__separator-long"></span>
	</div>
	<?php
}
STM_LMS_Templates::show_lms_template(
	'components/analytics/datepicker-modal',
	array(
		'id'    => $page_slug,
		'items' => array(
			'all_time' => esc_html__( 'All time', 'masterstudy-lms-learning-management-system' ),
		),
	)
);
