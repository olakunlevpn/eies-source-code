<?php
// phpcs:ignoreFile
$_post_id         = get_the_ID();
$_post_type       = get_post_type( $_post_id );
$assets           = trailingslashit( MSLMS_ZOOM_URL ) . 'build/';
$meeting_data     = get_post_meta( $_post_id, 'stm_zoom_data', true );
$meeting_password = get_post_meta( $_post_id, 'stm_password', true );
$meeting_id       = '';
$settings         = get_option( 'ms_zoom_settings', array() );
$api_key          = ! empty( $settings['sdk_key'] ) ? $settings['sdk_key'] : '';
$api_secret       = ! empty( $settings['sdk_secret'] ) ? $settings['sdk_secret'] : '';
$enforce_login    = absint( ! empty( get_post_meta( $_post_id, 'stm_enforce_login', true ) ) );
$tk               = '';

if ( ! empty( $meeting_data ) ) {
	$meeting_id = ! empty( $meeting_data['id'] ) ? $meeting_data['id'] : '';
}

$username = esc_attr__( 'Guest', 'masterstudy-lms-learning-management-system-pro' );
$email    = '';

$lang              = 'en-US';
$registration_form = false;

if ( 'stm-zoom-webinar' === $_post_type ) {
	$registration_form = true;

	if ( isset( $_POST['user_name'] ) && isset( $_POST['user_email'] ) && isset( $_POST['user_lang'] ) ) {
		$registration_form = false;
		$username          = sanitize_text_field( $_POST['user_name'] );
		$email             = sanitize_text_field( $_POST['user_email'] );
		$lang              = sanitize_text_field( $_POST['user_lang'] );
	}
}

if ( is_user_logged_in() ) {
	$registration_form = false;
	$user              = wp_get_current_user();
	$username          = $user->user_login;
	$email             = $user->user_email;
}

if ( $enforce_login ) {
	if ( class_exists( '\Api\Controllers\Users' ) ) {
		$webinars_api_object = new Zoom\Controllers\Meetings();
		$response            = $webinars_api_object->listRegistrants( $meeting_id );
		if ( is_array( $response ) && 200 === $response['code'] && isset( $response['registrants'] ) ) {
			$registrant = array_reduce(
				$response['registrants'],
				function ( $carry, $user ) use ( $email ) {
					if ( $user['email'] === $email && 'approved' === $user['status'] ) {
						$carry = $user;
					}

					return $carry;
				},
				false
			);

			if ( ! empty( $registrant ) ) {
				$url_components = wp_parse_url( $registrant['join_url'] );
				parse_str( $url_components['query'], $url_params );
				$tk = $url_params['tk'];
			}
		}
	}
}

?>
<!DOCTYPE html>
<head>
	<title><?php the_title(); ?></title>
	<meta charset="utf-8"/>
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">
</head>
<style>
	#stm-zoom-webinar-zmmtg-root {
		top: 0;
		left: 0;
		position: fixed;
		width: 100%;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		background-color: #fff;
		z-index: 1;
	}

	.stm-zoom-webinar-reg-form {
		display: flex;
		flex-direction: column;
		padding: 60px 50px;
		border-radius: 15px;
		box-shadow: 0 0 38px 0 rgb(0 0 0/10%);
		background-color: #fff;
	}

	.stm-zoom-webinar-reg-form input, .stm-zoom-webinar-reg-form select {
		border: 1px solid #d2d3d6;
		border-radius: 30px;
		background-color: #f3f4f6;
		box-sizing: border-box;
		font-size: 16px;
		padding: 10px 30px;
		min-width: 300px;
		margin-bottom: 10px;
		outline: none !important;
	}

	.stm-zoom-webinar-reg-form input[type=text], .stm-zoom-webinar-reg-form input[type=text]:focus {
		outline: none !important;
	}

	.stm-zoom-webinar-reg-form select {
		appearance: none;
		background-image: url("<?php echo esc_url( $assets ); ?>/img/select.svg");
		background-repeat: no-repeat, repeat;
		background-position: right .7em top 50%, 0 0;
		/* icon size, then gradient */
		background-size: .65em auto, 100%;
	}

	.stm-zoom-webinar-submit {
		width: 130px;
		padding: 10px;
		background-color: transparent;
		border-radius: 30px;
		color: #0e71eb;
		margin: 16px auto 0;
		font-weight: 700;
		border: 2px #0e71eb solid;
		font-size: 16px;
	}

	.stm-zoom-webinar-submit:hover {
		background-color: #0e71eb;
		color: #fff;
	}

</style>

<body>
<?php if ( $registration_form ) : ?>
	<div id="stm-zoom-webinar-zmmtg-root">
		<form method="POST">
			<div class="stm-zoom-webinar-reg-form">
				<input type="text" placeholder="<?php echo esc_html__( 'Name', 'masterstudy-lms-learning-management-system-pro' ); ?>"
					name="user_name" required>
				<input type="email" placeholder="<?php echo esc_html__( 'Email', 'masterstudy-lms-learning-management-system-pro' ); ?>"
					name="user_email" required>
				<select id="meeting_lang" class="sdk-select" name="user_lang">
					<option value="en-US"><?php echo esc_html( 'English' ); ?></option>
					<option value="de-DE"><?php echo esc_html( 'German Deutsch' ); ?></option>
					<option value="es-ES"><?php echo esc_html( 'Spanish Español' ); ?></option>
					<option value="fr-FR"><?php echo esc_html( 'French Français' ); ?></option>
					<option value="jp-JP"><?php echo esc_html( 'Japanese 日本語' ); ?></option>
					<option value="pt-PT"><?php echo esc_html( 'Portuguese Portuguese' ); ?></option>
					<option value="ru-RU"><?php echo esc_html( 'Russian Русский' ); ?></option>
					<option value="zh-CN"><?php echo esc_html( 'Chinese 简体中文' ); ?></option>
					<option value="zh-TW"><?php echo esc_html( 'Chinese 繁体中文' ); ?></option>
					<option value="ko-KO"><?php echo esc_html( 'Korean 한국어' ); ?></option>
					<option value="vi-VN"><?php echo esc_html( 'Vietnamese Tiếng Việt' ); ?></option>
					<option value="it-IT"><?php echo esc_html( 'Italian italiano' ); ?></option>
				</select>
				<input type="submit" value="<?php echo esc_html__( 'Login', 'masterstudy-lms-learning-management-system-pro' ); ?>"
					class="stm-zoom-webinar-submit">
			</div>
		</form>
	</div>
<?php endif; ?>
<?php if ( ! empty( $api_key ) && ! empty( $api_secret ) ) : ?>
<script>
	var API_KEY = '<?php echo esc_js( $api_key ); ?>';
	var SECRET_KEY = '<?php echo esc_js( $api_secret ); ?>';
	var leaveUrl = '<?php echo esc_url( get_home_url( '/' ) ); ?>';
	var endpoint = '<?php echo esc_url( admin_url( 'admin-ajax.php?action=MSLMS_ZOOM_meeting_sign' ) ); ?>';
	var meeting_id = '<?php echo esc_js( $meeting_id ); ?>';
	var meeting_password = '<?php echo esc_js( $meeting_password ); ?>';
	var username = '<?php echo esc_js( $username ); ?>';
	var email = '<?php echo esc_js( $email ); ?>';
	var lang = '<?php echo esc_js( $lang ); ?>';
	var role = 0;
	var enforce_login = <?php echo esc_js( $enforce_login ); ?>;
	var tk = '<?php echo esc_js( $tk ); ?>';
</script>
<?php endif; ?>

<?php
if ( ! $registration_form && ! empty( $api_key ) && ! empty( $api_secret ) ) :
	// @codingStandardsIgnoreStart
	?>
	<script src="<?php echo esc_url( $assets ); ?>js/core-libs.js"></script>
	<script src="<?php echo esc_url( $assets ); ?>js/event.js"></script>
<?php
	// @codingStandardsIgnoreEnd
elseif ( ! $registration_form ) :
	// Show error message when SDK credentials are not configured
	?>
	<div style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24; margin: 20px;">
		<h3><?php esc_html_e( 'Zoom SDK Not Configured', 'masterstudy-lms-learning-management-system-pro' ); ?></h3>
		<p><?php esc_html_e( 'Please configure your Zoom SDK credentials in the Zoom Conference settings to enable "Join in Browser" functionality.', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=mslms_zoom_settings' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Configure Zoom SDK', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</a>
		</p>
	</div>
<?php
endif;
?>

</body>

</html>
