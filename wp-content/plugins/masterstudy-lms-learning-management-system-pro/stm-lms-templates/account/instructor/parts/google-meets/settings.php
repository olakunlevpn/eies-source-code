<?php
STM_LMS_Templates::show_lms_template(
	'components/drawer',
	array(
		'default_slot' => 'masterstudy-account-google-meets-meetings__settings-drawer-template',
		'drawer_class' => 'masterstudy-account-google-meets-meetings__settings-drawer',
	)
);

$user_id              = get_current_user_id();
$frontend_gm_settings = get_user_meta( $user_id, 'frontend_instructor_google_meet_settings', true );
?>
<template id="masterstudy-account-google-meets-meetings__settings-drawer-template">
	<div
		class="masterstudy-account-google-meets-meetings__settings"
		data-default-timezone="<?php echo esc_attr( $frontend_gm_settings['timezone'] ?? 'UTC' ); ?>"
		data-default-updates="<?php echo esc_attr( $frontend_gm_settings['send_updates'] ?? 'all' ); ?>"
	>
		<div class="masterstudy-account-google-meets-meetings__settings-header">
			<span class="masterstudy-account-google-meets-meetings__settings-title">
				<?php echo esc_html__( 'Google meet settings', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</span>
			<a href="#" class="masterstudy-account-google-meets-meetings__settings-close">
				<span class="stmlms-times"></span>
			</a>
		</div>
		<div class="masterstudy-account-google-meets-meetings__settings-content">
			<div class="masterstudy-account-google-meets-meetings__settings-field-header">
				<div class="masterstudy-account-google-meets-meetings__settings-field-header-info">
					<span class="masterstudy-account-google-meets-meetings__settings-label">
						<?php echo esc_html__( 'Meet account status', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
					<span class="masterstudy-account-google-meets-meetings__settings-info-description">
						<?php echo esc_html__( 'You are currently connected to Meet Reset Credential', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
				</div>
				<div class="masterstudy-account-google-meets-meetings__settings-field-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'Change account', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'outline',
							'size'  => 'sm',
							'id'    => 'front-settings-change-account',
							'class' => 'masterstudy-account-google-meets-meetings__settings-change-account',
						)
					);
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'title' => esc_html__( 'Reset Credential', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'danger-light',
							'size'  => 'sm',
							'id'    => 'front-settings-reset-credentials',
							'class' => 'masterstudy-account-google-meets-meetings__settings-reset',
						)
					);
					?>
				</div>
			</div>
			<div class="masterstudy-account-google-meets-meetings__settings-field">
				<div class="masterstudy-account-google-meets-meetings__settings-field-info">
					<span class="masterstudy-account-google-meets-meetings__settings-control-label">
						<?php echo esc_html__( 'Default timezone', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
				</div>
				<div class="masterstudy-account-google-meets-meetings__settings-field-actions">
					<?php
					$timezones = stm_lms_get_timezone_options();
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'     => 'front-meeting-timezone-settings',
							'select_name'   => 'front-meeting-timezone-settings',
							'placeholder'   => esc_html__( 'Select timezone', 'masterstudy-lms-learning-management-system-pro' ),
							'default'       => $frontend_gm_settings['timezone'] ?? 'UTC',
							'apply_default' => true,
							'is_queryable'  => false,
							'clearable'     => false,
							'options'       => $timezones,
						)
					);
					?>
				</div>
				<span class="masterstudy-account-google-meets-meetings__settings-control-description">
					<?php echo esc_html__( 'Set the default timezone for Google Meet', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
			</div>
			<div class="masterstudy-account-google-meets-meetings__settings-field">
				<div class="masterstudy-account-google-meets-meetings__settings-field-info">
					<span class="masterstudy-account-google-meets-meetings__settings-control-label"><?php echo esc_html__( 'Default reminder time (minutes)', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</div>
				<div class="masterstudy-account-google-meets-meetings__settings-field-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/input',
						array(
							'input_type'  => 'number',
							'input_class' => 'masterstudy-account-google-meets-meetings__settings-input masterstudy-account-google-meets-meetings__settings-reminder',
							'input_value' => $frontend_gm_settings['reminder'] ?? 30,
						)
					);
					?>
				</div>
					<span class="masterstudy-account-google-meets-meetings__settings-control-description">
						<?php echo esc_html__( 'Set a default reminder time to get an email notification', 'masterstudy-lms-learning-management-system-pro' ); ?>
					</span>
			</div>
			<div class="masterstudy-account-google-meets-meetings__settings-field">
				<div class="masterstudy-account-google-meets-meetings__settings-field-info">
					<span class="masterstudy-account-google-meets-meetings__settings-control-label"><?php echo esc_html__( 'Send updates', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
				</div>
				<div class="masterstudy-account-google-meets-meetings__settings-field-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/select',
						array(
							'select_id'     => 'front-send-updates-settings',
							'select_name'   => 'front-send-updates-settings',
							'placeholder'   => esc_html__( 'Select updates', 'masterstudy-lms-learning-management-system-pro' ),
							'default'       => $frontend_gm_settings['send_updates'] ?? 'all',
							'apply_default' => true,
							'is_queryable'  => false,
							'clearable'     => false,
							'options'       => array(
								'all'          => esc_html__( 'All', 'masterstudy-lms-learning-management-system-pro' ),
								'externalOnly' => esc_html__( 'External Only', 'masterstudy-lms-learning-management-system-pro' ),
								'none'         => esc_html__( 'None', 'masterstudy-lms-learning-management-system-pro' ),
							),
						)
					);
					?>
				</div>
					<span class="masterstudy-account-google-meets-meetings__settings-control-description"><?php echo esc_html__( 'Select how to send notifications about the creation of the new event. Note that some emails might still be sent.', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
			</div>
		</div>
		<?php do_action( 'stm_lms_after_groups_end' ); ?>
		<div class="masterstudy-account-google-meets-meetings__settings-footer">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => esc_html__( 'Save', 'masterstudy-lms-learning-management-system-pro' ),
					'link'  => '#',
					'style' => 'primary',
					'size'  => 'sm',
					'class' => 'masterstudy-account-google-meets-meetings__settings-save',
				)
			);
			?>
		</div>
		<div class="masterstudy-account-google-meets-meetings__settings-notice masterstudy-account-utility_hidden">
			<span class="masterstudy-account-google-meets-meetings__settings-notice-text">
				<?php echo esc_html__( 'Settings saved successfully', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</span>
			<i class="stmlms-refresh-2 stmlms-spin masterstudy-account-google-meets-meetings__settings-notice-loading masterstudy-account-utility_hidden"></i>
			<i class="stmlms-check-3 masterstudy-account-google-meets-meetings__settings-notice-success masterstudy-account-utility_hidden" aria-hidden="true"></i>
		</div>
	</div>
</template>
