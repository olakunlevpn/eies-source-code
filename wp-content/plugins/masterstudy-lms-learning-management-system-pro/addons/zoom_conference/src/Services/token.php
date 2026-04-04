<?php
// phpcs:ignoreFile
/**
 * JWT Signature Generation for Zoom SDK 4.0.7+
 * 
 * This file contains functions to generate JWT signatures for Zoom Meeting SDK
 * as the generateSDKSignature API has been deprecated in v4.0.7
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate JWT signature for Zoom Meeting SDK 4.0.7+
 * Based on official documentation: https://developers.zoom.us/docs/meeting-sdk/auth/
 * 
 * @param string $app_key Zoom App Key (same as sdkKey)
 * @param string $app_secret Zoom App Secret
 * @param string $meeting_number Meeting Number
 * @param int $role User role (0=Attendee, 1=Host, 5=Assistant)
 * @return string JWT signature
 */
function mslms_generate_zoom_jwt_signature($app_key, $app_secret, $meeting_number, $role = 0, $video_webrtc_mode = 1) {
    $header = array(
        'alg' => 'HS256',
        'typ' => 'JWT'
    );
    
    $timestamp = time();
    $exp = $timestamp + 3600; // 1 hour expiration
    
    // Correct payload structure according to official documentation
    $payload = array(
        'appKey' => $app_key,           // Required: App Key
        'mn' => $meeting_number,         // Required: Meeting Number
        'role' => $role,                // Required: User role
        'iat' => $timestamp,            // Required: Issued at
        'exp' => $exp,                  // Required: Expiration
        'tokenExp' => $exp,             // Required: Token expiration (same as exp)
        'video_webrtc_mode' => $video_webrtc_mode       // Required: Enable WebRTC mode for Gallery View
    );
    
    // Encode header and payload
    $header_encoded = base64url_encode(json_encode($header));
    $payload_encoded = base64url_encode(json_encode($payload));
    
    // Create signature using HMAC SHA256
    $signature_data = $header_encoded . '.' . $payload_encoded;
    $signature = base64url_encode(hash_hmac('sha256', $signature_data, $app_secret, true));
    
    return $header_encoded . '.' . $payload_encoded . '.' . $signature;
}

/**
 * Base64 URL encode (without padding)
 * 
 * @param string $data Data to encode
 * @return string Base64 URL encoded string
 */
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * AJAX handler to generate JWT signature
 */
function mslms_ajax_generate_zoom_signature() {
    // Set proper headers for JSON response
    header('Content-Type: application/json');
    
    // Verify nonce for security (if available and nonce is provided)
    if (isset($_POST['nonce']) && !empty($_POST['nonce'])) {
        if (!wp_verify_nonce($_POST['nonce'], 'mslms_zoom_signature_nonce')) {
            wp_send_json_error('Security check failed');
        }
    }
    
    $app_key = isset($_POST['app_key']) ? sanitize_text_field($_POST['app_key']) : '';
    $app_secret = isset($_POST['app_secret']) ? sanitize_text_field($_POST['app_secret']) : '';
    $meeting_number = isset($_POST['meeting_number']) ? sanitize_text_field($_POST['meeting_number']) : '';
    $role = isset($_POST['role']) ? intval($_POST['role']) : 0;
    $video_webrtc_mode = isset($_POST['video_webrtc_mode']) ? intval($_POST['video_webrtc_mode']) : 1;
    
    if (empty($app_key) || empty($app_secret) || empty($meeting_number)) {
        wp_send_json_error('Missing required parameters');
    }
    
    try {
        $signature = mslms_generate_zoom_jwt_signature($app_key, $app_secret, $meeting_number, $role, $video_webrtc_mode);
        
        wp_send_json_success(array(
            'signature' => $signature,
            'expires_at' => time() + 3600
        ));
    } catch (Exception $e) {
        wp_send_json_error('Failed to generate signature: ' . $e->getMessage());
    }
}

// Register AJAX handlers
add_action('wp_ajax_mslms_generate_zoom_signature', 'mslms_ajax_generate_zoom_signature');
add_action('wp_ajax_nopriv_mslms_generate_zoom_signature', 'mslms_ajax_generate_zoom_signature');
