<?php
/**
 * Bike EMI Calculator Main Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bike_EMI_Calculator {
    
    public function __construct() {
        // Register shortcode
        add_shortcode('bike_emi_calculator', array($this, 'render_calculator'));
        
        // Register custom post type
        add_action('init', array($this, 'register_emi_application_post_type'));
    }
    
    /**
     * Register custom post type for EMI Applications
     */
    public function register_emi_application_post_type() {
        register_post_type('emi_application', array(
            'label' => __('EMI Applications', 'bike-emi-calculator'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'supports' => array('title', 'custom-fields'),
            'has_archive' => false,
            'rewrite' => false,
            'capabilities' => array(
                'create_posts' => 'manage_options',
                'edit_posts' => 'manage_options',
                'delete_posts' => 'manage_options',
                'edit_others_posts' => 'manage_options',
                'publish_posts' => 'manage_options',
            ),
            'map_meta_cap' => true,
        ));
    }
    
    /**
     * Render the calculator shortcode
     */
    public function render_calculator($atts) {
        $atts = shortcode_atts(array(
            'title' => __('EMI Calculator', 'bike-emi-calculator'),
        ), $atts);
        
        ob_start();
        include BIKE_EMI_CALCULATOR_PATH . 'templates/calculator.php';
        return ob_get_clean();
    }
    
    /**
     * Calculate EMI
     */
    public static function calculate_emi($principal, $annual_rate, $months) {
        if ($months <= 0) {
            return 0;
        }
        
        $monthly_rate = $annual_rate / (12 * 100);
        
        if ($monthly_rate == 0) {
            return $principal / $months;
        }
        
        $numerator = $principal * $monthly_rate * pow(1 + $monthly_rate, $months);
        $denominator = pow(1 + $monthly_rate, $months) - 1;
        
        return $numerator / $denominator;
    }
    
    /**
     * Get bike models from database
     */
    public static function get_bike_models() {
        global $wpdb;
        $table = $wpdb->prefix . 'emi_bike_models';
        
        return $wpdb->get_results("SELECT * FROM $table WHERE status = 'active' ORDER BY name ASC", ARRAY_A);
    }
    
    /**
     * Get tenure options from database
     */
    public static function get_tenure_options() {
        global $wpdb;
        $table = $wpdb->prefix . 'emi_tenure_options';
        
        return $wpdb->get_results("SELECT * FROM $table WHERE status = 'active' ORDER BY months ASC", ARRAY_A);
    }
    
    /**
     * Get required documents from database
     */
    public static function get_required_documents() {
        global $wpdb;
        $table = $wpdb->prefix . 'emi_required_documents';
        
        return $wpdb->get_results("SELECT * FROM $table WHERE status = 'active' ORDER BY display_order ASC", ARRAY_A);
    }
}
?>
