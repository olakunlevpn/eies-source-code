<?php

/*
 Plugin Name: GDPR Compliance & Cookie Consent
 Plugin URI:  https://stylemixthemes.com/
 Description: The GDPR (General Data Protection Regulation) is a set of instructions for companies that collect and process EU user data on the Internet. The new regulation is aimed at improving the level of protection and giving EU residents wide control over their data.
 Version:     1.1
 Author:      StylemixThemes
 Author URI:  https://stylemixthemes.com/
 License:     GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 Text Domain: stm_gdpr_compliance
 Domain Path: /languages
*/

namespace STM_GDPR;

use STM_GDPR\includes\Cookie;
use STM_GDPR\includes\DataAccess;
use STM_GDPR\includes\PluginOptions;
use STM_GDPR\includes\Helpers;
use STM_GDPR\includes\Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('STM_GDPR_SLUG', 'stm_gdpr_compliance');
define('STM_GDPR_PREFIX', 'stmgdpr_');
define('STM_GDPR_ROOT_FILE', __FILE__);
define('STM_GDPR_PATH', dirname( __FILE__ ));
define('STM_GDPR_URL', plugins_url( '', __FILE__ ));

spl_autoload_register(__NAMESPACE__ . '\\stm_autoload');
add_action('plugins_loaded', array(STM_GDPR::getInstance(), 'init'));

class STM_GDPR {

	private static $instance = null;

	public function init() {

		load_plugin_textdomain(STM_GDPR_SLUG, false, basename(dirname(__FILE__)) . '/languages/');

		if (is_admin()) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			add_action('cmb2_admin_init', array(PluginOptions::getInstance(), 'generateOptionsPage'));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'pluginSettingsLink'));
			add_action('admin_enqueue_scripts', array(Helpers::getInstance(), 'stm_enqueue_admin_scripts'));
		}

		if (Helpers::isEnabled(STM_GDPR_PREFIX . 'general', 'popup') && !Cookie::getInstance()->isAccepted()) {
			if(!is_admin() && Helpers::cmb_get_option( STM_GDPR_PREFIX . 'general', 'block_cookies')) {
				add_action('template_redirect', array(Cookie::getInstance(), 'block_cookies' ), 0);
				add_action('shutdown', array(Cookie::getInstance(), 'block_cookies' ), 0);
			}
			add_action('wp_footer', array(Cookie::getInstance(), 'displayPopup'));
			add_action('wp_ajax_stm_gdpr_cookie_accept', array(Cookie::getInstance(), 'cookieAccept'));
			add_action('wp_ajax_nopriv_stm_gdpr_cookie_accept', array(Cookie::getInstance(), 'cookieAccept'));
		}

		/* Enqueue front scripts and Ajax requests */
		add_action('wp_enqueue_scripts', array(Helpers::getInstance(), 'stm_enqueue_scripts'));
		add_action('wp_ajax_stm_gpdr_data_request', array(DataAccess::getInstance(), 'stm_gpdr_data_request'));
		add_action('wp_ajax_nopriv_stm_gpdr_data_request', array(DataAccess::getInstance(), 'stm_gpdr_data_request'));
		
		/* GDPR Shortcode and Widget */
		add_shortcode('stm-gpdr-data-access', array(DataAccess::getInstance(), 'stm_gdpr_shortcode'));
		require_once(STM_GDPR_PATH . '/includes/DataAccessWidget.php');

		/* Integrated plugins */
		Plugins::getInstance();

	}

	public function pluginSettingsLink($links = array()) {

		$actionLinks = array(
			'settings' => '<a href="' . add_query_arg(array('page' => STM_GDPR_SLUG), admin_url('admin.php')) . '" aria-label="' . esc_attr__('STM GDPR settings', STM_GDPR_SLUG) . '">' . esc_html__('Settings', 'stm_gdpr_compliance') . '</a>',
		);

		return array_merge($actionLinks, $links);
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}

function stm_autoload($class = '') {

    if(strpos($class, 'STM_GDPR') !== 0) {
		return;
	}

	$return = str_replace('STM_GDPR\\', '', $class);
	$return = str_replace('\\', '/', $return);

	require $return . '.php';
}