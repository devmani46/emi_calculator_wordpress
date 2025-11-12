<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AJAX endpoints:
 * - calculate EMI -> emi_calculate_ajax
 * - submit application -> emi_submit_ajax
 */

add_action( 'wp_ajax_nopriv_emi_calculate', 'emi_calculate_ajax' );
add_action( 'wp_ajax_emi_calculate', 'emi_calculate_ajax' );

function emi_calculate_ajax() {
    check_ajax_referer( 'emi_frontend_nonce', 'nonce' );

    $principal = floatval( $_POST['price'] ?? 0 );
    $annual_rate = floatval( $_POST['interest'] ?? 0 );
    $tenure_months = intval( $_POST['tenure'] ?? 0 );

    if ( $principal <= 0 || $tenure_months <= 0 ) {
        wp_send_json_error( 'Invalid input' );
    }

    // Monthly rate
    $r = ( $annual_rate / 100 ) / 12;
    if ( $r == 0 ) {
        $emi = $principal / $tenure_months;
    } else {
        $emi = $principal * $r * pow( 1 + $r, $tenure_months ) / ( pow( 1 + $r, $tenure_months ) - 1 );
    }

    $total_payable = $emi * $tenure_months;
    $total_interest = $total_payable - $principal;

    wp_send_json_success( array(
        'monthly_emi'    => round( $emi, 2 ),
        'total_payable'  => round( $total_payable, 2 ),
        'total_interest' => round( $total_interest, 2 ),
    ) );
}

// Submission handler
add_action( 'wp_ajax_nopriv_emi_submit', 'emi_submit_ajax' );
add_action( 'wp_ajax_emi_submit', 'emi_submit_ajax' );

function emi_submit_ajax() {
    check_ajax_referer( 'emi_frontend_nonce', 'nonce' );

    $name  = sanitize_text_field( $_POST['name'] ?? '' );
    $phone = sanitize_text_field( $_POST['phone'] ?? '' );
    $email = sanitize_email( $_POST['email'] ?? '' );
    $tenure = intval( $_POST['tenure'] ?? 0 );
    $bike_term_id = intval( $_POST['bike_model'] ?? 0 );

    if ( empty( $name ) || empty( $phone ) || empty( $email ) || $tenure <= 0 || $bike_term_id <= 0 ) {
        wp_send_json_error( 'Missing required fields' );
    }

    // Fetch bike meta (price and interest rate) from taxonomy term meta
    $price = get_term_meta( $bike_term_id, 'emi_price', true );
    $interest = get_term_meta( $bike_term_id, 'emi_interest', true );

    if ( empty( $price ) || $price <= 0 ) {
        wp_send_json_error( 'Invalid bike model data' );
    }

    // Calculate EMI
    $r = ( $interest / 100 ) / 12;
    if ( $r == 0 ) {
        $emi_val = $price / $tenure;
    } else {
        $emi_val = $price * $r * pow( 1 + $r, $tenure ) / ( pow( 1 + $r, $tenure ) - 1 );
    }

    // Create post
    $post_id = wp_insert_post( array(
        'post_title' => sanitize_text_field( $name ) . ' - ' . sanitize_text_field( $phone ),
        'post_type' => 'emi_applications',
        'post_status' => 'publish',
    ) );

    if ( is_wp_error( $post_id ) || $post_id <= 0 ) {
        wp_send_json_error( 'Failed to create application' );
    }

    // Set taxonomy
    wp_set_post_terms( $post_id, array( $bike_term_id ), 'bike_models' );

    // Handle documents: expects files[] named inputs
    $uploaded_documents = array();
    if ( ! empty( $_FILES ) ) {
        // Temporarily change upload dir to /uploads/emi-documents/
        add_filter( 'upload_dir', 'emi_custom_upload_dir' );
        foreach ( $_FILES as $key => $filearray ) {
            if ( is_array( $filearray['name'] ) ) {
                // multi-file support
                $count = count( $filearray['name'] );
                for ( $i = 0; $i < $count; $i++ ) {
                    $file = array(
                        'name'     => $filearray['name'][$i],
                        'type'     => $filearray['type'][$i],
                        'tmp_name' => $filearray['tmp_name'][$i],
                        'error'    => $filearray['error'][$i],
                        'size'     => $filearray['size'][$i]
                    );
                    $move = wp_handle_upload( $file, array( 'test_form' => false ) );
                    if ( ! isset( $move['error'] ) ) {
                        $filename = basename( $move['file'] );
                        $uploaded_documents[] = array( 'label' => sanitize_file_name($file['name']), 'url' => $move['url'], 'file' => $filename );
                    }
                }
            } else {
                $file = $filearray;
                $move = wp_handle_upload( $file, array( 'test_form' => false ) );
                if ( ! isset( $move['error'] ) ) {
                    $filename = basename( $move['file'] );
                    $uploaded_documents[] = array( 'label' => sanitize_file_name($file['name']), 'url' => $move['url'], 'file' => $filename );
                }
            }
        }
        remove_filter( 'upload_dir', 'emi_custom_upload_dir' );
    }

    // Store meta
    update_post_meta( $post_id, 'emi_customer_name', $name );
    update_post_meta( $post_id, 'emi_phone', $phone );
    update_post_meta( $post_id, 'emi_email', $email );
    update_post_meta( $post_id, 'emi_bike_term_id', $bike_term_id );
    update_post_meta( $post_id, 'emi_tenure', $tenure );
    update_post_meta( $post_id, 'emi_price', floatval( $price ) );
    update_post_meta( $post_id, 'emi_interest', floatval( $interest ) );
    update_post_meta( $post_id, 'emi_monthly_emi', round( $emi_val, 2 ) );
    update_post_meta( $post_id, 'emi_documents', $uploaded_documents );
    update_post_meta( $post_id, 'emi_status', 'Pending' );

    // Create .htaccess in upload dir to block direct access
    emi_create_htaccess();

    // Send SMS to customer
    $settings = get_option( 'emi_settings', array() );
    $sms_template = $settings['sms_template'] ?? 'Thank you {name}. Your EMI application (ID: {id}) is received.';
    $message = str_replace( array('{name}','{id}'), array( $name, $post_id ), $sms_template );
    emi_send_sms( $phone, $message );

    wp_send_json_success( array(
        'message' => 'Application submitted successfully',
        'id' => $post_id
    ) );
}

// Custom upload dir for emi-documents
function emi_custom_upload_dir( $dirs ) {
    $custom_subdir = '/emi-documents';
    $dirs['path'] = $dirs['basedir'] . $custom_subdir;
    $dirs['url']  = $dirs['baseurl'] . $custom_subdir;
    $dirs['subdir'] = $custom_subdir;
    if ( ! file_exists( $dirs['path'] ) ) {
        wp_mkdir_p( $dirs['path'] );
    }
    return $dirs;
}

function emi_create_htaccess() {
    $upload_dir = wp_upload_dir();
    $base = trailingslashit( $upload_dir['basedir'] ) . 'emi-documents/';
    if ( ! file_exists( $base ) ) {
        wp_mkdir_p( $base );
    }

    $htaccess = $base . '.htaccess';
    $content = "Order deny,allow\nDeny from all\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n";
    if ( ! file_exists( $htaccess ) ) {
        file_put_contents( $htaccess, $content );
    }
}
