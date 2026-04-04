<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\Helpers;

class MailChimp
{
    const SLUG = 'mailchimp';

    private static $instance = null;

	public function addCheckbox( $content, $form, $element ) {

    	$content .= '<div class="stm_gdpr_checker"><input id="stm_gdpr" class="stm_gdpr" type="checkbox" name="stm_gdpr" required />
			<label for="stm_gdpr">
				' . Helpers::checkboxText(self::SLUG) . '
			</label></div>';

		return $content;
	}

	public function displayError( $errors, $form ) {

		if ( empty( $_POST['stm_gdpr'] ) ) {
			$errors[] = Helpers::errorMessage(self::SLUG);
		}

		return $errors;
	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}