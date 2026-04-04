<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

wp_enqueue_style( 'meet-intro-links-css', STM_LMS_PRO_URL . 'addons-plus/GoogleMeet/Settings/components/meet-intro-links/css/meet-intro-links.css', null, get_bloginfo( 'version' ), 'all' );
?>

<div class="wpcfto_generic_field wpcfto_generic_field__select" field_data="[object Object]">
	<div class="meet-link-buttons">
		<a href="<?php echo esc_url( admin_url() . 'post-new.php?post_type=stm-google-meets' ); ?>" class="btn-outlined nuxy_meet_new">
			<?php echo esc_html__( 'Add a new meeting', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url() . 'edit.php?post_type=stm-google-meets' ); ?>" class="btn-outlined nuxy_meeting_list">
			<?php echo esc_html__( 'Meetings List', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</a>
		<a href="<?php echo esc_url( 'https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/google-meet' ); ?>" class="btn-outlined nuxy_documentation" target="_blank">
			<?php echo esc_html__( 'Documentation', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</a>
	</div>
</div>
