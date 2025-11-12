<?php
/**
 * Bike EMI Admin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bike_EMI_Admin {
    
    public function __construct() {
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add admin styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Add custom columns
        add_filter('manage_emi_application_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_emi_application_posts_custom_column', array($this, 'render_custom_column'), 10, 2);
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main admin page
        add_menu_page(
            __('EMI Calculator', 'bike-emi-calculator'),
            __('EMI Calculator', 'bike-emi-calculator'),
            'manage_options',
            'bike-emi-calculator',
            array($this, 'render_admin_page'),
            'dashicons-calculator',
            30
        );
        
        // Bike Models submenu
        add_submenu_page(
            'bike-emi-calculator',
            __('Bike Models', 'bike-emi-calculator'),
            __('Bike Models', 'bike-emi-calculator'),
            'manage_options',
            'bike-emi-models',
            array($this, 'render_bike_models_page')
        );
        
        // Tenure Options submenu
        add_submenu_page(
            'bike-emi-calculator',
            __('Tenure Options', 'bike-emi-calculator'),
            __('Tenure Options', 'bike-emi-calculator'),
            'manage_options',
            'bike-emi-tenure',
            array($this, 'render_tenure_options_page')
        );
        
        // Required Documents submenu
        add_submenu_page(
            'bike-emi-calculator',
            __('Required Documents', 'bike-emi-calculator'),
            __('Required Documents', 'bike-emi-calculator'),
            'manage_options',
            'bike-emi-documents',
            array($this, 'render_documents_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'bike-emi-calculator',
            __('Settings', 'bike-emi-calculator'),
            __('Settings', 'bike-emi-calculator'),
            'manage_options',
            'bike-emi-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'bike-emi') === false) {
            return;
        }
        
        wp_enqueue_style('bike-emi-admin', BIKE_EMI_CALCULATOR_URL . 'assets/css/admin.css', array(), BIKE_EMI_CALCULATOR_VERSION);
        wp_enqueue_script('bike-emi-admin', BIKE_EMI_CALCULATOR_URL . 'assets/js/admin.js', array('jquery'), BIKE_EMI_CALCULATOR_VERSION, true);
    }
    
    /**
     * Render main admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'bike-emi-calculator'));
        }
        
        include BIKE_EMI_CALCULATOR_PATH . 'templates/admin-dashboard.php';
    }
    
    /**
     * Render bike models page
     */
    public function render_bike_models_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'bike-emi-calculator'));
        }
        
        include BIKE_EMI_CALCULATOR_PATH . 'templates/admin-bike-models.php';
    }
    
    /**
     * Render tenure options page
     */
    public function render_tenure_options_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'bike-emi-calculator'));
        }
        
        include BIKE_EMI_CALCULATOR_PATH . 'templates/admin-tenure-options.php';
    }
    
    /**
     * Render documents page
     */
    public function render_documents_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'bike-emi-calculator'));
        }
        
        include BIKE_EMI_CALCULATOR_PATH . 'templates/admin-documents.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'bike-emi-calculator'));
        }
        
        include BIKE_EMI_CALCULATOR_PATH . 'templates/admin-settings.php';
    }
    
    /**
     * Set custom columns for EMI Applications post type
     */
    public function set_custom_columns($columns) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Customer Name', 'bike-emi-calculator'),
            'email' => __('Email', 'bike-emi-calculator'),
            'phone' => __('Phone', 'bike-emi-calculator'),
            'bike' => __('Bike Model', 'bike-emi-calculator'),
            'status' => __('Status', 'bike-emi-calculator'),
            'date' => __('Date', 'bike-emi-calculator'),
        );
        return $columns;
    }
    
    /**
     * Render custom column content
     */
    public function render_custom_column($column, $post_id) {
        switch ($column) {
            case 'email':
                echo esc_html(get_post_meta($post_id, 'customer_email', true));
                break;
            case 'phone':
                echo esc_html(get_post_meta($post_id, 'customer_phone', true));
                break;
            case 'bike':
                $bike_id = get_post_meta($post_id, 'bike_id', true);
                if ($bike_id) {
                    global $wpdb;
                    $bike = $wpdb->get_row($wpdb->prepare(
                        "SELECT name FROM {$wpdb->prefix}emi_bike_models WHERE id = %d",
                        $bike_id
                    ));
                    echo esc_html($bike->name ?? 'N/A');
                }
                break;
            case 'status':
                $status = get_post_meta($post_id, 'application_status', true);
                $status = $status ?: 'pending';
                echo '<span class="badge badge-' . esc_attr($status) . '">' . esc_html(ucfirst($status)) . '</span>';
                break;
        }
    }
}
?>
