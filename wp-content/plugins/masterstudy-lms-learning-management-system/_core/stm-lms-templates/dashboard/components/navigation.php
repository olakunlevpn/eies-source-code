<transition name="slide">

	<div class="stm-lms-dashboard-navigation">
		<div class="stm-lms-dashboard-navigation--inner">
			<a href="<?php echo esc_url( get_dashboard_url() ); ?>" class="back_to_site">
				<i class="stmlms-arrow-left-2"></i>
				<?php esc_html_e( 'Back to Site', 'masterstudy-lms-learning-management-system' ); ?>
			</a>

			<div class="stm-lms-dashboard-navigation--links">

				<router-link to="/courses">
					<i class="stmlms-book-2-open"></i>
					<?php esc_html_e( 'Courses', 'masterstudy-lms-learning-management-system' ); ?>
				</router-link>

				<a href="">
					<i class="stmlms-user-2"></i>
					<?php esc_html_e( 'Students', 'masterstudy-lms-learning-management-system' ); ?>
					<span><?php esc_html_e( 'Soon', 'masterstudy-lms-learning-management-system' ); ?></span>
				</a>

			</div>

		</div>
	</div>

</transition>
