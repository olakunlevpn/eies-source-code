<?php
namespace STM_GDPR\includes;

use STM_GDPR\includes\plugins\BuddyPress;
use STM_GDPR\includes\plugins\ContactForm7;
use STM_GDPR\includes\plugins\GravityForms;
use STM_GDPR\includes\plugins\MailChimp;
use STM_GDPR\includes\plugins\WooCommerce;
use STM_GDPR\includes\plugins\WordPress;

class Plugins
{
    private static $instance = null;

    public function __construct() {

        foreach (Helpers::enabledPlugins() as $plugin) {

            switch ($plugin['slug']) {

                case WordPress::SLUG :

                    add_filter('comment_form_submit_field', array(WordPress::getInstance(), 'addCheckbox'), 999);
                    add_action('pre_comment_on_post', array(WordPress::getInstance(), 'displayError'));
                    add_action('comment_post', array(WordPress::getInstance(), 'addCommentMeta'));
                    add_filter('manage_edit-comments_columns', array(WordPress::getInstance(), 'displayMetaColumn'));
                    add_action('manage_comments_custom_column', array(WordPress::getInstance(), 'displayCommentOverview'), 10, 2);

                    break;

                case ContactForm7::SLUG :

                    add_action('wpcf7_init', array(ContactForm7::getInstance(), 'addFormTag'));
                    add_filter('wpcf7_before_send_mail', array(ContactForm7::getInstance(), 'addMailMsg'), 100);
                    add_filter('wpcf7_validate_stmgdpr', array(ContactForm7::getInstance(), 'validate'), 10, 2);

                    break;

				case WooCommerce::SLUG :

					add_action('woocommerce_review_order_before_submit', array(WooCommerce::getInstance(), 'displayCheckbox'), 100);
					add_action('woocommerce_checkout_process', array(WooCommerce::getInstance(), 'displayError'));
					add_action('woocommerce_checkout_update_order_meta', array(WooCommerce::getInstance(), 'updateOrderMeta'));
					add_action('woocommerce_admin_order_data_after_order_details', array(WooCommerce::getInstance(), 'displayOrderData'));

					break;

				case MailChimp::SLUG :

					add_filter('mc4wp_form_errors', array( MailChimp::getInstance(), 'displayError'), 10, 2 );
					add_filter('mc4wp_form_content', array( MailChimp::getInstance(), 'addCheckbox'), 10, 3 );

					break;

                case BuddyPress::SLUG :
                
                    add_action( 'bp_after_message_reply_box', array( BuddyPress::getInstance(), 'addCheckbox' ), 100 );
                    add_action( 'bp_after_messages_compose_content', array( BuddyPress::getInstance(), 'addCheckbox' ), 100 );
                    add_action( 'bp_activity_post_form_options', array( BuddyPress::getInstance(), 'addCheckbox' ), 100 );
                    add_action( 'bp_after_group_forum_post_new', array( BuddyPress::getInstance(), 'addCheckbox' ), 100 );
                    add_action( 'groups_forum_new_topic_after', array( BuddyPress::getInstance(), 'addCheckbox' ), 100 );
                    add_action( 'groups_forum_new_reply_after', array( BuddyPress::getInstance(), 'addCheckbox' ), 100 );

					break;

				case GravityForms::SLUG :

					add_filter('gform_entries_field_value', array(GravityForms::getInstance(), 'displayOverviewDate'), 10, 4);
                    add_filter('gform_get_field_value', array(GravityForms::getInstance(), 'displayDate'), 10, 2);
                    
                    foreach (GravityForms::getInstance()->getForms() as $form) {
                        add_filter('gform_entry_list_columns_' . $form['id'], array(GravityForms::getInstance(), 'displayOverviewDateColumn'), 10, 2);
                        add_filter('gform_save_field_value_' . $form['id'], array(GravityForms::getInstance(), 'addDate'), 10, 3);
                        add_action('gform_validation_' . $form['id'], array(GravityForms::getInstance(), 'validate'));
                    }

					break;
            }

        }

    }

    public static function supportedPlugins() {

        return array(
            array(
                'slug' => BuddyPress::SLUG,
                'file' => 'buddypress/bp-loader.php',
                'name' => __('BuddyPress', 'stm_gdpr_compliance'),
                'desc' => __('GDPR checkbox will be added automatically above the submit button. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'stm_gdpr_compliance'),
            ),
            array(
                'slug' => ContactForm7::SLUG,
                'file' => 'contact-form-7/wp-contact-form-7.php',
                'name' => __('Contact Form 7', 'stm_gdpr_compliance'),
				'desc' => __('GDPR checkbox will be added automatically to all your Contact Forms. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'stm_gdpr_compliance'),
            ),
            array(
                'slug' => GravityForms::SLUG,
                'file' => 'gravityforms/gravityforms.php',
                'name' => __('Gravity Forms', 'stm_gdpr_compliance'),
				'desc' => __('GDPR checkbox will be added automatically to all your Gravity Forms. HTML tags are NOT allowed due to plugin limitations.', 'stm_gdpr_compliance'),
            ),
            array(
                'slug' => MailChimp::SLUG,
                'file' => 'mailchimp-for-wp/mailchimp-for-wp.php',
                'name' => __('MailChimp', 'stm_gdpr_compliance'),
				'desc' => __('GDPR checkbox will be added automatically at the end of the MailChimp form. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'stm_gdpr_compliance'),
            ),
            array(
                'slug' => WooCommerce::SLUG,
                'file' => 'woocommerce/woocommerce.php',
                'name' => __('WooCommerce', 'stm_gdpr_compliance'),
				'desc' => __('GDPR checkbox will be added automatically at the end of the Checkout page. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'stm_gdpr_compliance'),
            ),
            array(
                'slug' => WordPress::SLUG,
				'file' => 'wordpress',
                'name' => __('WordPress Comments', 'stm_gdpr_compliance'),
				'desc' => __('GDPR checkbox will be added automatically above the submit button. You can use HTML tags and <span>%privacy_policy%</span> shortcode link for below inputs.', 'stm_gdpr_compliance'),
            )
        );
    }

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}