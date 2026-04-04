<?php
$license_data = get_option( 'stm_fs_license_data' );
if (
	$license_data &&
	is_admin() &&
	isset( $_GET['page'] ) && 'stm-lms-settings-account' === $_GET['page'] || isset( $_GET['page'] ) && 'stm-lms-license' === $_GET['page']
) {
	$expires_at   = strtotime( $license_data['expires'] );
	$now          = time();
	$seconds_left = $expires_at - $now;
	$days_left    = floor( $seconds_left / DAY_IN_SECONDS );
	$status_text  = '';

	if ( $seconds_left <= 0 ) {
		$status_text      = __( 'Your Trial Expired', 'masterstudy' );
		$status_desc_text = __( 'Your trial has ended. Upgrade now to continue building and managing your courses with the latest features.', 'masterstudy' );
	} elseif ( $days_left > 10 ) {
		$status_text      = __( 'Your Trial Ending Soon', 'masterstudy' );
		$status_desc_text = __( 'Time’s running out! Secure your advanced features by upgrading now.', 'masterstudy' );
	} else {
		$status_text      = __( 'Your Trial Expired', 'masterstudy' );
		$status_desc_text = __( 'Your trial has ended. Upgrade now to continue building and managing your courses with the latest features.', 'masterstudy' );
	}
	?>
	<div class="masterstudy-wizard-section license-status-section">
		<div id="step-status-license">
			<div class="license-upgrade-info">
				<div class="license-upgrade-info__content">
					<h2><?php echo esc_html( $status_text ); ?></h2>
					<p><?php echo esc_html( $status_desc_text ); ?></p>
					<ul class="license-upgrade-dates">
					<?php if ( ! empty( $license_data['activated_at'] ) ) : ?>
						<li><strong><?php echo esc_html__( 'Activation Date:', 'masterstudy' ); ?></strong> <?php echo esc_html( date( 'd.m.Y', strtotime( $license_data['activated_at'] ) ) ); ?></li>
					<?php endif; ?>
					<?php if ( ! empty( $license_data['expires'] ) ) : ?>
						<li><strong><?php echo esc_html__( 'Expiration Date:', 'masterstudy' ); ?></strong> <?php echo esc_html( date( 'd.m.Y', strtotime( $license_data['expires'] ) ) ); ?></li>
					<?php endif; ?>
					</ul>
					<a href="#" class="reset-pro-plus-plugin"><?php echo esc_html__( 'Move back to PRO version', 'masterstudy' ); ?></a>
				</div>
				<div class="license-upgrade-video">
					<div class="license-upgrade-video__frame"><a href="https://www.youtube.com/watch?v=TBGOvaIWB4o" target="_blank"><span class="lng_centerplay"></span></a><img src="<?php echo esc_url( STM_TEMPLATE_URI . '/assets/admin/images/video-frame.png' ); ?>" width="160" height="90" alt="MasterStudy video frame"></div>
					<div class="license-upgrade-video__info">
						<span class="quick-guide"><?php echo esc_html__( 'Quick Guide', 'masterstudy' ); ?></span>
						<p><?php echo esc_html__( 'How to upgrade to MasterStudy PRO Plus', 'masterstudy' ); ?></p>
					</div>
				</div>
			</div>
			<div class="license-upgrade-offer">
				<div class="badge"><?php echo esc_html__( '50% OFF FIRST YEAR', 'masterstudy' ); ?></div>
				<div class="label"><?php echo esc_html__( 'Upgrade to', 'masterstudy' ); ?></div>
				<h3>MasterStudy <strong class="highlight"><span>PRO PLUS</span></strong></h3>
				<div class="license-upgrade-price">
					<span class="old-price">$99</span>
					<span class="new-price">$49.50</span>
					<span class="separator"></span>
					<span class="per-year"><?php echo esc_html__( 'per year', 'masterstudy' ); ?></span>
				</div>
				<ul class="upgrade-features">
					<li><span class="lng_centerglobus"></span> <?php echo esc_html__( '1 Site License', 'masterstudy' ); ?></li>
					<li><span class="lng_centerpuzzle1"></span> <?php echo esc_html__( '30+ Premium Features', 'masterstudy' ); ?></li>
					<li><span class="lng_centerupdates"></span> <?php echo esc_html__( 'Updates for 1 year', 'masterstudy' ); ?></li>
					<li><span class="lng_centersupport"></span> <?php echo esc_html__( 'Priority Ticket Support', 'masterstudy' ); ?></li>
				</ul>
				<a href="https://stylemixthemes.com/masterstudy/upgrade-to-pro-plus/?utm_source=wpadmin&utm_medium=push&utm_campaign=get-ms-pro-10-discount+plugin_coupon%3Dupgrade50&utm_content=gopro&utm_term=trial/#pricing-plans" class="theme-update-button" target="_blank"><?php echo esc_html__( 'Upgrade Now', 'masterstudy' ); ?></a>
			</div>
		</div>
		<div id="step-pro-features" style="display: none;">
			<h2 class="masterstudy-wizard-title"><?php echo esc_html__( 'Are you sure you want to roll back?', 'masterstudy' ); ?></h2>
			<p class="masterstudy-wizard-description"><?php echo esc_html__( 'By continuing you will lose access to the more advanced PRO Plus version', 'masterstudy' ); ?></p>
			<div class="feature-warning">
				<strong><?php echo esc_html__( 'The following features will no longer be available:', 'masterstudy' ); ?></strong>
				<ul>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'AI Course Creation', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Upcoming Courses', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Question Media', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Grades', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Reports & Analytics', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Email Branding, Email Events', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( '+10 New Single Course Styles', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Video Preview for Single Course Page', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Required Video Progress', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Instructor Sales Details', 'masterstudy' ); ?></li>
					<li><span class="stmadmin-icon-cross"></span> <?php echo esc_html__( 'Social Login', 'masterstudy' ); ?></li>
				</ul>
			</div>
			<div class="rollback-actions">
				<a href="#" class="reset-pro-plus-plugin theme-update-button"><?php echo esc_html__( 'Yes, back to previous version', 'masterstudy' ); ?></a>
				<a href="#" class="reset-pro-plus-plugin-cancel theme-update-button"><?php echo esc_html__( 'Cancel', 'masterstudy' ); ?></a>
			</div>
		</div>
		<div id="step-roll-back" style="display: none;">
			<h2 class="masterstudy-wizard-title"><?php echo esc_html__( 'Rolling Back', 'masterstudy' ); ?></h2>
			<p class="masterstudy-wizard-description"><?php echo esc_html__( 'Going back to the previous version. You’ll be up and running shortly.', 'masterstudy' ); ?></p>
			<div class="progress-bar">
				<div class="progress-bar-fill"></div>
			</div>
			<p class="progress-text"><?php echo esc_html__( 'Progress:', 'masterstudy' ); ?> <span id="progress-value">0</span>%</p>
		</div>
	</div>
	<?php
}
