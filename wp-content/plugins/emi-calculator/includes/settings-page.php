<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings page to manage:
 * - SMS API credentials and template
 * - Tenure options
 * - Document requirements
 */

add_action( 'admin_menu', 'emi_register_settings_page' );
add_action( 'admin_init', 'emi_register_settings' );

function emi_register_settings_page() {
    add_menu_page(
        __( 'EMI Settings', 'emi-calculator' ),
        __( 'EMI Settings', 'emi-calculator' ),
        'manage_emi_settings',
        'emi-settings',
        'emi_render_settings_page',
        'dashicons-money',
        56
    );
}

function emi_register_settings() {
    register_setting( 'emi_settings_group', 'emi_settings', array( 'sanitize_callback' => 'emi_sanitize_settings' ) );

    add_settings_section( 'emi_general_section', __( 'General', 'emi-calculator' ), function(){ echo '<p>' . __( 'General EMI plugin settings', 'emi-calculator' ) . '</p>'; }, 'emi-settings' );

    add_settings_field( 'tenures', __( 'Tenure options (months)', 'emi-calculator' ), 'emi_field_tenures', 'emi-settings', 'emi_general_section' );
    add_settings_field( 'sms_api', __( 'SMS API Configuration', 'emi-calculator' ), 'emi_field_sms_api', 'emi-settings', 'emi_general_section' );
    add_settings_field( 'document_requirements', __( 'Document Requirements (JSON array)', 'emi-calculator' ), 'emi_field_docs', 'emi-settings', 'emi_general_section' );
}

function emi_sanitize_settings( $input ) {
    $output = array();
    $output['tenures'] = isset( $input['tenures'] ) ? sanitize_text_field( $input['tenures'] ) : '';
    $output['sms_api_url'] = isset( $input['sms_api_url'] ) ? esc_url_raw( $input['sms_api_url'] ) : '';
    $output['sms_api_key'] = isset( $input['sms_api_key'] ) ? sanitize_text_field( $input['sms_api_key'] ) : '';
    $output['sms_sender_id'] = isset( $input['sms_sender_id'] ) ? sanitize_text_field( $input['sms_sender_id'] ) : '';
    $output['sms_template'] = isset( $input['sms_template'] ) ? sanitize_textarea_field( $input['sms_template'] ) : 'Thank you {name}. Your EMI application (ID: {id}) is received.';
    $output['document_requirements'] = isset( $input['document_requirements'] ) ? wp_kses_post( $input['document_requirements'] ) : '["ID Proof","Address Proof","Income Proof"]';
    return $output;
}

function emi_field_tenures() {
    $opts = get_option( 'emi_settings', array() );
    $val = isset( $opts['tenures'] ) ? $opts['tenures'] : '6,12,24';
    echo '<input type="text" name="emi_settings[tenures]" value="' . esc_attr( $val ) . '" class="regular-text" />';
    echo '<p class="description">Comma separated tenure months e.g. 6,12,24</p>';
}

function emi_field_sms_api() {
    $opts = get_option( 'emi_settings', array() );
    $url = isset( $opts['sms_api_url'] ) ? $opts['sms_api_url'] : '';
    $key = isset( $opts['sms_api_key'] ) ? $opts['sms_api_key'] : '';
    $sender = isset( $opts['sms_sender_id'] ) ? $opts['sms_sender_id'] : '';
    $template = isset( $opts['sms_template'] ) ? $opts['sms_template'] : 'Thank you {name}. Your EMI application (ID: {id}) is received.';

    echo '<input type="text" name="emi_settings[sms_api_url]" value="' . esc_attr( $url ) . '" placeholder="https://smsprovider.example/send" class="regular-text" /><br/>';
    echo '<input type="text" name="emi_settings[sms_api_key]" value="' . esc_attr( $key ) . '" placeholder="API Key" class="regular-text" /><br/>';
    echo '<input type="text" name="emi_settings[sms_sender_id]" value="' . esc_attr( $sender ) . '" placeholder="SENDERID" class="regular-text" />';
    echo '<p class="description">Use {name} and {id} variables in the template.</p>';
    echo '<textarea name="emi_settings[sms_template]" rows="4" cols="60">' . esc_textarea( $template ) . '</textarea>';
}

function emi_field_docs() {
    $opts = get_option( 'emi_settings', array() );
    $docs = isset( $opts['document_requirements'] ) ? $opts['document_requirements'] : '["ID Proof","Address Proof"]';
    echo '<textarea name="emi_settings[document_requirements]" rows="6" cols="60">' . esc_textarea( $docs ) . '</textarea>';
    echo '<p class="description">JSON array of strings: e.g. ["ID Proof","Address Proof"]</p>';
}

function emi_render_settings_page() {
    if ( ! current_user_can( 'manage_emi_settings' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }
    ?>
    <div class="wrap">
        <h1><?php _e( 'EMI Settings', 'emi-calculator' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'emi_settings_group' );
            do_settings_sections( 'emi-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
