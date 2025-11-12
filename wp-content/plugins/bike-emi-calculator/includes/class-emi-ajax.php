<?php
/**
 * Bike EMI AJAX Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bike_EMI_AJAX {
    
    public function __construct() {
        // AJAX endpoints
        add_action('wp_ajax_submit_emi_application', array($this, 'submit_emi_application'));
        add_action('wp_ajax_nopriv_submit_emi_application', array($this, 'submit_emi_application'));
        
        add_action('wp_ajax_get_bike_models', array($this, 'get_bike_models'));
        add_action('wp_ajax_nopriv_get_bike_models', array($this, 'get_bike_models'));
    }
    
    /**
     * Submit EMI application via AJAX
     */
    public function submit_emi_application() {
        check_ajax_referer('emi_nonce', 'nonce');
        
        // Validate required fields
        if (empty($_POST['customer_name']) || empty($_POST['customer_email']) || empty($_POST['customer_phone']) || 
            empty($_POST['bike_id']) || empty($_POST['tenure_months'])) {
            wp_send_json_error(__('All fields are required.', 'bike-emi-calculator'));
        }
        
        // Validate email
        if (!is_email($_POST['customer_email'])) {
            wp_send_json_error(__('Invalid email address.', 'bike-emi-calculator'));
        }
        
        // Validate phone
        if (!preg_match('/^[0-9]{10}$/', sanitize_text_field($_POST['customer_phone']))) {
            wp_send_json_error(__('Invalid phone number. Please enter a 10-digit number.', 'bike-emi-calculator'));
        }
        
        // Prepare application data
        $app_data = array(
            'customer_name' => sanitize_text_field($_POST['customer_name']),
            'customer_email' => sanitize_email($_POST['customer_email']),
            'customer_phone' => sanitize_text_field($_POST['customer_phone']),
            'customer_address' => sanitize_textarea_field($_POST['customer_address']),
            'bike_id' => intval($_POST['bike_id']),
            'tenure_months' => intval($_POST['tenure_months']),
            'monthly_emi' => isset($_POST['monthly_emi']) ? floatval($_POST['monthly_emi']) : 0,
            'total_amount' => isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0,
            'total_interest' => isset($_POST['total_interest']) ? floatval($_POST['total_interest']) : 0,
        );
        
        // Create application
        $app_id = Bike_EMI_Database::create_application($app_data);
        
        if (!$app_id) {
            wp_send_json_error(__('Failed to create application.', 'bike-emi-calculator'));
        }
        
        // Handle file uploads
        if (!empty($_FILES)) {
            $upload_result = $this->handle_document_upload($app_id);
            if (!$upload_result['success']) {
                wp_send_json_error($upload_result['message']);
            }
        }
        
        // Send SMS notification
        Bike_EMI_Notifications::send_sms_notification($app_data['customer_phone'], $app_data['customer_name'], $app_id);
        
        // Send confirmation email
        Bike_EMI_Notifications::send_confirmation_email($app_data['customer_email'], $app_data['customer_name'], $app_id);
        
        wp_send_json_success(array(
            'message' => __('Application submitted successfully! You will receive confirmation SMS shortly.', 'bike-emi-calculator'),
            'app_id' => $app_id,
            'reference_id' => 'EMI-' . time() . '-' . rand(1000, 9999),
        ));
    }
    
    /**
     * Handle document upload
     */
    private function handle_document_upload($app_id) {
        $upload_dir = wp_upload_dir();
        $app_folder = $upload_dir['basedir'] . '/emi-applications/' . $app_id;
        
        // Create application folder
        if (!file_exists($app_folder)) {
            if (!wp_mkdir_p($app_folder)) {
                return array(
                    'success' => false,
                    'message' => __('Failed to create upload directory.', 'bike-emi-calculator')
                );
            }
        }
        
        // Allowed file types
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'application/pdf');
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        foreach ($_FILES as $field_name => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Validate file size
                if ($file['size'] > $max_file_size) {
                    return array(
                        'success' => false,
                        'message' => sprintf(__('File %s is too large. Maximum size is 5MB.', 'bike-emi-calculator'), $file['name'])
                    );
                }
                
                // Validate file type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mime_type, $allowed_types)) {
                    return array(
                        'success' => false,
                        'message' => sprintf(__('File type not allowed for %s. Only JPG, PNG, and PDF are allowed.', 'bike-emi-calculator'), $file['name'])
                    );
                }
                
                // Generate safe filename
                $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $safe_filename = wp_unique_filename($app_folder, sanitize_file_name($field_name . '_' . time() . '.' . $file_ext));
                $file_path = $app_folder . '/' . $safe_filename;
                
                // Move file
                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    Bike_EMI_Database::add_application_document(
                        $app_id,
                        $field_name,
                        $file_path,
                        $safe_filename,
                        $file['size'],
                        $mime_type
                    );
                    
                    // Change permissions
                    chmod($file_path, 0644);
                } else {
                    return array(
                        'success' => false,
                        'message' => sprintf(__('Failed to upload %s.', 'bike-emi-calculator'), $file['name'])
                    );
                }
            } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                return array(
                    'success' => false,
                    'message' => sprintf(__('Error uploading %s.', 'bike-emi-calculator'), $file['name'])
                );
            }
        }
        
        return array('success' => true);
    }
    
    /**
     * Get bike models via AJAX
     */
    public function get_bike_models() {
        check_ajax_referer('emi_nonce', 'nonce');
        
        $bikes = Bike_EMI_Calculator::get_bike_models();
        wp_send_json_success($bikes);
    }
}
?>
