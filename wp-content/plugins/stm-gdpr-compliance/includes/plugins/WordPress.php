<?php
namespace STM_GDPR\includes\plugins;

use STM_GDPR\includes\Helpers;

class WordPress
{
    const SLUG = 'wordpress';

    private static $instance = null;

	public function addCheckbox( $submit = '' ) {

		$checkbox = apply_filters(
			STM_GDPR_PREFIX . 'wordpress_checkbox',
			'<p class="' . STM_GDPR_SLUG . '-checkbox"><label><input type="checkbox" name="' . STM_GDPR_SLUG . '" id="' . STM_GDPR_SLUG . '" value="1" />' . Helpers::checkboxText(self::SLUG) . ' <abbr class="required" title="' . esc_attr__('required', STM_GDPR_SLUG) . '">*</abbr></label></p>',
			$submit
		);

		return $checkbox . $submit;
	}

	public function displayError() {

		if (!isset($_POST[STM_GDPR_SLUG])) {

			wp_die(
				'<p>' . sprintf(
					__('<strong>ERROR</strong>: %s', STM_GDPR_SLUG),
					Helpers::errorMessage(self::SLUG)
				) . '</p>',
				__('Comment Submission Failure'),
				array('back_link' => true)
			);

		}

	}

	public function addCommentMeta($commentId = 0) {

		if (isset($_POST[STM_GDPR_SLUG]) && !empty($commentId)) {

			add_comment_meta($commentId, STM_GDPR_SLUG, time());

		}

	}

	public function displayMetaColumn($columns = array()) {

		$columns[STM_GDPR_SLUG] = __('GDPR Accepted On', STM_GDPR_SLUG);

		return $columns;
	}

	public function displayCommentOverview($column = '', $commentId = 0) {

		if ($column === STM_GDPR_SLUG) {

			$date = get_comment_meta($commentId, STM_GDPR_SLUG, true);
			$value = (!empty($date)) ? Helpers::localDate(get_option('date_format') . ' ' . get_option('time_format'), $date) : __('Not accepted.', STM_GDPR_SLUG);
			echo $value;

		}

		return $column;
	}

    public static function getInstance() {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}