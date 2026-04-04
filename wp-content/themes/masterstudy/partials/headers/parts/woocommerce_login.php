<?php if ( defined( 'STM_LMS_URL' ) ) : ?>
	<div class="pull-right">
		<div class="header_login_url">
			<?php
			if ( is_user_logged_in() ) :
				$current_user = STM_LMS_User::get_current_user();
				?>

				<a href="<?php echo esc_url( STM_LMS_User::user_page_url( $current_user['id'] ) ); ?>">
					<i class="fa fa-user"></i><?php echo esc_attr( $current_user['login'] ); ?>
				</a>
				<span class="vertical_divider"></span>

				<a class="logout-link" href="<?php echo esc_url( wp_logout_url( get_home_url() ) ); ?>"
				title="<?php esc_attr_e( 'Log out', 'masterstudy' ); ?>">
					<?php echo esc_html__( 'Log out', 'masterstudy' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( STM_LMS_User::login_page_url() ); ?>">
					<i class="fa fa-user"></i><?php echo esc_html__( 'Login', 'masterstudy' ); ?>
				</a>
				<?php if ( ! STM_LMS_Options::get_option( 'restrict_registration', false ) ) : ?>
					<span class="vertical_divider"></span>
					<a href="<?php echo esc_url( add_query_arg( 'mode', 'register', STM_LMS_User::login_page_url() ) ); ?>"><?php echo esc_html__( 'Register', 'masterstudy' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
<?php elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) ) : ?>
	<div class="pull-right">
		<div class="header_login_url">
			<?php
			if ( is_user_logged_in() ) :
				$current_user = wp_get_current_user();
				if ( ! empty( $current_user->user_login ) ) :
					?>
					<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
						<i class="fa fa-user"></i><?php echo esc_attr( $current_user->user_login ); ?>
					</a>
					<span class="vertical_divider"></span>
				<?php endif; ?>
				<a class="logout-link" href="<?php echo esc_url( wp_logout_url( get_home_url() ) ); ?>"
				title="<?php esc_attr_e( 'Log out', 'masterstudy' ); ?>">
					<?php esc_html__( 'Log out', 'masterstudy' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
					<i class="fa fa-user"></i><?php echo esc_html__( 'Login', 'masterstudy' ); ?>
				</a>
				<span class="vertical_divider"></span>
				<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>"><?php echo esc_html__( 'Register', 'masterstudy' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
