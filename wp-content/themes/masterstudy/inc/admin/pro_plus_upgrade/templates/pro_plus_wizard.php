<section class="masterstudy-wizard-section">
	<div id="step-form">
		<h2 class="masterstudy-wizard-title"><?php echo esc_html__( 'Unlock Your Free License', 'masterstudy' ); ?></h2>
		<p class="masterstudy-wizard-description"><?php echo esc_html__( 'Get the latest features and improvements. Ready to enhance your LMS?', 'masterstudy' ); ?></p>
		<form id="masterstudy-wizard-form" class="masterstudy-wizard-form">
			<label for="upgrade-email"><?php echo esc_html__( 'Email', 'masterstudy' ); ?></label>
			<input type="email" id="upgrade-email" name="email" placeholder="Enter your email" required>
			<label class="checkbox-container">
				<input type="checkbox" name="agree" required>
				<?php
				printf(
					wp_kses(
						/* translators: %s: Privacy Policy URL */
						__( 'By proceeding, you agree to our &nbsp; <a href="%s" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.', 'masterstudy' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
								'rel'    => array(),
							),
						)
					),
					esc_url( 'https://stylemixthemes.com/privacy-policy/' )
				);
				?>
			</label>
			<button type="submit" class="theme-update-button"><?php echo esc_html__( 'Upgrade Now', 'masterstudy' ); ?></button>
			<button type="button" class="theme-update-button theme-update-button-cancel" onclick="window.history.back();"><?php echo esc_html__( 'Cancel', 'masterstudy' ); ?></button>
		</form>
		<div class="masterstudy-upgrade-message" style="margin-top: 16px;"></div>
	</div>
	<div id="step-loading" style="display: none;">
		<h2 class="masterstudy-wizard-title"><?php echo esc_html__( 'Installing Plugins', 'masterstudy' ); ?></h2>
		<p class="masterstudy-wizard-description"><?php echo esc_html__( 'Powering up your site – plugins loading!', 'masterstudy' ); ?></p>
		<div class="progress-bar">
			<div class="progress-bar-fill"></div>
		</div>
		<p class="progress-text"><?php echo esc_html__( 'Progress:', 'masterstudy' ); ?> <span id="progress-value">0</span>%</p>
	</div>
</section>
<?php
$faq_block = array(
	array(
		'question' => 'What do I get by upgrading to MasterStudy Pro Plus?',
		'answer'   => '
			<p>By upgrading to MasterStudy Pro Plus, you unlock the most advanced features for course creation, management, and student engagement.</p>
			<p>Pro Plus combines all previous MasterStudy Pro add-ons and introduces even more powerful tools like the new <a href="https://stylemixthemes.com/wordpress-lms-plugin/addons/certificates/" target="_blank">Certificate Builder</a>, <a href="https://stylemixthemes.com/wordpress-lms-plugin/addons/ai-lab/" target="_blank">AI Lab</a>, <a href="https://stylemixthemes.com/wordpress-lms-plugin/addons/grades/" target="_blank">Advanced Grading</a>, <a href="https://stylemixthemes.com/wordpress-lms-plugin/addons/email-manager/" target="_blank">Branded Emails</a>, <a href="https://stylemixthemes.com/wordpress-lms-plugin/reports-and-analytics/" target="_blank">Advanced reports and Analytics</a>, flexible, marketing-ready <a href="https://stylemixthemes.com/wordpress-lms-plugin/course-style/" target="_blank">course page</a> design and more.</p>
			<p>It\'s a complete solution that gives you more freedom, customization, and control — without needing additional coding or complex setups.</p>
		',
	),
	array(
		'question' => 'How is MasterStudy Theme different from MasterStudy Pro Plus?',
		'answer'   => '
			<p>The MasterStudy Theme was the classic setup where features were activated through the theme\'s Pro plugin and additional manual installations. In contrast, <a href="https://masterstudy.stylemixthemes.com/lms-plugin/" target="_blank">MasterStudy Pro Plus</a> is a unified, all-in-one plugin that works seamlessly with any theme, including MasterStudy.</p>
			<p>It simplifies the structure, combines all previous add-ons, removes unnecessary dependencies, and introduces <a href="https://stylemixthemes.com/wordpress-lms-plugin/addons/" target="_blank">an evolved feature set</a> with faster updates and better flexibility. No more switching between multiple tools — it\'s all integrated in one place now.</p>
		',
	),
	array(
		'question' => 'What happens to my existing content when I upgrade?',
		'answer'   => '
			<p>No need to worry — all your existing content remains safe and untouched. When you upgrade to MasterStudy Pro Plus, your current courses, lessons, quizzes, and user data stay exactly as they are.</p>
			<p>The Pro Plus features simply become available, giving you the option to enhance your existing courses or create new ones with improved tools like AI Lab, new course styles, advanced grading, upcoming courses, detailed analytics and more.</p>
			<p>You can start using the new features right away without losing anything you\'ve already built.</p>
		',
	),
	array(
		'question' => 'What happens after my license expires?',
		'answer'   => '
			<p>If your MasterStudy Pro Plus license expires, your content will remain fully intact and accessible.</p>
			<p>Nothing is deleted or lost. You simply return to the feature set of the MasterStudy theme —meaning you can continue using your courses and existing setup, but advanced Pro Plus features (like AI Lab, advanced grading, reports and analytics etc.) will no longer be available until you renew your license.</p>
			<p>Your work, progress, and student data stay exactly where you left them.</p>
		',
	),
);
?>
<section class="faq-section">
	<h2 class="faq-title">Frequently Asked Questions</h2>
	<div class="faq-list">
		<?php foreach ( $faq_block as $block ) : ?>
		<div class="faq-item">
			<button class="faq-question">
				<?php echo esc_html( $block['question'] ); ?> <span class="lng_centerarrow faq-arrow"></span>
			</button>
			<div class="faq-answer"><?php echo wp_kses_post( $block['answer'] ); ?></div>
		</div>
		<?php endforeach; ?>
	</div>
</section>
