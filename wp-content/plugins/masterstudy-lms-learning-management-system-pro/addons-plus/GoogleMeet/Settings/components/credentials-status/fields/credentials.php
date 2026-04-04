<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

wp_enqueue_style( 'stm-credentials-css', STM_LMS_PRO_URL . 'addons-plus/GoogleMeet/Settings/components/credentials-status/css/credentials.css', null, get_bloginfo( 'version' ), 'all' );
wp_enqueue_script( 'stm-credentials-js', STM_LMS_PRO_URL . 'addons-plus/GoogleMeet/Settings/components/credentials-status/js/credentials.js', array( 'jquery' ), STM_LMS_PRO_VERSION, true );
wp_localize_script(
	'stm-credentials-js',
	'stm_google_meet_ajax_variable',
	array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'stm-lms-gm-nonce' ),
	)
);
?>

<div class="wpcfto_generic_field wpcfto_generic_field__select" field_data="[object Object]">
	<div class="wpcfto-field-aside">
		<label class="wpcfto-field-aside__label"><?php echo esc_html__( 'Meet account status', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
		<div class="wpcfto-field-description wpcfto-field-description__before description">
			<?php echo esc_html__( 'You are currently connected to Meet Reset Credential', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</div>
	</div>
	<div class="wpcfto-field-content">
		<div class="wpcfto-admin-btns">
			<div class="credential-buttons">
				<button class="btn-outlined nuxy_reset_credentials">
					<?php echo esc_html__( 'Reset Credentials', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</button>
				<button class="btn-outlined nuxy_change_account">
					<?php echo esc_html__( 'Change Account', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</button>
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
</div>
