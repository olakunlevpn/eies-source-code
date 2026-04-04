<?php
// phpcs:ignoreFile
$settings           = get_option( 'ms_zoom_settings', array() );
$api_key            = ! empty( $settings['api_key'] ) ? $settings['api_key'] : '';
$api_secret         = ! empty( $settings['api_secret'] ) ? $settings['api_secret'] : '';
$auth_account_id    = ! empty( $settings['auth_account_id'] ) ? $settings['auth_account_id'] : '';
$auth_client_id     = ! empty( $settings['auth_client_id'] ) ? $settings['auth_client_id'] : '';
$auth_client_secret = ! empty( $settings['auth_client_secret'] ) ? $settings['auth_client_secret'] : '';

$action     = ! empty( $_POST['stm_action'] ) ? sanitize_text_field( $_POST['stm_action'] ) : '';
$email      = ! empty( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
$first_name = ! empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
$last_name  = ! empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
$type       = ! empty( $_POST['stm_user_type'] ) ? sanitize_text_field( $_POST['stm_user_type'] ) : '';

$error_message  = '';
$message_status = '';
if ( ( empty( $api_key ) && empty( $api_secret ) ) && ( empty( $auth_account_id ) && empty( $auth_client_id ) && empty( $auth_client_secret ) ) ) {
	$error_message = esc_html__( 'Provide API key & API secret key to crete new user', 'masterstudy-lms-learning-management-system-pro' );
}

if ( ! empty( $email ) && ! empty( $action ) && ! empty( $type ) ) {
	$_POST['action']    = $action;
	$user_data          = array(
		'email'      => $email,
		'type'       => $type,
		'first_name' => $first_name,
		'last_name'  => $last_name,
	);
	$_POST['user_info'] = $user_data;

	$zoom     = new MSLMSUserApi();
	$new_user = $zoom->createUser( $user_data );

	if ( ! empty( $new_user ) && ! empty( $new_user['code'] ) ) {
		if ( 201 === $new_user['code'] ) {
			$message        = esc_html__( 'User added successfully', 'masterstudy-lms-learning-management-system-pro' );
			$message_status = 'success';
			delete_transient( 'mslms_zoom_users' );
		} elseif ( ! empty( $new_user['message'] ) ) {
			$message        = esc_html( $new_user['message'] );
			$message_status = 'error';
		} else {
			$message        = esc_html__( 'Error', 'masterstudy-lms-learning-management-system-pro' );
			$message_status = 'error';
		}
		if ( 'No privilege.' === $message ) {
			$message = esc_html__( "You don't have permission to add a new user", 'masterstudy-lms-learning-management-system-pro' );
		}
		$error_message = '<div class="MSLMS_ZOOM_nonce ' . esc_attr( $message_status ) . '"><h3>' . wp_kses_post( $message ) . '</h3></div>';
	}
}
?>
<div class="add_zoom_user_page">
	<h1><?php esc_html_e( 'Add User', 'masterstudy-lms-learning-management-system-pro' ); ?></h1>
	<?php
	echo wp_kses_post( $error_message );
	if ( ( ! empty( $api_key ) && ! empty( $api_secret ) ) || ( ! empty( $auth_account_id ) && ! empty( $auth_client_id ) && ! empty( $auth_client_secret ) ) ) :
		?>
		<div class="stm-lms-tab active stm_metaboxes_grid">
			<form id="MSLMS_ZOOM_add_user" method="post" class="stm_metaboxes_grid__inner">
				<div class="form-group">
					<label
						for="stm_action"><?php esc_html_e( 'Action (Required)', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
					<select name="stm_action" id="stm_action" required>
						<option selected value="create">Create</option>
						<option value="autoCreate">Auto Create</option>
						<option value="custCreate">Cust Create</option>
						<option value="ssoCreate">SSO Create</option>
					</select>
					<div class="description">
						<p>
							<b><?php esc_html_e( 'Create', 'masterstudy-lms-learning-management-system-pro' ); ?></b> - <?php esc_html_e( 'User will get an email sent from Zoom. There is a confirmation link in this email. The user will then need to use the link to activate their Zoom account. The user can then set or change their password.', 'masterstudy-lms-learning-management-system-pro' ); ?><br>
							<b><?php esc_html_e( 'Auto Create', 'masterstudy-lms-learning-management-system-pro' ); ?></b> - <?php esc_html_e( 'This action is provided for the enterprise customer who has a managed domain. This feature is disabled by default because of the security risk involved in creating a user who does not belong to your domain.', 'masterstudy-lms-learning-management-system-pro' ); ?><br>
							<b><?php esc_html_e( 'Cust Create', 'masterstudy-lms-learning-management-system-pro' ); ?></b> - <?php esc_html_e( 'Users created via this option do not have passwords and will not have the ability to log into the Zoom Web Portal or the Zoom Client. To use this option, you must contact the ISV Platform Sales team at isv@zoom.us.', 'masterstudy-lms-learning-management-system-pro' ); ?><br>
							<b><?php esc_html_e( 'SSO Create', 'masterstudy-lms-learning-management-system-pro' ); ?></b> - <?php esc_html_e( 'This action is provided for the enabled “Pre-provisioning SSO User” option. A user created in this way has no password. If not a basic user, a personal vanity URL using the user name (no domain) of the provisioning email will be generated. If the user name or PMI is invalid or occupied, it will use a random number or random personal vanity URL.', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label
						for="stm_email"><?php esc_html_e( 'Email (required)', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
					<input id="stm_email" type="email" name="email" required/>
				</div>
				<div class="form-group">
					<label
						for="stm_first_name"><?php esc_html_e( 'First Name', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
					<input id="stm_first_name" type="text" name="first_name"/>
				</div>
				<div class="form-group">
					<label
						for="stm_last_name"><?php esc_html_e( 'Last Name', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
					<input id="stm_last_name" type="text" name="last_name"/>
				</div>
				<div class="form-group">
					<label
						for="stm_user_type"><?php esc_html_e( 'User Type (Required)', 'masterstudy-lms-learning-management-system-pro' ); ?></label>
					<select name="stm_user_type" id="stm_user_type" required>
						<option selected value="1">Basic</option>
						<option value="2">Licensed</option>
						<option value="3">On-prem</option>
					</select>
				</div>
				<div class="form-group">
					<button class="button load_button"
							type="submit"><?php esc_html_e( 'Save', 'masterstudy-lms-learning-management-system-pro' ); ?></button>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div>
