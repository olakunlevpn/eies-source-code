<?php
	wp_enqueue_style( 'masterstudy-lms-ai-settings' );
	wp_enqueue_script( 'masterstudy-lms-ai-settings' );
?>

<div class="wpcfto_generic_field wpcfto_generic_field__select">
	<div class="instructor-access-buttons">
		<div class="wpcfto-field-aside">
			<label class="wpcfto-field-aside__label" for="">
				<?php echo esc_html__( 'Instructor Access', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</label>
			<div class="wpcfto-field-description wpcfto-field-description__before description">
				<?php echo esc_html__( 'Allow instructors to access AI features.', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</div>
		</div>
		<a href="<?php echo esc_url( admin_url() . 'admin.php?page=manage_users' ); ?>" class="button">
			<?php echo esc_html__( 'Manage', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</a>
	</div>
</div>
