<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add CRM menu, list table and actions for applications
 */

add_action( 'admin_menu', 'emi_admin_menu' );

function emi_admin_menu() {
    add_menu_page(
        __( 'EMI Applications', 'emi-calculator' ),
        __( 'EMI Applications', 'emi-calculator' ),
        'edit_emi_applications',
        'emi-applications',
        'emi_admin_applications_page',
        'dashicons-list-view',
        57
    );

    add_submenu_page( 'emi-applications', __( 'Settings', 'emi-calculator' ), __( 'Settings', 'emi-calculator' ), 'manage_emi_settings', 'emi-settings', 'emi_render_settings_page' );
}

// Admin listing page
function emi_admin_applications_page() {
    if ( ! current_user_can( 'edit_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    // Query latest applications (with optional filters)
    $args = array(
        'post_type' => 'emi_applications',
        'posts_per_page' => 20,
        'post_status' => 'any',
    );

    if ( isset( $_GET['bike_model'] ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'bike_models',
                'field'    => 'term_id',
                'terms'    => intval( $_GET['bike_model'] ),
            ),
        );
    }

    $apps = get_posts( $args );

    ?>
    <div class="wrap">
        <h1><?php _e( 'EMI Applications', 'emi-calculator' ); ?></h1>

        <form method="get" style="margin-bottom:20px;">
            <input type="hidden" name="page" value="emi-applications" />
            <?php
            $terms = get_terms( array( 'taxonomy' => 'bike_models', 'hide_empty' => false ) );
            echo '<select name="bike_model"><option value="">' . __('All Bikes', 'emi-calculator') . '</option>';
            foreach ( $terms as $t ) {
                $selected = ( isset( $_GET['bike_model'] ) && intval($_GET['bike_model']) === intval($t->term_id) ) ? 'selected' : '';
                echo "<option value='{$t->term_id}' {$selected}>" . esc_html( $t->name ) . "</option>";
            }
            echo '</select> ';
            submit_button( __( 'Filter' ), 'secondary', '', false );
            ?>
        </form>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'emi-calculator'); ?></th>
                    <th><?php _e('Customer', 'emi-calculator'); ?></th>
                    <th><?php _e('Phone / Email', 'emi-calculator'); ?></th>
                    <th><?php _e('Bike Model', 'emi-calculator'); ?></th>
                    <th><?php _e('EMI (monthly)', 'emi-calculator'); ?></th>
                    <th><?php _e('Tenure', 'emi-calculator'); ?></th>
                    <th><?php _e('Status', 'emi-calculator'); ?></th>
                    <th><?php _e('Documents', 'emi-calculator'); ?></th>
                    <th><?php _e('Submitted', 'emi-calculator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $apps as $app ) : 
                    $meta = get_post_meta( $app->ID );
                    $bike_terms = wp_get_post_terms( $app->ID, 'bike_models' );
                    $bike_label = ! empty( $bike_terms ) ? $bike_terms[0]->name : '-';
                    $documents = isset( $meta['emi_documents'] ) ? maybe_unserialize( $meta['emi_documents'][0] ) : array();
                ?>
                <tr>
                    <td><?php echo esc_html( $app->ID ); ?></td>
                    <td><?php echo esc_html( $meta['emi_customer_name'][0] ?? '' ); ?></td>
                    <td><?php echo esc_html( $meta['emi_phone'][0] ?? '' ) . '<br/>' . esc_html( $meta['emi_email'][0] ?? '' ); ?></td>
                    <td><?php echo esc_html( $bike_label ); ?></td>
                    <td><?php echo esc_html( $meta['emi_monthly_emi'][0] ?? '' ); ?></td>
                    <td><?php echo esc_html( $meta['emi_tenure'][0] ?? '' ); ?></td>
                    <td><?php echo esc_html( $meta['emi_status'][0] ?? 'Pending' ); ?></td>
                    <td>
                        <?php
                        if ( ! empty( $documents ) && is_array( $documents ) ) {
                            foreach ( $documents as $k => $url ) {
                                $label = isset($url['label']) ? $url['label'] : "Doc";
                                $file_url = esc_url( $url['url'] );
                                $download_link = admin_url( 'admin-post.php?action=emi_download_document&file=' . rawurlencode( $url['file'] ) . '&post=' . $app->ID );
                                echo '<a href="' . esc_url( $download_link ) . '">' . esc_html( $label ) . '</a><br/>';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td><?php echo esc_html( get_the_date( '', $app ) ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Serve uploaded documents securely - only for authorized users
add_action( 'admin_post_emi_download_document', 'emi_download_document' );
function emi_download_document() {
    if ( ! current_user_can( 'read_emi_applications' ) && ! current_user_can( 'edit_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    if ( empty( $_GET['file'] ) || empty( $_GET['post'] ) ) {
        wp_die( __( 'Invalid request', 'emi-calculator' ) );
    }

    $file = sanitize_text_field( wp_unslash( $_GET['file'] ) );
    $post_id = intval( $_GET['post'] );

    // Construct upload directory path
    $upload_dir = wp_upload_dir();
    $base = trailingslashit( $upload_dir['basedir'] ) . 'emi-documents/';

    $path = realpath( $base . $file );

    // Security checks
    if ( ! $path || strpos( $path, realpath( $base ) ) !== 0 ) {
        wp_die( __( 'Invalid file', 'emi-calculator' ) );
    }

    if ( ! file_exists( $path ) ) {
        wp_die( __( 'File not found', 'emi-calculator' ) );
    }

    // Serve file
    header( 'Content-Description: File Transfer' );
    header( 'Content-Type: application/octet-stream' );
    header( 'Content-Disposition: attachment; filename=' . basename( $path ) );
    header( 'Content-Length: ' . filesize( $path ) );
    readfile( $path );
    exit;
}
