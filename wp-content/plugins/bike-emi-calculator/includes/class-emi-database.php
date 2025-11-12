<?php
/**
 * Bike EMI Database Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bike_EMI_Database {
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Bike Models Table
        $bike_models_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}emi_bike_models (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description LONGTEXT,
            price DECIMAL(10, 2) NOT NULL,
            interest_rate DECIMAL(5, 2) NOT NULL,
            image_url VARCHAR(500),
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY status (status)
        ) $charset_collate;";
        
        // Tenure Options Table
        $tenure_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}emi_tenure_options (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            months INT(11) NOT NULL,
            years INT(11) NOT NULL,
            label VARCHAR(100) NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY months_unique (months),
            KEY status (status)
        ) $charset_collate;";
        
        // Required Documents Table
        $documents_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}emi_required_documents (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            doc_type VARCHAR(100) NOT NULL,
            description LONGTEXT,
            allowed_formats VARCHAR(255),
            is_required TINYINT(1) DEFAULT 1,
            display_order INT(11) DEFAULT 0,
            status VARCHAR(20) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY status (status)
        ) $charset_collate;";
        
        // EMI Applications Table
        $applications_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}emi_applications (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            customer_email VARCHAR(255) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            customer_address LONGTEXT,
            bike_id BIGINT(20) UNSIGNED NOT NULL,
            tenure_months INT(11) NOT NULL,
            application_status VARCHAR(50) DEFAULT 'submitted',
            monthly_emi DECIMAL(10, 2),
            total_amount DECIMAL(10, 2),
            total_interest DECIMAL(10, 2),
            reference_id VARCHAR(100),
            sms_sent TINYINT(1) DEFAULT 0,
            sms_sent_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY customer_phone (customer_phone),
            KEY application_status (application_status),
            KEY created_at (created_at),
            UNIQUE KEY reference_id (reference_id)
        ) $charset_collate;";
        
        // Application Documents Table
        $app_documents_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}emi_application_documents (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            application_id BIGINT(20) UNSIGNED NOT NULL,
            doc_type VARCHAR(100) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_size BIGINT(20),
            mime_type VARCHAR(100),
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY application_id (application_id),
            KEY doc_type (doc_type),
            FOREIGN KEY (application_id) REFERENCES {$wpdb->prefix}emi_applications(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        dbDelta($bike_models_sql);
        dbDelta($tenure_sql);
        dbDelta($documents_sql);
        dbDelta($applications_sql);
        dbDelta($app_documents_sql);
        
        // Insert default data
        self::insert_default_data();
    }
    
    /**
     * Insert default data
     */
    public static function insert_default_data() {
        global $wpdb;
        
        // Check if data already exists
        $bike_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}emi_bike_models");
        
        if ($bike_count == 0) {
            // Insert default bike models (placeholder)
            $wpdb->insert($wpdb->prefix . 'emi_bike_models', array(
                'name' => 'Sample Bike Model 1',
                'price' => 100000,
                'interest_rate' => 8.5,
                'status' => 'active',
            ));
        }
        
        // Check tenure options
        $tenure_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}emi_tenure_options");
        
        if ($tenure_count == 0) {
            // Insert default tenure options
            $default_tenures = array(
                array('months' => 12, 'years' => 1, 'label' => '1 Year (12 Months)'),
                array('months' => 24, 'years' => 2, 'label' => '2 Years (24 Months)'),
                array('months' => 36, 'years' => 3, 'label' => '3 Years (36 Months)'),
                array('months' => 48, 'years' => 4, 'label' => '4 Years (48 Months)'),
                array('months' => 60, 'years' => 5, 'label' => '5 Years (60 Months)'),
            );
            
            foreach ($default_tenures as $tenure) {
                $wpdb->insert($wpdb->prefix . 'emi_tenure_options', $tenure);
            }
        }
        
        // Check required documents
        $doc_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}emi_required_documents");
        
        if ($doc_count == 0) {
            // Insert default documents
            $default_docs = array(
                array(
                    'name' => 'Citizen Card',
                    'doc_type' => 'citizen_card',
                    'description' => 'Upload front and back side of citizen card',
                    'allowed_formats' => '.pdf,.jpg,.jpeg,.png',
                    'is_required' => 1,
                    'display_order' => 1,
                ),
                array(
                    'name' => 'PAN Card',
                    'doc_type' => 'pan',
                    'description' => 'PAN card copy',
                    'allowed_formats' => '.pdf,.jpg,.jpeg,.png',
                    'is_required' => 1,
                    'display_order' => 2,
                ),
                array(
                    'name' => 'Income Proof',
                    'doc_type' => 'income',
                    'description' => 'Salary slip or income certificate',
                    'allowed_formats' => '.pdf,.jpg,.jpeg,.png',
                    'is_required' => 1,
                    'display_order' => 3,
                ),
                array(
                    'name' => 'Address Proof',
                    'doc_type' => 'address',
                    'description' => 'Utility bill or rental agreement',
                    'allowed_formats' => '.pdf,.jpg,.jpeg,.png',
                    'is_required' => 1,
                    'display_order' => 4,
                )
            );
            
            foreach ($default_docs as $doc) {
                $wpdb->insert($wpdb->prefix . 'emi_required_documents', $doc);
            }
        }
    }
    
    /**
     * Get application by ID
     */
    public static function get_application($app_id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}emi_applications WHERE id = %d",
            $app_id
        ), ARRAY_A);
    }
    
    /**
     * Create EMI application
     */
    public static function create_application($data) {
        global $wpdb;
        
        $reference_id = 'EMI-' . time() . '-' . rand(1000, 9999);
        
        $insert_data = array(
            'customer_name' => sanitize_text_field($data['customer_name']),
            'customer_email' => sanitize_email($data['customer_email']),
            'customer_phone' => sanitize_text_field($data['customer_phone']),
            'customer_address' => sanitize_textarea_field($data['customer_address']),
            'bike_id' => intval($data['bike_id']),
            'tenure_months' => intval($data['tenure_months']),
            'monthly_emi' => isset($data['monthly_emi']) ? floatval($data['monthly_emi']) : 0,
            'total_amount' => isset($data['total_amount']) ? floatval($data['total_amount']) : 0,
            'total_interest' => isset($data['total_interest']) ? floatval($data['total_interest']) : 0,
            'reference_id' => $reference_id,
            'application_status' => 'submitted',
        );
        
        $wpdb->insert($wpdb->prefix . 'emi_applications', $insert_data);
        
        return $wpdb->insert_id;
    }
    
    /**
     * Add application document
     */
    public static function add_application_document($app_id, $doc_type, $file_path, $file_name, $file_size, $mime_type) {
        global $wpdb;
        
        return $wpdb->insert($wpdb->prefix . 'emi_application_documents', array(
            'application_id' => intval($app_id),
            'doc_type' => sanitize_text_field($doc_type),
            'file_path' => $file_path,
            'file_name' => $file_name,
            'file_size' => intval($file_size),
            'mime_type' => $mime_type,
        ));
    }
    
    /**
     * Get application documents
     */
    public static function get_application_documents($app_id) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}emi_application_documents WHERE application_id = %d ORDER BY uploaded_at DESC",
            $app_id
        ), ARRAY_A);
    }
    
    /**
     * Update application status
     */
    public static function update_application_status($app_id, $status) {
        global $wpdb;
        
        return $wpdb->update(
            $wpdb->prefix . 'emi_applications',
            array('application_status' => sanitize_text_field($status)),
            array('id' => intval($app_id)),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Mark SMS as sent
     */
    public static function mark_sms_sent($app_id) {
        global $wpdb;
        
        return $wpdb->update(
            $wpdb->prefix . 'emi_applications',
            array(
                'sms_sent' => 1,
                'sms_sent_at' => current_time('mysql'),
            ),
            array('id' => intval($app_id)),
            array('%d', '%s'),
            array('%d')
        );
    }
}
?>
