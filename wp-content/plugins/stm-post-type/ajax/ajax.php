<?php
// Event Sign up form
if (!function_exists('event_sign_up')) {
	function event_sign_up()
	{
		// Get event details
		$json = array();
		$json['errors'] = array();

		$_POST['event']['event_id'] = filter_var($_POST['event']['event_id'], FILTER_VALIDATE_INT);

		if (empty($_POST['event']['event_id'])) {
			return false;
		}

		$event_price = get_post_meta($_POST['event']['event_id'], 'event_price', true);

		if (!filter_var($_POST['event']['name'], FILTER_SANITIZE_STRING)) {
			$json['errors']['name'] = true;
		}
		if (!is_email($_POST['event']['email'])) {
			$json['errors']['email'] = true;
		}
		if (!is_numeric($_POST['event']['phone'])) {
			$json['errors']['phone'] = true;
		}
		if (!filter_var($_POST['event']['message'], FILTER_SANITIZE_STRING)) {
			$json['errors']['message'] = true;
		}

		if (!empty($event_price) && empty($json['errors'])) {

			$participant_data['post_title'] = $_POST['event']['name'];
			$participant_data['post_type'] = 'event_participant';
			$participant_data['post_status'] = 'draft';
			$participant_data['post_excerpt'] = $_POST['event']['message'];
			$participant_id = wp_insert_post($participant_data);
			update_post_meta($participant_id, 'participant_email', $_POST['event']['email']);
			update_post_meta($participant_id, 'participant_phone', $_POST['event']['phone']);
			update_post_meta($participant_id, 'participant_event', $_POST['event']['event_id']);

			$json['redirect_url'] = generatePayment($_POST['event'], $participant_id);
		} elseif (empty($json['errors'])) {

			$participant_data['post_title'] = $_POST['event']['name'];
			$participant_data['post_type'] = 'event_participant';
			$participant_data['post_status'] = 'pending';
			$participant_data['post_excerpt'] = $_POST['event']['message'];
			$participant_id = wp_insert_post($participant_data);

			update_post_meta($participant_id, 'participant_email', $_POST['event']['email']);
			update_post_meta($participant_id, 'participant_phone', $_POST['event']['phone']);
			update_post_meta($participant_id, 'participant_event', $_POST['event']['event_id']);

			$events_admin_email_subject = str_replace(array('[event]'), array(get_the_title($_POST['event']['event_id'])), stm_option('admin_subject'));

			$events_admin_email_message = str_replace(array('[event]', '[name]', '[email]', '[phone]', '[message]'), array(get_the_title($_POST['event']['event_id']), $_POST['event']['name'], $_POST['event']['email'], $_POST['event']['phone'], $_POST['event']['message']), stm_option('admin_message'));

			$events_participant_email_subject = str_replace(array('[event]'), array(get_the_title($_POST['event']['event_id'])), stm_option('user_subject'));

			$events_participant_email_message = str_replace(array('[name]'), array($_POST['event']['name']), stm_option('user_message'));

			add_filter('wp_mail_content_type', 'set_html_content_type');

			$headers[] = 'From: ' . get_bloginfo('blogname') . ' <' . get_bloginfo('admin_email') . '>';

			wp_mail(get_bloginfo('admin_email'), $events_admin_email_subject, nl2br($events_admin_email_message), $headers);

			wp_mail($_POST['event']['email'], $events_participant_email_subject, nl2br($events_participant_email_message), $headers);

			remove_filter('wp_mail_content_type', 'set_html_content_type');

			$json['success'] = __('Your application has been successfully sent', 'stm-post-type');
		}

		echo json_encode($json);
		exit;
	}
}

add_action('wp_ajax_event_sign_up', 'event_sign_up');
add_action('wp_ajax_nopriv_event_sign_up', 'event_sign_up');