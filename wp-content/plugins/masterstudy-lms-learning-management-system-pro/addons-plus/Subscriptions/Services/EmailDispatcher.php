<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Services;

use MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories\SubscriptionRepository;
use STM_LMS_Helpers;

if (
	! class_exists( 'STM_LMS_Helpers' )
	|| ! method_exists( 'STM_LMS_Helpers', 'is_pro_plus' )
	|| ! STM_LMS_Helpers::is_pro_plus()
	|| ! function_exists( 'is_ms_lms_addon_enabled' )
	|| ! is_ms_lms_addon_enabled( 'subscriptions' )
) {
	return;
}

function ms_lms_subscriptions_build_email_context( $user_id, $subscription_id ) {
	$repo      = new SubscriptionRepository();
	$plan      = $repo->get( $subscription_id );
	$plan_meta = maybe_unserialize( $plan['meta']['plan'] ?? '' );
	$plan_name = ( is_array( $plan_meta ) && ! empty( $plan_meta['name'] ) ) ? $plan_meta['name'] : 'Subscription';

	$user         = get_userdata( (int) $user_id );
	$user_mail    = ( $user && ! empty( $user->user_email ) ) ? $user->user_email : '';
	$raw_site_url = \STM_LMS_Helpers::masterstudy_lms_get_site_url();

	$email_settings = get_option( 'stm_lms_email_manager_settings', array() );
	if ( is_ms_lms_addon_enabled( 'email_manager' ) && ! empty( $email_settings ) ) {
		$raw_site_url = \MS_LMS_Email_Template_Helpers::link( \STM_LMS_Helpers::masterstudy_lms_get_site_url() );
	}

	return array(
		'blog_name'       => \STM_LMS_Helpers::masterstudy_lms_get_site_name(),
		'site_url'        => $raw_site_url, // raw URL (not linked)
		'date'            => gmdate( 'Y-m-d H:i:s' ),
		'user_login'      => \STM_LMS_Helpers::masterstudy_lms_get_user_full_name_or_login( $user_id ),
		'user_id'         => (int) $user_id,
		'user_email'      => sanitize_email( $user_mail ),
		'plan_name'       => sanitize_text_field( $plan_name ),
		'expiration_date' => sanitize_text_field( $plan['end_date'] ?? '' ),
		'is_trial'        => ! empty( $plan['is_trial_used'] ),
	);
}

function ms_lms_subscriptions_send_email_template( $template, $data, $subscription_id, $order_id = 0 ) {
	if ( empty( $template ) || empty( $data ) ) {
		return;
	}

	$key = sprintf( 'ms_lms_email_%s_%d_%d', $template, (int) $subscription_id, (int) $order_id );
	if ( get_transient( $key ) ) {
		return;
	}
	set_transient( $key, 1, HOUR_IN_SECONDS );

	// Resolve recipient.
	$recipient = ! empty( $data['user_email'] ) ? $data['user_email'] : '';
	if ( empty( $recipient ) && ! empty( $data['user_id'] ) ) {
		$user      = get_userdata( (int) $data['user_id'] );
		$recipient = ( $user && ! empty( $user->user_email ) ) ? $user->user_email : '';
	}
	if ( empty( $recipient ) ) {
		return;
	}

	$has_email_manager = function_exists( 'is_ms_lms_addon_enabled' )
	&& is_ms_lms_addon_enabled( 'email_manager' )
	&& class_exists( '\STM_LMS_Helpers' )
	&& method_exists( '\STM_LMS_Helpers', 'send_email' );

	if ( $has_email_manager ) {
		// Use Email Manager transport + templates.
		\STM_LMS_Helpers::send_email(
			$recipient,
			'',
			'',
			$template,
			$data
		);

		return;
	}

	// Fallback: full HTML (same copy as PRO templates).
	ms_lms_subscriptions_send_via_fallback( $recipient, (string) $template, (array) $data );
}


// In Services/EmailDispatcher.php (or a required file in the same namespace)
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound

/**
 * Return PRO-like template definition (subject + message) for a given key.
 * This is used ONLY when Email Manager is disabled.
 */
function ms_lms_subscriptions_get_fallback_template( string $template ): array {
	// Map of PRO templates (exact copies of your subjects/messages).
	$map = array(
		'masterstudy_lms_subscription_trial_access' => array(
			'subject' => esc_html__( 'Your trial access for {{plan_name}} has started', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}},<br>
		Your trial access for {{plan_name}} has started today. <br>
		Enjoy exploring all available features and courses included in your plan. <br>
		Your trial will remain active until {{expiration_date}}. <br>
		Make the most of it and start learning today. <br>
		Log in [{{site_url}}] anytime to continue your learning journey.',
		),

		'masterstudy_lms_subscription_state_activated' => array(
			'subject' => esc_html__( 'Your subscription {{plan_name}} is now active', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}},<br>
		Good news. Your subscription for {{plan_name}} on {{blog_name}} is now active. <br>
		You can start accessing all courses and materials included in your plan. <br>
		Log in [{{site_url}}] anytime to continue your learning journey.',
		),

		'masterstudy_lms_subscription_state_on_hold' => array(
			'subject' => esc_html__( 'Your subscription {{plan_name}} is currently on hold', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}},<br>
		Your subscription for {{plan_name}} on {{blog_name}} has been placed on hold. <br>
		During this period, access to course content may be limited. <br>
		If this change was unexpected, please check your account status or contact us for assistance.',
		),

		'masterstudy_lms_subscription_state_renewed' => array(
			'subject' => esc_html__( 'Your subscription {{plan_name}} has been renewed', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}}, <br>
		Your subscription for {{plan_name}} on {{blog_name}} has been successfully renewed on {{date}}.<br>
		Thank you for staying with us. <br>
		You can continue [{{site_url}}] enjoying all your courses without interruption.',
		),

		'masterstudy_lms_subscription_state_expired' => array(
			'subject' => esc_html__( 'Your subscription {{plan_name}} has expired', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}}, <br>
		Your subscription for {{plan_name}} on {{blog_name}} has expired. <br>
		Access to courses in your plan is now paused. <br>
		You can renew your subscription at any time to regain full access and continue your learning journey. Renew now at {{site_url}}.',
		),

		'masterstudy_lms_subscription_state_expires_soon' => array(
			'subject' => esc_html__( 'Your subscription expires on {{expiration_date}}', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}}, <br>
		This is a friendly reminder that your subscription for {{plan_name}} will expire on {{expiration_date}}. <br>
		To avoid interruption in your learning progress, consider renewing your subscription before it ends. <br>
		You can manage or renew your plan anytime from here [{{site_url}}].',
		),

		'masterstudy_lms_subscription_state_cancelled' => array(
			'subject' => esc_html__( 'Your subscription {{plan_name}} has been cancelled', 'masterstudy-lms-learning-management-system-pro' ),
			'message' => 'Hello {{user_login}}, <br>
		Your subscription for {{plan_name}} on {{blog_name}} has been cancelled as of {{date}}.<br>
		If you cancelled it yourself, no further action is needed. If this was a mistake, you can easily reactivate your plan from your account.<br>
		Visit here [{{site_url}}] to view your subscription options.',
		),
	);

	return $map[ $template ] ?? array(
		'subject' => '',
		'message' => '',
	);
}

/**
 * Render a template by replacing {{tags}} and converting [{{site_url}}] into an anchor.
 *
 * @param string $text Raw subject or body text with placeholders.
 * @param array $data Context (blog_name, site_url, date, user_login, plan_name, expiration_date, etc.).
 *
 * @return string
 */
function ms_lms_subscriptions_render_template_text( string $text, array $data ): string {
	// 1) Build a clean URL even if 'site_url' came as an anchor (defensive).
	$raw = (string) ( $data['site_url'] ?? '' );
	if ( preg_match( '/href="([^"]+)"/', $raw, $m ) ) {
		$url = $m[1]; // extract href if it’s an <a>
	} else {
		$url = $raw;
	}

	// 2) Replace the special token [{{site_url}}] into a clickable link
	//    BEFORE we escape/replace {{...}} placeholders.
	if ( false !== strpos( $text, '[{{site_url}}]' ) ) {
		$link = $url ? '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>' : '';
		$text = str_replace( '[{{site_url}}]', $link, $text );
	}

	// 3) Now replace plain {{placeholders}} with escaped values.
	foreach ( $data as $key => $value ) {
		$key  = (string) $key;
		$val  = is_scalar( $value ) ? (string) $value : '';
		$text = str_replace( '{{' . $key . '}}', esc_html( $val ), $text );
	}

	return $text;
}

/**
 * Fallback sender (full HTML): builds subject/body from PRO templates,
 * when Email Manager is disabled.
 */
function ms_lms_subscriptions_send_via_fallback( string $recipient, string $template, array $data ): void {
	$tpl  = ms_lms_subscriptions_get_fallback_template( $template );
	$subj = ms_lms_subscriptions_render_template_text( $tpl['subject'] ?? '', $data );
	$body = ms_lms_subscriptions_render_template_text( $tpl['message'] ?? '', $data );

	if ( '' === $subj || '' === $body ) {
		// Nothing to send.
		return;
	}

	add_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );
	wp_mail( $recipient, $subj, $body );
	remove_filter( 'wp_mail_content_type', 'STM_LMS_Helpers::set_html_content_type' );

}

// phpcs:enable


add_action(
	'masterstudy_lms_subscription_created',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );

		if ( ! empty( $data['is_trial'] ) && ! empty( $data['expiration_date'] ) ) {
			ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_trial_access', $data, $subscription_id );
		} else {
			ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_activated', $data, $subscription_id );
		}
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_activated',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_activated', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_suspended',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_on_hold', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_payment_succeeded',
	function ( $user_id, $subscription_id ) {
		if ( ! ms_lms_should_send_renewed_email( (int) $subscription_id ) ) {
			return;
		}
		$data         = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		$data['date'] = current_time( 'mysql' );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_renewed', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_reactivated',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_activated', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_cancelled',
	function ( $user_id, $subscription_id ) {
		$data         = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		$data['date'] = current_time( 'mysql' );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_cancelled', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_expired',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_expired', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_payment_completed',
	function ( $user_id, $subscription_id, $order_id ) {
		if ( ! ms_lms_should_send_renewed_email( (int) $subscription_id ) ) {
			return;
		}
		$data         = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		$data['date'] = current_time( 'mysql' );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_renewed', $data, $subscription_id, $order_id );
	},
	10,
	3
);

add_action(
	'masterstudy_lms_subscription_payment_failed',
	function ( $user_id, $subscription_id, $order_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_on_hold', $data, $subscription_id, $order_id );
	},
	10,
	3
);

add_action(
	'masterstudy_lms_subscription_payment_refunded',
	function ( $user_id, $subscription_id, $order_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_on_hold', $data, $subscription_id, $order_id );
	},
	10,
	3
);

add_action(
	'masterstudy_lms_subscription_refunded',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		// If you have a separate template, switch to 'masterstudy_lms_subscription_state_refunded'
		ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_on_hold', $data, $subscription_id );
	},
	10,
	2
);

add_action(
	'masterstudy_lms_subscription_expires_soon',
	function ( $user_id, $subscription_id ) {
		$data = ms_lms_subscriptions_build_email_context( $user_id, $subscription_id );
		if ( ! empty( $data['expiration_date'] ) ) {
			ms_lms_subscriptions_send_email_template( 'masterstudy_lms_subscription_state_expires_soon', $data, $subscription_id );
		}
	},
	10,
	2
);

