<?php
namespace STM_GDPR\includes;

use STM_GDPR\includes\Helpers;

class Cookie
{
	const cookieName = 'stm_gdpr_cookie';
	
	private static $instance = null;
	
	private $userID;

	private function __construct() {
		$this->userID = get_current_user_id();
	}

    public function displayPopup() {

    	$settings = Helpers::cmb_get_option(STM_GDPR_PREFIX . 'general');
    	$privacy = Helpers::cmb_get_option(STM_GDPR_PREFIX . 'privacy');
    	$privacy_link = (!empty($privacy['privacy_page'])) ? get_page_link($privacy['privacy_page']) : '#';
    	$button_text = (!empty($settings['button_text'])) ? $settings['button_text'] : __('Ok, I agree', 'stm_gdpr_compliance');

		$popup = '<div id="stm_gdpr_popup-main" class="stm_gdpr_popup-main" style="background-color: ' . $settings['popup_bg_color'] . '; color: ' . $settings['popup_text_color'] . ';
		' . str_replace( '_', ': 20px; ', esc_attr( $settings['popup_position'] ) ) . '">
			<div class="stm_gdpr_popup-content">' . $settings['popup_content'] . '</div>
			<div class="stm_gdpr_popup-links">
				<a href="#" id="stm_gdpr_popup_accept" class="stm_gdpr_popup-accept">' . $button_text . '</a>
				<a href="' . $privacy_link . '" class="stm_gdpr_popup-privacy">' . $privacy['link_text'] . '</a>
			</div>
		</div>';

    	echo $popup;
	}
	
	public function cookieAccept() {
		
		$expire_time   = Helpers::cmb_get_option(STM_GDPR_PREFIX . 'general', 'expire_time');

		setcookie(self::cookieName, md5(time()), time() + $expire_time, '/');

		if ( $this->userID ) {
			update_user_meta($this->userID, self::cookieName, time() + $expire_time);
		}

	}
	
	public function isAccepted() {
		
		if ( $this->userID ) {
			$isAcceptedUser = get_user_meta($this->userID, self::cookieName, true);
			if ( !empty($isAcceptedUser) && is_numeric($isAcceptedUser) && $isAcceptedUser > time() ) {
				return true;
			}
		}

		if ( !empty($_COOKIE[self::cookieName]) ) {
			return true;
		}

		return false;

	}

	public function block_cookies() {

		if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' )) || (!empty($_POST['action']) && $_POST['action'] == 'stm_gdpr_cookie_accept') ) {
			return;
		}

		@header_remove( 'Set-Cookie' );

		if ( isset( $_COOKIE ) ) {

			$time = time() - 100;
			foreach ( array_keys( $_COOKIE ) as $cookie_name ) {
				if ( $cookie_name && $cookie_name != self::cookieName) {
					@setcookie( $cookie_name, '', $time, '/' );
				}
			}

		}

	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}