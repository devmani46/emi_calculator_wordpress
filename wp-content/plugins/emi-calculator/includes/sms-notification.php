<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Simple wrapper to send SMS via configured provider.
 * This function expects the settings in emi_settings.
 * It uses wp_remote_post - adapt to provider spec.
 */

function emi_send_sms( $phone, $message ) {
    $opts = get_option( 'emi_settings', array() );
    $api_url = $opts['sms_api_url'] ?? '';
    $api_key = $opts['sms_api_key'] ?? '';
    $sender = $opts['sms_sender_id'] ?? '';

    if ( empty( $api_url ) || empty( $phone ) ) {
        return false;
    }

    // Build payload depending on provider - this is a flexible example (JSON)
    $body = array(
        'to' => $phone,
        'from' => $sender,
        'message' => $message,
        'api_key' => $api_key,
    );

    $args = array(
        'body' => wp_json_encode( $body ),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'timeout' => 15,
    );

    $response = wp_remote_post( $api_url, $args );
    if ( is_wp_error( $response ) ) {
        return false;
    }

    $code = wp_remote_retrieve_response_code( $response );
    return $code >= 200 && $code < 300;
}
