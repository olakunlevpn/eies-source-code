<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleOpenAuth;

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );

do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );
wp_enqueue_style( 'masterstudy-account-main' );

$user_id                       = get_current_user_id();
$google_api_credentials_config = get_user_meta( $user_id, GoogleOpenAuth::CONFIG_NAME, true );
$google_api_credentials_token  = get_user_meta( $user_id, GoogleOpenAuth::TOKEN_NAME, true );
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<?php
		if ( empty( $google_api_credentials_config ) || empty( $google_api_credentials_token ) ) {
			STM_LMS_Templates::show_lms_template( 'account/instructor/parts/google-meets/wizard' );
		} else {
			STM_LMS_Templates::show_lms_template( 'account/instructor/parts/google-meets/meetings' );
		}
		?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
