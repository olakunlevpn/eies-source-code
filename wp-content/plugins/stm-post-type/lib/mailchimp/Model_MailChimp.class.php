<?php
/**
 *          RAFAEL FERREIRA © 2014 || MailChimp Form
 * ------------------------------------------------------------------------
 *                      ** MailChimp Class    **
 * ------------------------------------------------------------------------
 */
require_once 'mailchimp.php';

class Model_MailChimp {
	public static function subscribe( $email, $merge_vars ) {
		$instance = new Mailchimp( stm_option( 'mailchimp_api_key' ) );
		return $instance->lists->subscribe( stm_option( 'mailchimp_list_id' ), $email, $merge_vars, 'html', false );
	}

	public static function subscribe_with_confirmation( $email, $merge_vars ) {
		$instance = new Mailchimp( stm_option( 'mailchimp_api_key' ) );
		return $instance->lists->subscribe( stm_option( 'mailchimp_list_id' ), $email, $merge_vars );
	}
}
