<?php
/**
 * Plugin Name: Bike EMI Calculator
 * Description: Complete EMI Calculator for Bike Models with Document Upload & SMS Notifications
 * Version: 1.0.0
 * Author: EMI Solutions
 * License: GPL2
 * Text Domain: bike-emi-calculator
 * Domain Path: /languages
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

define('BIKE_EMI_CALCULATOR_PATH', plugin_dir_path(__FILE__));
define('BIKE_EMI_CALCULATOR_URL', plugin_dir_url(__FILE__));
define('BIKE_EMI_CALCULATOR_VERSION', '1.0.0');

// Include necessary files
require_once BIKE_EMI_CALCULATOR_PATH . 'includes/class-emi-calculator.php';
require_once BIKE_EMI_CALCULATOR_PATH . 'includes/class-emi-database.php';
require_once BIKE_EMI_CALCULATOR_PATH . 'includes/class-emi-ajax.php';
require_once BIKE_EMI_CALCULATOR_PATH . 'includes/class-emi-admin.php';
require_once BIKE_EMI_CALCULATOR_PATH . 'includes/class-emi-notifications.php';

// Initialize plugin on load
add_action('plugins_loaded', 'bike_emi_calculator_init');
function bike_emi_calculator_init() {
    load_plugin_textdomain('bike-emi-calculator', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Initialize classes
    new Bike_EMI_Calculator();
    new Bike_EMI_Database();
    new Bike_EMI_AJAX();
    new Bike_EMI_Admin();
    new Bike_EMI_Notifications();
}

// Activation hook
register_activation_hook(__FILE__, 'bike_emi_calculator_activate');
function bike_emi_calculator_activate() {
    Bike_EMI_Database::create_tables();
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'bike_emi_calculator_deactivate');
function bike_emi_calculator_deactivate() {
    flush_rewrite_rules();
}

// Enqueue frontend scripts and styles
add_action('wp_enqueue_scripts', 'bike_emi_calculator_enqueue_scripts');
function bike_emi_calculator_enqueue_scripts() {
    wp_enqueue_script('bike-emi-calculator', BIKE_EMI_CALCULATOR_URL . 'assets/js/bike-emi.js', array('jquery'), BIKE_EMI_CALCULATOR_VERSION, true);
    wp_enqueue_style('bike-emi-calculator', BIKE_EMI_CALCULATOR_URL . 'assets/css/bike-emi.css', array(), BIKE_EMI_CALCULATOR_VERSION);
    
    wp_localize_script('bike-emi-calculator', 'emiData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('emi_nonce'),
        'siteUrl' => site_url(),
    ));
}

// Create uploads directory for EMI documents
add_action('wp_mkdir_p', 'bike_emi_calculator_create_uploads_dir');
function bike_emi_calculator_create_uploads_dir() {
    $upload_dir = wp_upload_dir();
    $emi_dir = $upload_dir['basedir'] . '/emi-applications';
    
    if (!file_exists($emi_dir)) {
        wp_mkdir_p($emi_dir);
        
        // Create .htaccess for security
        $htaccess_content = "Deny from all\n";
        file_put_contents($emi_dir . '/.htaccess', $htaccess_content);
    }
}
add_action('init', 'bike_emi_calculator_create_uploads_dir');
?>
