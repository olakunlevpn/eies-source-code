<?php // @codingStandardsIgnoreStart
/**
 *          RAFAEL FERREIRA © 2014 || MailChimp Form
 * ------------------------------------------------------------------------
 *                      ** Handling Tools **
 * ------------------------------------------------------------------------
 */

require_once 'Model_MailChimp.class.php';

class Handling {

	public static function handling_request( $email, $profile, $type = null ) {
		$merge_vars = self::parse_profile( $profile, $type );
		try {
			Model_MailChimp::subscribe( $email, $merge_vars );
		} catch ( Mailchimp_List_AlreadySubscribed $e ) {
			return self::handling_this( 'List_AlreadySubscribed' );
		} catch ( Mailchimp_List_MergeFieldRequired $e ) {
			exit( 'Please go to http://mailchimp.com/, login, go to your list, then Settings, and put "First Name" and "Last Name" not required.' );
		} catch ( Mailchimp_Invalid_ApiKey $e ) {
			exit( 'Your API Key is Invalid!' );
		} catch ( Mailchimp_List_DoesNotExist $e ) {
			exit( 'The list that you provided does not exists!' );
		} catch ( Exception $e ) {
			// Send error back to developer
			self::sendError( print_r( $e, true ), 'Handling.class.php', '24' );
			return self::handling_this( 'ERRO' );
		}
		self::handling_this();
	}

	public static function handling_request_with_confirmation( $email, $profile, $type = null ) {
		$merge_vars = self::parse_profile( $profile, $type );
		try {
			$result = Model_MailChimp::subscribe_with_confirmation( $email, $merge_vars );

			if ( isset( $result['status'] ) && 'subscribed' === $result['status'] ) {
				$json['success'] = __( 'A notification has been sent to your email. Please confirm your subscription from there.', 'stm-post-type' );
			} else {
				$json['error'] = __( 'User with this email is already subscribed', 'stm-post-type' );
			}

			echo json_encode( $json );
			exit;
		} catch ( Mailchimp_List_AlreadySubscribed $e ) {
			$json['error'] = __( 'User with this email is already subscribed', 'stm-post-type' );
			echo json_encode( $json );
			exit;
		} catch ( Mailchimp_List_MergeFieldRequired $e ) {
			$json['error'] = __( 'Please go to http://mailchimp.com/, login, go to your list, then Settings, and put \"First Name\" and \"Last Name\" not required.', 'stm-post-type' );
			echo json_encode( $json );
			exit;
		} catch ( Mailchimp_Invalid_ApiKey $e ) {
			$json['error'] = __( 'Your API Key is Invalid!', 'stm-post-type' );
			echo json_encode( $json );
			exit;
		} catch ( Mailchimp_List_DoesNotExist $e ) {
			$json['error'] = __( 'The list that you provided does not exists!', 'stm-post-type' );
			echo json_encode( $json );
			exit;
		} catch ( Exception $e ) {
			// Send error back to developer
			self::sendError( print_r( $e, true ), 'Handling.class.php', '42' );
			return self::handling_this( 'ERRO' );
		}
		self::handling_this();
	}

	private static function parse_profile( $profile, $type = null ) {
		if ( is_null( $type ) ) {
			return array();
		}
		if ( $type == 'facebook' ) {
			$array['FNAME'] = @$profile->profile->first_name;
			$array['LNAME'] = @$profile->profile->last_name;
		} elseif ( $type == 'google' ) {
			$array['FNAME'] = @$profile->profile->given_name;
			$array['LNAME'] = @$profile->profile->family_name;
		} elseif ( $type == 'microsoft' ) {
			$array['FNAME'] = @$profile['first_name'];
			$array['LNAME'] = @$profile['last_name'];
		} elseif ( $type == 'linkedin' ) {
			$array['FNAME'] = @$profile['firstName'];
			$array['LNAME'] = @$profile['lastName'];
		}
		return $array;
	}

	private static function handling_this( $response = null ) {
		global $responsePage;
		if ( isset( $response ) && ! is_null( $response ) ) {
			if ( $response == 'List_AlreadySubscribed' ) {
				header( 'Location: ' . $responsePage['repeated'] );
			} else {
				header( 'Location: ' . $responsePage['error'] );
			}
		} else {
			header( 'Location: ' . $responsePage['success'] );
		}
	}

	public static function sendError( $error = '', $file = '', $line = '' ) {
		$erro['description'] = $error;
		$erro['file']        = $file;
		$erro['line']        = $line;

		$array['error_details'] = json_encode( $erro );

		$array['version'] = self::version();
		$array['website'] = get_site_url();
		$array['product'] = 'mailchimp_social_form';

		self::curlHttpRequest( 'http://codecanyon.rafaelferreira.pt/registry/count/error.json', 'post', $array );
	}

	public static function curlHttpRequest( $url, $method = 'get', $request_fields = array() ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );

		if ( $method == 'post' ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $request_fields );
		} elseif ( $method == 'get' ) {
			curl_setopt( $ch, CURLOPT_HTTPGET, true );
		} elseif ( $method == 'del' ) {
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		}

		return curl_exec( $ch );
	}
}
// @codingStandardsIgnoreEnd
