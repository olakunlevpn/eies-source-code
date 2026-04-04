<?php

wp_enqueue_style( 'masterstudy-account-google-meets-wizard' );
wp_enqueue_script( 'masterstudy-account-google-meets-wizard' );
wp_localize_script(
	'masterstudy-account-google-meets-wizard',
	'stm_google_meet_ajax_variable',
	array(
		'url'          => admin_url( 'admin-ajax.php' ),
		'nonce'        => wp_create_nonce( 'stm-lms-gm-nonce' ),
		'translations' => array(
			'copied' => esc_html__( 'Copied', 'masterstudy-lms-learning-management-system-pro' ),
			'copy'   => esc_html__( 'Copy', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);
?>
<div class="masterstudy-account-google-meets-wizard">
	<div class="masterstudy-account-google-meets-wizard__header">
		<img class="masterstudy-account-google-meets-wizard__header-logo"
			src="<?php echo esc_attr( STM_LMS_PRO_URL . 'assets/img/meet-form-logo.svg' ); ?>"
			alt="google meet">
		<div class="masterstudy-account-google-meets-wizard__header-steps">
			<?php echo esc_html__( 'Step:', 'masterstudy-lms-learning-management-system-pro' ); ?>
			<span class="masterstudy-account-google-meets-wizard__header-steps-value"></span>
			<?php echo esc_html__( 'from 4', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</div>
	</div>

	<div class="masterstudy-account-google-meets-wizard__content">
	</div>

	<template id="masterstudy-account-google-meets-wizard__step-0">
		<div class="masterstudy-account-google-meets-wizard__step">
			<div class="masterstudy-account-google-meets-wizard__step-header">
				<span class="masterstudy-account-google-meets-wizard__step-header-title"><?php echo esc_html__( 'Setup your Google Meet Integration', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				<span class="masterstudy-account-google-meets-wizard__step-header-subtitle">
					<?php echo esc_html__( 'Google Meet integration enables seamless video conferencing will enhance collaboration and communication between users. Follow the steps below to get started.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
			</div>
			<div class="masterstudy-account-google-meets-wizard__step-content">
				<div class="masterstudy-account-google-meets-wizard__step-content-header">
					<span class="masterstudy-account-google-meets-wizard__step-content-header-number">1</span>
					<span class="masterstudy-account-google-meets-wizard__step-content-header-title"><?php echo esc_html__( 'Open Google Developer Console', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</div>

				<span class="masterstudy-account-google-meets-wizard__step-content-instructions">
					<?php echo esc_html__( 'Access the Google Developer Console to create and configure your project for the Google Meet addon following Documentation.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
				<div class="masterstudy-account-google-meets-wizard__step-content-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title'  => esc_html__( 'Open Dev Console', 'masterstudy-lms-learning-management-system-pro' ),
							'link'   => 'https://console.cloud.google.com/apis/dashboard',
							'style'  => 'outline',
							'size'   => 'sm',
							'target' => '_blank',
						)
					);

					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'class'  => 'masterstudy-account-google-meets-wizard__documentation-btn',
							'title'  => esc_html__( 'Documentation', 'masterstudy-lms-learning-management-system-pro' ),
							'link'   => 'https://docs.stylemixthemes.com/masterstudy-lms/lms-pro-addons/google-meet',
							'style'  => 'primary',
							'size'   => 'sm',
							'target' => '_blank',
						)
					);
					?>
				</div>
			</div>
		</div>
	</template>
	<template id="masterstudy-account-google-meets-wizard__step-1">
		<div class="masterstudy-account-google-meets-wizard__step">
			<div class="masterstudy-account-google-meets-wizard__step-content">
				<div class="masterstudy-account-google-meets-wizard__step-content-header">
					<span class="masterstudy-account-google-meets-wizard__step-content-header-number">2</span>
					<span class="masterstudy-account-google-meets-wizard__step-content-header-title"><?php echo esc_html__( 'Set Web Application URL in Google Developer Console', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</div>

				<span class="masterstudy-account-google-meets-wizard__step-content-instructions">
					<?php echo esc_html__( 'The Web Application URL is an essential configuration that establishes the connection between the add-on and the Google Meet integration. By using the URL below, you enable seamless integration and allow your users to access Google Meet features directly from your site.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
				<div class="masterstudy-account-google-meets-wizard__step-content-actions">
					<input class="masterstudy-account-google-meets-wizard__copy-text" type="text" disabled
						value="<?php echo esc_url( ms_plugin_user_account_url( 'google-meets' ) ); ?>">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-account-google-meets-wizard__copy-btn',
							'title' => esc_html__( 'Copy', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'outline',
							'size'  => 'sm',
						)
					);
					?>
				</div>
			</div>
		</div>
	</template>
	<template id="masterstudy-account-google-meets-wizard__step-2">
		<div class="masterstudy-account-google-meets-wizard__step">
			<div class="masterstudy-account-google-meets-wizard__step-content">
				<div class="masterstudy-account-google-meets-wizard__step-content-header">
					<span class="masterstudy-account-google-meets-wizard__step-content-header-number">3</span>
					<span class="masterstudy-account-google-meets-wizard__step-content-header-title"><?php echo esc_html__( 'Upload Credentials .JSON File', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</div>

				<span class="masterstudy-account-google-meets-wizard__step-content-instructions">
					<?php echo esc_html__( 'In this step, you need to upload the credentials .JSON file. The credentials .JSON file contains the necessary authentication information that allows securely interacting with the Google Meet API.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
				<div class="masterstudy-account-google-meets-wizard__step-content-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-account-google-meets-wizard__select-file',
							'title' => esc_html__( 'Select file', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'outline',
							'size'  => 'sm',
						)
					);
					?>
					<input class="masterstudy-account-google-meets-wizard__file-name masterstudy-account-utility_hidden" type="text" disabled value="">
					<input type="file" class="masterstudy-account-google-meets-wizard__select-file-input" accept=".json,application/json" >
					<img src="<?php echo esc_url( STM_LMS_PRO_URL . 'assets/img/close_meet.png' ); ?>"
						class="masterstudy-account-google-meets-wizard__select-file-cancel" alt="remove file">
				</div>
			</div>
		</div>
	</template>
	<template id="masterstudy-account-google-meets-wizard__step-3">
		<div class="masterstudy-account-google-meets-wizard__step">
			<div class="masterstudy-account-google-meets-wizard__step-content">
				<div class="masterstudy-account-google-meets-wizard__step-content-header">
					<span class="masterstudy-account-google-meets-wizard__step-content-header-number">3</span>
					<span class="masterstudy-account-google-meets-wizard__step-content-header-title"><?php echo esc_html__( 'Grant App Permissions', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</div>

				<span class="masterstudy-account-google-meets-wizard__step-content-instructions">
					<?php echo esc_html__( 'Click Grant Permissions to give access to your Google account. Please allow all required permissions so that this app works correctly.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
			</div>
		</div>
	</template>

	<div class="masterstudy-account-google-meets-wizard__footer">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'class' => 'masterstudy-account-utility_hidden',
				'id'    => 'masterstudy-account-google-meets-wizard__back-btn',
				'title' => esc_html__( 'Back', 'masterstudy-lms-learning-management-system-pro' ),
				'link'  => '#',
				'style' => 'outline',
				'size'  => 'sm',
			)
		);

		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'id'    => 'masterstudy-account-google-meets-wizard__next-btn',
				'title' => esc_html__( 'Next', 'masterstudy-lms-learning-management-system-pro' ),
				'link'  => '#',
				'style' => 'primary',
				'size'  => 'sm',
			)
		);

		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'class' => 'masterstudy-account-utility_hidden',
				'id'    => 'masterstudy-account-google-meets-wizard__reset-creds-btn',
				'title' => esc_html__( 'Reset Credential', 'masterstudy-lms-learning-management-system-pro' ),
				'link'  => '#',
				'style' => 'outline',
				'size'  => 'sm',
			)
		);

		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'class' => 'masterstudy-account-utility_hidden',
				'id'    => 'masterstudy-account-google-meets-wizard__grant-permission-btn',
				'title' => esc_html__( 'Go to google\'s consent screen', 'masterstudy-lms-learning-management-system-pro' ),
				'link'  => '#',
				'style' => 'primary',
				'size'  => 'sm',
			)
		);
		?>
	</div>
</div>
