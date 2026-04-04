<?php
namespace STM_GDPR\includes;

use STM_GDPR\includes\plugins\ContactForm7;
use STM_GDPR\includes\plugins\GravityForms;

require_once STM_GDPR_PATH . '/includes/cmb2/init.php';

class PluginOptions
{
    private static $instance = null;

    public function generateOptionsPage() {

    	if (isset($_GET['settings-updated'])) {
			ContactForm7::getInstance()->updateForms();
			GravityForms::getInstance()->updateForms();
		}

		$pluginData = Helpers::stm_plugin_data();

		/**
		 * Registers options page menu item and form.
		 */
		$cmb_options = new_cmb2_box( array(
			'id'           => STM_GDPR_SLUG,
			'title'        => $pluginData['Name'],
			'menu_title'   => 'GDPR',
			'object_types' => array( 'options-page' ),
			'option_key'   => STM_GDPR_SLUG,
			'icon_url'     => 'dashicons-unlock',
			'capability'   => 'manage_options',
		) );

		/* General Tab */
		$general_group_id = $cmb_options->add_field( array(
			'id'           => STM_GDPR_PREFIX . 'general',
			'type'         => 'group',
			'repeatable'   => false,
			'before_group' => '<div class="tab-content" id="general">',
			'after_group'  => '</div>',
			'options'      => array(
				'group_title'   => __( 'General', 'stm_gdpr_compliance' ),
				'sortable'      => false,
				'show_as_tab'   => true
			)
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'         => __( 'Privacy and Cookie consent popup', 'stm_gdpr_compliance' ),
			'desc'         => __( 'Ask visitors to accept using Cookie and data.', 'stm_gdpr_compliance' ),
			'id'           => 'privacy_title',
			'type'         => 'title',
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Enable', 'stm_gdpr_compliance' ),
			'desc'        => __( 'Display Privacy and Cookie popup box', 'stm_gdpr_compliance' ),
			'id'          => 'popup',
			'type'        => 'checkbox'
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Block Cookies', 'stm_gdpr_compliance' ),
			'desc'        => __( 'Block Cookies until accepting the Coockie Consent', 'stm_gdpr_compliance' ),
			'id'          => 'block_cookies',
			'type'        => 'checkbox'
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Expire time (seconds)', 'stm_gdpr_compliance' ),
			'desc'        => __( 'Cookie consent expire time. Default 6 months', 'stm_gdpr_compliance' ),
			'id'          => 'expire_time',
			'type'        => 'text',
			'default'	  => '15768000',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Button text', 'stm_gdpr_compliance' ),
			'id'          => 'button_text',
			'type'        => 'text',
			'default'	  => 'Ok, I agree'
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Popup content', 'stm_gdpr_compliance' ),
			'id'          => 'popup_content',
			'type'        => 'wysiwyg',
			'default'	  => __( 'This website uses cookies and asks your personal data to enhance your browsing experience.', 'stm_gdpr_compliance' ),
			'options'	  => array(
				'textarea_rows'	=> 10
			)
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Popup background color', 'stm_gdpr_compliance' ),
			'id'          => 'popup_bg_color',
			'type'        => 'colorpicker',
			'default'     => '#131323',
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Popup text color', 'stm_gdpr_compliance' ),
			'id'          => 'popup_text_color',
			'type'        => 'colorpicker',
			'default'     => '#fff',
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Popup position', 'stm_gdpr_compliance' ),
			'id'          => 'popup_position',
			'type'        => 'select',
			'options'     => array(
				'right_top_' 	=> 'Right Top',
				'left_top_' 	=> 'Left Top',
				'right_bottom_' => 'Right Bottom',
				'left_bottom_' 	=> 'Left Bottom'
			),
			'default'	  => 'left_bottom_'
		) );

		$cmb_options->add_group_field( $general_group_id, array(
			'name'        => __( 'Custom CSS', 'stm_gdpr_compliance' ),
			'id'          => 'popup_custom_css',
			'type'        => 'textarea',
			'class'       => 'cmb2-textarea-code'
		) );

		/* Privacy Policy */
		$privacy_group_id = $cmb_options->add_field( array(
			'id'           => STM_GDPR_PREFIX . 'privacy',
			'type'         => 'group',
			'repeatable'   => false,
			'before_group' => '<div class="tab-content" id="general">',
			'after_group'  => '</div>',
			'options'      => array(
				'group_title'   => __( 'Privacy Policy', 'stm_gdpr_compliance' ),
				'sortable'      => false,
				'show_as_tab'   => true
			)
		) );

		$cmb_options->add_group_field( $privacy_group_id, array(
			'name'         => __( 'Privacy Policy content', 'stm_gdpr_compliance' ),
			'desc'         => __( 'Select Privacy Policy page and link text for the Popup', 'stm_gdpr_compliance' ),
			'id'           => 'privacy_page_title',
			'type'         => 'title',
		) );

		$cmb_options->add_group_field( $privacy_group_id, array(
			'name'        => __( 'Privacy Policy page', 'stm_gdpr_compliance' ),
			'id'          => 'privacy_page',
			'type'        => 'select',
			'options'     => Helpers::cmb_pages_array(),
			'default'	  => Helpers::get_privacy_page()
		) );

		$cmb_options->add_group_field( $privacy_group_id, array(
			'name'        => __( 'Custom link text', 'stm_gdpr_compliance' ),
			'id'          => 'link_text',
			'type'        => 'text',
			'default'	  => 'Privacy Policy'
		) );

		/* Plugins Tab */
		$plugins_group_id = $cmb_options->add_field( array(
			'id'           => STM_GDPR_PREFIX . 'plugins',
			'type'         => 'group',
			'repeatable'   => false,
			'before_group' => '<div class="tab-content" id="plugins">',
			'after_group'  => '</div>',
			'options'      => array(
				'group_title'   => __( 'Integrated plugins', 'stm_gdpr_compliance' ),
				'sortable'      => false,
				'show_as_tab'   => true
			)
		) );

		$pluginsList = Helpers::pluginsList();
		if (!empty($pluginsList)) {
			foreach ($pluginsList as $plugin){
				$cmb_options->add_group_field( $plugins_group_id, array(
					'name'        => $plugin['name'],
					'desc'        => $plugin['desc'],
					'id'          => $plugin['slug'] . '_title',
					'type'        => 'title',
				) );

				$cmb_options->add_group_field( $plugins_group_id, array(
					'name'        => __( 'Enable', 'stm_gdpr_compliance' ),
					'id'          => $plugin['slug'],
					'type'        => 'checkbox',
				) );

				$cmb_options->add_group_field( $plugins_group_id, array(
					'name'        => __( 'Checkbox label', 'stm_gdpr_compliance' ),
					'id'          => $plugin['slug'] . '_label',
					'type'        => 'text',
					'default'	  => __( 'I agree with storage and handling of my data by this website.', 'stm_gdpr_compliance' )
				) );

				$cmb_options->add_group_field( $plugins_group_id, array(
					'name'        => __( 'Error notification', 'stm_gdpr_compliance' ),
					'id'          => $plugin['slug'] . '_error',
					'type'        => 'text',
					'default'	  => __( 'You have to accept the privacy checkbox', 'stm_gdpr_compliance' )
				) );
			}
		}

		$cmb_options->add_group_field( $plugins_group_id, array(
			'name'         => __( 'The list of integrated plugins', 'stm_gdpr_compliance' ),
			'desc'         => __( 'Our plugin currently supports <b>Contact Form 7</b>, <b>Gravity Forms</b>, <b>WooCommerce</b>, <b>WordPress Comments</b>, <b>MailChimp Wp</b>, <b>BuddyPress</b>.
				Integrated plugins options will be displayed automatically after installing and activating supported plugins.', 'stm_gdpr_compliance' ),
			'id'           => 'plugins_title',
			'type'         => 'title',
		) );


		/* Data Access Tab */
		$data_access_group_id = $cmb_options->add_field( array(
			'id'           => STM_GDPR_PREFIX . 'data_access',
			'type'         => 'group',
			'repeatable'   => false,
			'before_group' => '<div class="tab-content" id="data_access">',
			'after_group'  => '</div>',
			'options'      => array(
				'group_title'   => __( 'Data Access & To Be Forgotten', 'stm_gdpr_compliance' ),
				'sortable'      => false,
				'show_as_tab'   => true
			)
		) );

		$cmb_options->add_group_field( $data_access_group_id, array(
			'name'         => __( 'NOTE: ', 'stm_gdpr_compliance' ),
			'desc'         => __( 'You have to create a page with a shortcode: <span>[stm-gpdr-data-access]</span> or use <b>GDPR Compliance</b> widget.<br>
								Also, here you can see <a href="' . add_query_arg(array('page' => 'export_personal_data'), admin_url('tools.php')) . '">Data Export Requests</a> and
								<a href="' . add_query_arg(array('page' => 'remove_personal_data'), admin_url('tools.php')) . '">Data Erasure Requests</a>. 
								This feature works only for <b>WordPress 4.9.6</b> or higher versions.', 'stm_gdpr_compliance' ),
			'id'           => 'plugins_title',
			'type'         => 'title',
		) );

		$cmb_options->add_group_field( $data_access_group_id, array(
			'name'        => __( 'Error message', 'stm_gdpr_compliance' ),
			'id'          => 'error_prefix',
			'type'        => 'text',
			'default'	  => __( 'Some errors occurred:', 'stm_gdpr_compliance' )
		) );

		$cmb_options->add_group_field( $data_access_group_id, array(
			'name'        => __( 'Success message', 'stm_gdpr_compliance' ),
			'id'          => 'success',
			'type'        => 'wysiwyg',
			'default'	  => __( 'Your request have been submitted. Check your email to validate your data request.', 'stm_gdpr_compliance' ),
			'options'	  => array(
				'textarea_rows'	=> 15
			)
		) );

		$cmb_options->add_group_field( $data_access_group_id, array(
			'name'        => __( 'Input field classes', 'stm_gdpr_compliance' ),
			'id'          => 'input-class',
			'type'        => 'text',
			'default'	  => ''
		) );

		$cmb_options->add_group_field( $data_access_group_id, array(
			'name'        => __( 'Submit button classes', 'stm_gdpr_compliance' ),
			'id'          => 'button-class',
			'type'        => 'text',
			'default'	  => ''
		) );


	}

	public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}