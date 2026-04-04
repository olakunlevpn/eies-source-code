<?php
stm_module_styles( 'header_mobile', 'account' );

if ( function_exists( 'stm_lms_register_style' ) ) {
	wp_enqueue_script( 'vue.js' );
	wp_enqueue_script( 'vue-resource.js' );
	stm_lms_register_style( 'enterprise' );

}

$user_account_icon = stm_option( 'header_user_account_switch', true );
$label             = class_exists( 'STM_LMS_Options' ) && STM_LMS_Options::get_option( 'restrict_registration', false )
	? esc_html__( 'Login', 'masterstudy' )
	: esc_html__( 'Login/Sign Up', 'masterstudy' );

if ( function_exists( 'stm_lms_register_style' ) ) {
	stm_lms_register_style( 'become_instructor' );
}

if ( class_exists( 'STM_LMS_User' ) ) :
	$messages = 0;
	?>

	<div class="stm_lms_account_popup">
		<div class="stm_lms_account_popup__close">
			<i class="stmlms-cross"></i>
		</div>
		<div class="inner">
			<?php
			if ( is_user_logged_in() ) :
				$user     = STM_LMS_User::get_current_user();
				$messages = STM_LMS_Chat::user_new_messages( $user['id'] );
				?>
				<div class="stm_lms_account_popup__user">
					<?php
					if ( ! empty( $user_account_icon ) ) {
						echo wp_kses_post( html_entity_decode( $user['avatar'] ) );
					} else {
						?>
						<a href="<?php echo esc_url( STM_LMS_User::login_page_url() ); ?>" class="stm_lms_account_popup__login">
							<i class="stmlms-user sbc"></i>
						</a>
					<?php } ?>
					<div class="stm_lms_account_popup__user_info">
						<h4><?php echo esc_html( $user['login'] ); ?></h4>
						<a href="<?php echo esc_url( STM_LMS_User::user_page_url() ); ?>"
						target="_blank">
							<?php esc_html_e( 'My Profile', 'masterstudy' ); ?>
						</a>
					</div>
				</div>

			<?php else : ?>
				<a href="<?php echo esc_url( STM_LMS_User::login_page_url() ); ?>" class="stm_lms_account_popup__login">
					<i class="stmlms-user sbc"></i>
					<h3><?php echo esc_html( $label ); ?></h3>
				</a>
			<?php endif; ?>

			<?php $w = STM_LMS_User::get_wishlist( get_current_user_id() ); ?>

			<div class="stm_lms_account_popup__list heading_font">

				<a class="stm_lms_account_popup__list_single"
				href="<?php echo esc_url( STM_LMS_Course::courses_page_url() ); ?>">
					<?php esc_html_e( 'Courses', 'masterstudy' ); ?>
				</a>

				<?php if ( is_user_logged_in() ) : ?>
					<?php
					if ( STM_LMS_Cart::woocommerce_checkout_enabled() && function_exists( 'wc_get_checkout_url' ) ) {
						$checkout_url = wc_get_checkout_url();
					} else {
						$checkout_url = STM_LMS_Cart::checkout_url();
					}
					?>
					<a class="stm_lms_account_popup__list_single"
					href="<?php echo esc_url( $checkout_url ); ?>">
						<?php esc_html_e( 'Checkout', 'masterstudy' ); ?>
					</a>

					<a class="stm_lms_account_popup__list_single has_number"
					href="<?php echo esc_url( STM_LMS_Chat::chat_url() ); ?>">
						<?php esc_html_e( 'Messages', 'masterstudy' ); ?>
						<?php if ( ! empty( $messages ) ) : ?>
							<span class="sbc"><?php echo intval( $messages ); ?></span>
						<?php endif; ?>
					</a>

				<?php endif; ?>

				<a class="stm_lms_account_popup__list_single has_number"
				href="<?php echo esc_url( STM_LMS_User::wishlist_url() ); ?>">
					<?php esc_html_e( 'Favorites', 'masterstudy' ); ?>
					<span><?php echo intval( count( $w ) ); ?></span>
				</a>

				<?php if ( is_user_logged_in() ) : ?>
					<a class="stm_lms_account_popup__list_single"
					href="<?php echo esc_url( STM_LMS_User::user_page_url() . 'settings' ); ?>">
						<?php esc_html_e( 'Settings', 'masterstudy' ); ?>
					</a>

					<?php if ( stm_option( 'online_show_links', true ) ) : ?>
						<a class="stm_lms_account_popup__list_single"
						data-masterstudy-modal="masterstudy-enterprise-modal"
						href="#">
							<?php esc_html_e( 'For Enterprise', 'masterstudy' ); ?>
						</a>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ( is_user_logged_in() ) : ?>
					<a class="stm_lms_account_popup__list_single"
					href="<?php echo esc_url( wp_logout_url( get_home_url() ) ); ?>">
						<?php esc_html_e( 'Logout', 'masterstudy' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
