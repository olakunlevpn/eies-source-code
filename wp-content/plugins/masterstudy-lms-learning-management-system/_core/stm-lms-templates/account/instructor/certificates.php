<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
?>

<div class="stm-lms-wrapper user-account-page">
	<div class="container">
		<?php
		do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user );
		STM_LMS_Templates::show_lms_template( 'account/instructor/parts/certificates/grid' );
		?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
