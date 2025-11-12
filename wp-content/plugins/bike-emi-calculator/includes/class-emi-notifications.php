<?php
/**
 * Bike EMI Notifications Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bike_EMI_Notifications {
    
    /**
     * Send SMS notification
     */
    public static function send_sms_notification($phone, $name, $app_id) {
        // Get application reference ID
        $app = Bike_EMI_Database::get_application($app_id);
        if (!$app) {
            return false;
        }
        
        $reference_id = $app['reference_id'];
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Message to send
        $message = sprintf(
            __('Hi %s, your EMI application has been received. Reference ID: %s. We will contact you shortly.', 'bike-emi-calculator'),
            $name,
            $reference_id
        );
        
        // Allow custom SMS sending via filter/hook
        $sms_sent = apply_filters('bike_emi_send_sms', false, $phone, $message, $app_id);
        
        if ($sms_sent) {
            // Mark SMS as sent in database
            Bike_EMI_Database::mark_sms_sent($app_id);
            return true;
        }
        
        // If no SMS provider is hooked, log the message
        do_action('bike_emi_sms_fallback', $phone, $message, $app_id);
        
        return false;
    }
    
    /**
     * Send confirmation email
     */
    public static function send_confirmation_email($email, $name, $app_id) {
        $app = Bike_EMI_Database::get_application($app_id);
        if (!$app) {
            return false;
        }
        
        global $wpdb;
        $bike_table = $wpdb->prefix . 'emi_bike_models';
        $bike = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $bike_table WHERE id = %d",
            $app['bike_id']
        ), ARRAY_A);
        
        $subject = sprintf(__('EMI Application Confirmation - %s', 'bike-emi-calculator'), $app['reference_id']);
        
        $message = sprintf(
            __('Dear %s,\n\nThank you for submitting your EMI application.\n\nApplication Details:\nReference ID: %s\nBike Model: %s\nMonthly EMI: Rs%s\nTenure: %d months\n\nWe will review your documents and contact you shortly.\n\nBest Regards,\nEMI Support Team', 'bike-emi-calculator'),
            $name,
            $app['reference_id'],
            $bike['name'],
            number_format($app['monthly_emi'], 2),
            $app['tenure_months']
        );
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        return wp_mail($email, $subject, $message, $headers);
    }
    
    /**
     * Send admin notification
     */
    public static function send_admin_notification($app_id) {
        $app = Bike_EMI_Database::get_application($app_id);
        if (!$app) {
            return false;
        }
        
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('New EMI Application Submitted - %s', 'bike-emi-calculator'), $app['reference_id']);
        
        $message = sprintf(
            __('A new EMI application has been submitted.\n\nCustomer Name: %s\nEmail: %s\nPhone: %s\nReference ID: %s\n\nPlease review the documents in the admin panel.', 'bike-emi-calculator'),
            $app['customer_name'],
            $app['customer_email'],
            $app['customer_phone'],
            $app['reference_id']
        );
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        return wp_mail($admin_email, $subject, $message, $headers);
    }
    
    /**
     * Get SMS gateway option
     */
    public static function get_sms_gateway() {
        return get_option('bike_emi_sms_gateway', 'none');
    }
    
    /**
     * Get SMS gateway API key
     */
    public static function get_sms_api_key() {
        return get_option('bike_emi_sms_api_key', '');
    }
}
?>
