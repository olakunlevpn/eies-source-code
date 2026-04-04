<section class="upgrade-offer">
	<div class="upgrade-offer__inner">
		<div class="upgrade-offer__content">
			<h1 class="upgrade-offer__title">
				Move to the <span>new<br>generation</span><br> of MasterStudy
			</h1>
			<div class="upgrade-offer__badge">A WIN-WIN PROPOSITION</div>
			<div class="upgrade-offer__badge upgrade-offer__badge-solid">NO CREDIT CARD REQUIRED</div>
			<div>
				<a href="https://stylemixthemes.com/wordpress-lms-plugin/" class="theme-update-button light" target="_blank">Explore</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=stm-admin-upgrade-pro-plus' ) ); ?>" class="theme-update-button secondary">UPGRADE FOR FREE</a>
			</div>
		</div>
		<div class="upgrade-offer__card">
			<img src="<?php echo esc_url( STM_TEMPLATE_URI . '/assets/admin/images/for-free.png' ); ?>"  class="upgrade-offer__card-for-free" width="517" height="377" alt="MasterStudy logo">
			<img src="<?php echo esc_url( STM_TEMPLATE_URI . '/assets/admin/images/arrow.svg' ); ?>"  class="upgrade-offer__card-arrow" width="116" height="125" alt="MasterStudy logo">
		</div>
	</div>
</section>
<section class="video-section">
	<iframe src="https://www.youtube.com/embed/TBGOvaIWB4o" title="YouTube video" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
</section>
<?php
$feature_blocks = array(
	array(
		'before_title'       => 'Manual Course Building',
		'before_description' => '
			<p>Building a course from scratch means hours of manual work. You have to brainstorm a course structure, write lessons one by one, create quizzes, and search for visuals to support your content.</p>
			<p>Switching between different tools and struggling to maintain consistency can slow down the process. Even with templates, customization is limited, and every piece of content has to be written and reviewed manually.</p>
		',
		'after_title'        => 'AI-Powered Course Creation',
		'after_description'  => 'Generate full courses — including structure, lessons, quizzes, and images — in just a few clicks.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-1.png',
		'live_demo'          => 'https://stylemixthemes.com/wordpress-lms-plugin/addons/ai-lab/',
	),
	array(
		'before_title'       => 'Hard-Coded Templates',
		'before_description' => '
			<p>LMS page templates are rigid and lack flexibility. Most of course pages look identical, with layouts locked into a similar style and no room for creativity.</p>
			<p>There’s no way to add or remove sections like testimonials, pricing tables, or brand-specific elements. Layouts can’t be adjusted, and design personalization is limited.</p>
		',
		'after_title'        => 'Visual Page Builder Power',
		'after_description'  => 'Create fully customizable LMS pages with 50+ Elementor widgets designed to showcase your course, boost conversions, and match your brand perfectly.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-13.png',
		'live_demo'          => 'https://stylemixthemes.com/wordpress-lms-plugin/course-style/',
	),
	array(
		'before_title'       => 'Limited Course Layouts',
		'before_description' => '
			<p>The theme offers only three single course styles, with few customization options. Instructors have to choose from predefined templates that often don’t match their course’s personality or marketing needs.</p>
			<p>Many pages look similar, and it is challenging to differentiate premium courses. The design flexibility for individual courses is limited.</p>
		',
		'after_title'        => '10+ Unique Layout Options',
		'after_description'  => 'Take full control of how your course page looks with seamless Elementor integration - build from scratch or use brand new fully customizable templates and 50+ dedicated widgets.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-8.png',
		'live_demo'          => 'https://stylemixthemes.com/wordpress-lms-plugin/course-style/',
	),
	array(
		'before_title'       => 'Static Emails',
		'before_description' => '
			<p>All system-generated emails have a basic, non-customizable look. They lack branding elements like logos, color schemes, and personalized messaging.</p>
			<p>Instructors and course creators can’t control the tone or design of their communication. This misses a chance to reinforce brand identity and connect with learners through a consistent experience.</p>
		',
		'after_title'        => 'Branded Emails with Smart Triggers',
		'after_description'  => 'Fully customize your emails: from new enrollments to course completions - control how each message looks and set events.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-7.png',
		'live_demo'          => 'https://stylemixthemes.com/wordpress-lms-plugin/email-management/',
	),
	array(
		'before_title'       => 'Basic Gradebook',
		'before_description' => '
			<p>The grading system is limited, showing only basic Passed only status or score tracking. Instructors can’t customize grading criteria, set detailed scoring rubrics, or handle complex grading workflows. It is harder to provide meaningful feedback or track nuanced student progress.</p>
			<p>Multi-component grading like assignments, quizzes, and participation are limited to basic progress bars. This makes it challenging to manage grading for diverse course formats.</p>
		',
		'after_title'        => 'Customizable Grading System',
		'after_description'  => 'Build fully customized grading scales, assign weight to different course elements, use different formats of scores and showcase statuses.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-5.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin/?demo_login=https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/?tab=grades',
	),
	array(
		'before_title'       => 'No Analytics',
		'before_description' => '
			<p>There is no built-in way to analyze student activity, engagement, or course effectiveness. Instructors have to rely on basic progress tracking or external tools.</p>
			<p>Important metrics like quiz scores, completion rates, or course popularity aren’t visualized. This makes it difficult to make data-driven decisions, improve courses, or support struggling students effectively.</p>
		',
		'after_title'        => 'In-Depth Reporting Tools',
		'after_description'  => 'Access advanced reports and analytics to track student performance, course completion, and engagement trends.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-6.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin/?demo_login=https://masterstudy.stylemixthemes.com/lms-plugin/user-account/analytics/',
	),
	array(
		'before_title'       => 'Instant Access Only',
		'before_description' => '
			<p>Learners can only see courses available for immediate enrollment. There is no built-in way to display courses that will launch in the future.</p>
			<p>Instructors can’t promote upcoming courses whatsoever, often losing potential students who would have been interested in pre-registration. It is impossible to create anticipation or waitlists directly inside the LMS.</p>
		',
		'after_title'        => 'Upcoming Course Visibility',
		'after_description'  => 'Learners can browse and track upcoming courses, allowing you to market in advance and grow anticipation.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-3.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/real-things-art-painting-by-jason-ni/',
	),
	array(
		'before_title'       => 'Standard Questions',
		'before_description' => '
			<p>Quizzes are mostly text-based, offering limited ways to engage students visually or interactively. There is no option to add video or audio to quiz questions, which makes assessments feel static.</p>
			<p>This approach does not cater to different learning styles or provide real-world scenarios that require multimedia context. It limits creativity and depth in student evaluation.</p>
		',
		'after_title'        => 'Media-rich Questions',
		'after_description'  => 'Create interactive and engaging quizzes with video, audio, and images.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-4.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin?generate_demo_user=https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/5057/',
	),
	array(
		'before_title'       => 'No Video Progress',
		'before_description' => '
			<p>Students can mark lessons as completed without actually watching video content. This leads to inaccurate progress reports and reduced accountability.</p>
			<p>Instructors can’t ensure that learners have engaged with the core video material before advancing.</p>
		',
		'after_title'        => 'Required Video Progress',
		'after_description'  => 'Enable required video viewing to ensure students watch lessons before they can mark them as complete.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-10.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin?generate_demo_user=https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/5028',
	),
	array(
		'before_title'       => 'Basic Instructor Profiles',
		'before_description' => '
			<p>Instructor profiles are mainly limited to showcasing bios and social links. There is no detailed sales or revenue tracking available at the instructor level.</p>
			<p>Instructors can’t easily monitor their own earnings, course sales, or student enrollments in one place. This makes financial performance less transparent and harder to manage.</p>
		',
		'after_title'        => 'Instructor Sales Details',
		'after_description'  => 'Access dedicated dashboards with detailed sales, revenue, and enrollment statistics.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-11.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin?demo_login=https://masterstudy.stylemixthemes.com/lms-plugin/user-account/sales/',
	),
	array(
		'before_title'       => 'Image-Only Preview',
		'before_description' => '
			<p>Course landing pages can only display a static image as a visual preview. There is no option to showcase a teaser or introduction video directly.</p>
			<p>This makes it harder to capture attention, demonstrate course quality, or build student trust quickly. Static visuals don’t always reflect the course’s true value.</p>
		',
		'after_title'        => 'Engaging Video Preview',
		'after_description'  => 'Upload video previews to give students a dynamic first impression and boost conversions.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-9.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/how-to-be-a-dj-make-electronic-music/',
	),
	array(
		'before_title'       => 'Video-Only Learning',
		'before_description' => '
			<p>Courses are limited to video lessons, offering no flexibility for students who prefer audio-based learning. Learners have to stay in front of a screen, which makes it harder for those on the go. Instructors can’t easily offer podcasts, summaries, or audio practice.</p>
			<p>The learning experience is fully visual, with no option to switch formats for different learning styles. This often limits course accessibility and convenience.</p>
		',
		'after_title'        => 'Audio-Enabled Courses',
		'after_description'  => 'Add audio lessons to your courses, providing a flexible, screen-free learning experience.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-2.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin?generate_demo_user=https://masterstudy.stylemixthemes.com/lms-plugin/courses-page/basics-of-masterstudy/53383/',
	),
	array(
		'before_title'       => 'Email-Only Login',
		'before_description' => '
			<p>Students can only register and sign in using their email addresses and passwords. This creates extra steps, increasing the chance of abandoned sign-ups or forgotten passwords.</p>
			<p>There is no option to simplify access using existing social media accounts.</p>
		',
		'after_title'        => 'Quick Social Logins',
		'after_description'  => 'Enable instant sign-in using social media accounts, making registration and access much faster.',
		'image'              => STM_TEMPLATE_URI . '/assets/admin/images/pro-plus/feature-12.png',
		'live_demo'          => 'https://masterstudy.stylemixthemes.com/lms-plugin/user-account/',
	),
);
?>
<section class="features-section">
	<h2 class="features-title">Go Beyond The Basics: What Pro Plus Unlocks For You</h2>
	<?php foreach ( $feature_blocks as $block ) : ?>
	<div class="feature-card-wrap">
		<div class="feature-card">
			<div class="feature-before">
				<div class="feature-title-box">
					<div>
						<div class="feature-label">Before</div>
						<div class="feature-title"><?php echo esc_html( $block['before_title'] ); ?></div>
					</div>
				</div>
				<div class="feature-description"><?php echo wp_kses_post( $block['before_description'] ); ?></div>
			</div>
			<div class="feature-after">
				<div class="feature-title-box">
					<div>
						<div class="feature-label">After</div>
						<div class="feature-title"><?php echo esc_html( $block['after_title'] ); ?></div>
					</div>
					<a href="<?php echo esc_url( $block['live_demo'] ); ?>" class="theme-update-button" target="_blank">Explore</a>
				</div>
				<div class="feature-description"><?php echo esc_html( $block['after_description'] ); ?></div>
				<div class="feature-image">
					<img src="<?php echo esc_url( $block['image'] ); ?>" width="601" height="300" alt="<?php echo esc_attr( $block['after_title'] ); ?>">
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
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
	array(
		'question' => 'Do I get a discount for the Pro Plus as a MasterStudy Theme user?',
		'answer'   => '
			<p>Yes. If you’ve purchased the MasterStudy Theme, you’re eligible for an exclusive 50% discount on the first year of any Pro Plus annual plan — whether it’s for 1 site, 10 sites, or unlimited sites.</p>
			<p>If you prefer a one-time payment, you can also get 30% off the lifetime license. This offer is only available to verified theme users.</p>
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
