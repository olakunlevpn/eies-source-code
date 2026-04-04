<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\Helpers;

class WooCommerce
{
    const SLUG = 'woocommerce';

    private static $instance = null;

	public function displayCheckbox() {

		$args = array(
			'type' => 'checkbox',
			'class' => array('stmgdpr-checkbox'),
			'label' => Helpers::checkboxText(self::SLUG),
			'required' => true,
		);
		woocommerce_form_field('stmgdpr', $args);

	}

	public function displayError() {

		if (!isset($_POST['stmgdpr'])) {
			wc_add_notice(Helpers::errorMessage(self::SLUG), 'error');
		}

	}

	public function updateOrderMeta($orderID = 0) {

		if (isset($_POST['stmgdpr']) && !empty($orderID)) {
			update_post_meta($orderID, '_stmgdpr', time());
		}

	}

	public function displayOrderData(\WC_Order $order) {

		$label = __('GDPR accepted on:', 'stm_gdpr_compliance');
		$date = get_post_meta($order->get_id(), '_stmgdpr', true);
		$value = (!empty($date)) ? Helpers::localDate(get_option('date_format') . ' ' . get_option('time_format'), $date) : __('Not accepted.', 'stm_gdpr_compliance');

		echo sprintf('<p class="form-field form-field-wide stm-gdpr-date"><strong>%s</strong><br />%s</p>', $label, $value);
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
