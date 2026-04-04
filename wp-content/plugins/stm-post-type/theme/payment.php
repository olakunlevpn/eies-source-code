<?php 

if( !empty( $_GET['stm_check_donation_ipn'] ) ) {
	
	header('HTTP/1.1 200 OK');
	checkPayment( $_REQUEST );

	exit;
}

function paypal_url() {
	$paypal_mode = stm_option( 'paypal_mode' );
	$paypal_url      = ( $paypal_mode == 'live' ) ? 'www.paypal.com' : 'www.sandbox.paypal.com';
	return $paypal_url;
}

if( ! function_exists( 'generatePayment' ) ) {
	
	function generatePayment( $data, $event = false ){
		
		if($event) {
			$participant_id = $event;
			$amount = get_post_meta( $data['event_id'], 'event_price', true );
			$return['result'] = true;
			$paypalEmail = stm_option( 'paypal_email' );
			$returnUrl            = home_url();
	
			$items['item_name']   = get_the_title( $data['event_id'] );
			$items['item_number'] = $data['event_id'];
			$items['amount']      = $amount;
			$items                = http_build_query( $items );
	
			$return = 'https://'.paypal_url() . '/cgi-bin/webscr?cmd=_xclick&business='.$paypalEmail.'&'. $items .'&no_shipping=1&no_note=1&currency_code='.stm_option('currency').'&bn=PP%2dBuyNowBF&charset=UTF%2d8&invoice=' . $participant_id  . '&return=' . $returnUrl . '&rm=2&notify_url='.$returnUrl;
		}
	
		return $return;
	
	}
}

if( ! function_exists( 'checkPayment' ) ){
	
	function checkPayment($data){
	
		$item_name        = $data['item_name'];
		$item_number      = $data['item_number'];
		$payment_status   = $data['payment_status'];
		$payment_amount   = $data['mc_gross'];
		$payment_currency = $data['mc_currency'];
		$txn_id           = $data['txn_id'];
		$receiver_email   = $data['receiver_email'];
		$payer_email      = $data['payer_email'];
		$invoice          = $data['invoice'];
	
		$req = 'cmd=_notify-validate';
	
		foreach ($data as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req  .= "&$key=$value";
		}
	
		$ch = curl_init('https://'.paypal_url().'/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
	
		if( !($res = curl_exec($ch)) ) {
			echo ("Got " . curl_error($ch) . " when processing IPN data");
			curl_close($ch);
			return false;
		}
		curl_close($ch);
	
		if (strcmp ($res, "VERIFIED") == 0) {
	
			$mail_To      = get_bloginfo( 'admin_email' );
			$mail_Subject = "VERIFIED IPN";
			$mail_Body    = $req;
	
	
			wp_update_post( array( 'ID'=>$invoice, 'post_status'=>'publish' ) );
	
			if( get_post_type( $invoice ) == 'event_participant' ){
				$participant_info = get_post( $invoice );
				$events_admin_email_subject = str_replace( array( '[event]' ), array( get_the_title( $item_number ) ), get_theme_mod( 'events_admin_email_subject', __( 'New participant for [event]', 'stm-post-type' ) ));
				$events_admin_email_message = str_replace( array( '[event]', '[name]', '[email]', '[phone]', '[message]' ), array( get_the_title( $item_number ), get_the_title( $invoice ), get_post_meta( $invoice, 'participant_email', true ), get_post_meta( $invoice, 'participant_phone', true ), $participant_info->post_excerpt ), get_theme_mod( 'events_admin_email_message', __( 'A new member wants to join your [event]
				Participant Info:
		        Name: [name]
				Email: [email]
				Phone: [phone]
				Message: [message]', 'stm-post-type' ) ));
				$events_participant_email_subject = str_replace( array( '[event]' ), array( get_the_title( $item_number ) ), get_theme_mod( 'events_participant_email_subject', __( 'Confirmation of your pariticipation in the [event]', 'stm-post-type' ) ));
				$events_participant_email_message = str_replace(
					array( '[name]' ),
					array( get_the_title( $invoice ) ),
					get_theme_mod(
						'events_participant_email_message',
						sprintf(__( 'Dear [name]. 
						This email is sent to you to confirm your participation in the event. 
						We will contact you soon with further details. 
						With any question, feel free to phone +999999999999 or write to <a href="mailto:%s">%s</a>.
						Regards, MasterStudy Team', 'stm-post-type' ), get_bloginfo('admin_email'), get_bloginfo('admin_email') )
					)
				);
	
				add_filter('wp_mail_content_type', 'set_html_content_type');
	
				$headers[] = 'From: ' . get_bloginfo('blogname') . ' <' . get_bloginfo('admin_email') . '>';
	
				wp_mail( get_bloginfo( 'admin_email' ), $events_admin_email_subject, nl2br( $events_admin_email_message ), $headers );
	
				wp_mail( get_post_meta( $invoice, 'participant_email', true ), $events_participant_email_subject, nl2br( $events_participant_email_message ), $headers );
	
				remove_filter('wp_mail_content_type', 'set_html_content_type');
			}	
		}
		else if (strcmp ($res, "INVALID") == 0) {
	
			$mail_To      = get_bloginfo( 'admin_email' );
			$mail_Subject = "INVALID IPN";
			$mail_Body    = $req;
	
			//wp_mail($mail_To, $mail_Subject, $mail_Body);
		}
	}
}