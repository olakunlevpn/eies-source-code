<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\Helpers;

class BuddyPress
{
    const SLUG = 'buddypress';

	private static $instance = null;

	public function addCheckbox(){
		echo '<input id="stm_gdpr" class="stm_gdpr" type="checkbox" name="stm_gdpr" required />
		<label for="stm_gdpr">
			' . Helpers::checkboxText(self::SLUG) . '
		</label>';
	}

    public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}