<?php
/**
 * Plugin Name: EMI Calculator & Application Manager
 * Description: EMI calculation + application submission, document upload, and admin CRM interface.
 * Version: 1.0.0
 * Text Domain: emi-calculator
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'EMI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EMI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Includes
require_once EMI_PLUGIN_DIR . 'includes/cpt-register.php';
require_once EMI_PLUGIN_DIR . 'includes/settings-page.php';
require_once EMI_PLUGIN_DIR . 'includes/admin-menu.php';
require_once EMI_PLUGIN_DIR . 'includes/form-handler.php';

// Activation / Deactivation hooks
register_activation_hook( __FILE__, 'emi_plugin_activate' );
register_deactivation_hook( __FILE__, 'emi_plugin_deactivate' );

function emi_plugin_activate() {
    // Create custom role for CRM with specific capabilities (if not exists)
    add_role( 'emi_crm', 'EMI CRM', array(
        'read' => true,
        'edit_emi_applications' => true,
        'publish_emi_applications' => true,
        'manage_emi_settings' => true,
        'read_private_emi_applications' => true,
    ) );

    // Add capabilities to administrator role
    $roles = array( 'administrator' );
    foreach ( $roles as $r ) {
        $role = get_role( $r );
        if ( $role ) {
            $role->add_cap( 'edit_emi_applications' );
            $role->add_cap( 'read_emi_applications' );
            $role->add_cap( 'delete_emi_applications' );
            $role->add_cap( 'manage_emi_settings' );
        }
    }

    // Ensure CPT/taxonomy registered and rewrite flushed
    emi_register_cpt_and_tax();
    flush_rewrite_rules();
}

function emi_plugin_deactivate() {
    // remove capabilities if you want (commented out to avoid accidental loss)
    // flush rewrite rules
    flush_rewrite_rules();
}

// Register scripts and shortcode
add_action( 'wp_enqueue_scripts', 'emi_enqueue_frontend_assets' );
add_action( 'admin_enqueue_scripts', 'emi_enqueue_admin_assets' );

function emi_enqueue_frontend_assets() {
    wp_enqueue_style( 'emi-style', EMI_PLUGIN_URL . 'assets/css/emi-style.css' );
    wp_enqueue_script( 'emi-scripts', EMI_PLUGIN_URL . 'assets/js/emi-scripts.js', array('jquery'), null, true );

    wp_localize_script( 'emi-scripts', 'emi_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'emi_frontend_nonce' ),
    ) );
}

function emi_enqueue_admin_assets( $hook ) {
    // Only enqueue when relevant admin pages are loaded or for our plugin pages
    wp_enqueue_style( 'emi-admin-style', EMI_PLUGIN_URL . 'assets/css/emi-style.css' );
}

// Shortcode
add_shortcode( 'emi_calculator_form', 'emi_render_shortcode' );
function emi_render_shortcode( $atts ) {
    ob_start();
    include EMI_PLUGIN_DIR . 'templates/emi-form.php';
    return ob_get_clean();
}
