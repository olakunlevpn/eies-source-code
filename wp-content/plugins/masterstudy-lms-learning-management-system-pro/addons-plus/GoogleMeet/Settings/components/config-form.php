<?php
/**
 * Config Form template.
 */
?>
<div class="stm-lms-google-meet-wrapper">

	<form id="regForm" class="google-meet-steps">
		<div class="gm-header">
			<div class="gm-logo">
				<img src="<?php echo esc_url( STM_LMS_PRO_URL . 'assets/img/meet-form-logo.svg' ); ?>" alt="google meet logo">
			</div>
		</div>

		<div class="tab" style="display: block">
			<div class="gm-tab1-head">
				<h3><?php echo esc_html__( 'Setup your Google Meet Integration', 'masterstudy-lms-learning-management-system-pro' ); ?></h3>
				<p><?php echo esc_html__( 'Google Meet integration enables seamless video conferencing will enhance collaboration and communication between users. Follow the steps below to get started.', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
			</div>
			<div class="gm-tab1-head">
				<div class="intro-head">
					<span>1</span>
					<p class="title"><?php echo esc_html__( 'Open Google Developer Console', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
				</div>
				<p><?php echo esc_html__( 'Access the Google Developer Console to create and configure your project for the Google Meet addon following Documentation.', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
				<div class="head-buttons">
					<a href="https://console.cloud.google.com/apis/dashboard" class="gm-btn-outlined" target="_blank">
						<?php echo esc_html__( 'Open Dev Console', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</a>
					<a href="https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/google-meet" class="gm-secondary-btn" target="_blank">
						<?php echo esc_html__( 'Documentation', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</a>
				</div>
			</div>
		</div>
		<div class="tab">
			<div class="gm-tab1-head">
				<div class="intro-head">
					<span>2</span>
					<p class="title"><?php echo esc_html__( 'Set Web Application URL in Google Developer Console', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
				</div>
				<p><?php echo esc_html__( 'The Web Application URL is an essential configuration that establishes the connection between the add-on and the Google Meet integration. By using the URL below, you enable seamless integration and allow your users to access Google Meet features directly from your site.', 'masterstudy-lms-learning-management-system-pro' ); ?></p>
				<div class="gm-copy-url">
					<input type="text" class="gm-copy-url" id="gm-copy-url"
						value="<?php echo esc_url( admin_url() . 'admin.php?page=google_meet_settings' ); ?>" disabled />
					<a class="gm-btn lms-gm-btn-copy">
						<?php echo esc_html__( 'Copy', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</a>
				</div>
			</div>
		</div>
		<div class="tab" id="jsonCredentials">
			<div class="gm-tab1-head">
				<div class="intro-head">
					<span>3</span>
					<p class="title">
						<?php echo esc_html__( 'Upload Credentials .JSON File', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</p>
				</div>
				<p>
					<?php echo esc_html__( 'In this step, you need to upload the credentials .JSON file. The credentials .JSON file contains the necessary authentication information that allows securely interacting with the Google Meet API.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</p>
				<div class="gm-json-config-wrapper">
					<div class="gm-json-config-upload">
						<label for="lms-gm-upload-file" id="lms-gm-upload-file-label">
							<?php echo esc_html__( 'Select File', 'masterstudy-lms-learning-management-system-pro' ); ?>
						</label>
						<input type="file" id="lms-gm-upload-file" class="lms-gm-upload-file" name="file"/>
					</div>
					<img src="<?php echo esc_url( STM_LMS_PRO_URL . 'assets/img/close_meet.png' ); ?>"
						class="cancel-uploaded-file" alt="close window">
				</div>
			</div>
		</div>
		<div class="tab">
			<div class="gm-tab1-head">
				<div class="intro-head">
					<span>4</span>
					<p class="title">
						<?php echo esc_html__( 'Grant App Permissions', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</p>
				</div>
				<p>
					<?php echo esc_html__( 'Click Grant Permissions to give access to your Google account. Please allow all required permissions so that this app works correctly.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</p>
			</div>
		</div>
		<div id="meetSteps">
			<?php echo esc_html__( 'Step:', 'masterstudy-lms-learning-management-system-pro' ); ?>
			<span class="step">1</span>
			<span class="step">2</span>
			<span class="step">3</span>
			<span class="step">4</span>
			<?php echo esc_html__( 'from 4', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</div>

		<div class="next-btn-buttons">
			<button type="button" id="prevBtn" class="gm-prev-btn" style="opacity: 0;"><?php echo esc_html__( 'Back', 'masterstudy-lms-learning-management-system-pro' ); ?></button>
			<button type="button" id="nextBtn" class="gm-next-btn"><?php echo esc_html__( 'Next', 'masterstudy-lms-learning-management-system-pro' ); ?></button>
		</div>
	</form>
</div>
