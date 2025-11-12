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

// Edit application page
function emi_admin_edit_application_page() {
    if ( ! current_user_can( 'edit_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    if ( ! isset( $_GET['app_id'] ) ) {
        wp_die( __( 'Application ID not provided', 'emi-calculator' ) );
    }

    $app_id = intval( $_GET['app_id'] );
    $post = get_post( $app_id );

    if ( ! $post || $post->post_type !== 'emi_applications' ) {
        wp_die( __( 'Application not found', 'emi-calculator' ) );
    }

    // Handle form submission
    if ( isset( $_POST['emi_status_nonce'] ) && wp_verify_nonce( $_POST['emi_status_nonce'], 'emi_save_status_nonce' ) ) {
        $current_user = wp_get_current_user();
        $can_edit = in_array( 'administrator', $current_user->roles ) || in_array( 'emi_crm', $current_user->roles );

        if ( $can_edit && isset( $_POST['emi_status'] ) ) {
            update_post_meta( $app_id, '_emi_status', sanitize_text_field( $_POST['emi_status'] ) );
            echo '<div class="updated notice"><p>' . __( 'Status updated successfully!', 'emi-calculator' ) . '</p></div>';
        }
    }

    // Get current status
    $status = get_post_meta( $app_id, '_emi_status', true );
    $meta = get_post_meta( $app_id );
    $bike_terms = wp_get_post_terms( $app_id, 'bike_models' );
    $bike_label = ! empty( $bike_terms ) ? $bike_terms[0]->name : '-';
    $documents = isset( $meta['emi_documents'] ) ? maybe_unserialize( $meta['emi_documents'][0] ) : array();
    $current_user = wp_get_current_user();
    $can_edit = in_array( 'administrator', $current_user->roles ) || in_array( 'emi_crm', $current_user->roles );

    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $meta['emi_customer_name'][0] ?? 'Application' ) . ' - ' . esc_html( $meta['emi_phone'][0] ?? '' ); ?></h1>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div>
                <h2><?php _e( 'Application Details', 'emi-calculator' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Application ID:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $app_id ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Customer Name:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_customer_name'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Phone:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_phone'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Email:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_email'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Bike Model:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $bike_label ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Monthly EMI:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_monthly_emi'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Tenure (months):', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_tenure'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Submitted:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( get_the_date( 'Y-m-d H:i:s', $post ) ); ?></td>
                    </tr>
                </table>

                <h2><?php _e( 'Documents', 'emi-calculator' ); ?></h2>
                <?php if ( ! empty( $documents ) && is_array( $documents ) ) : ?>
                    <ul>
                    <?php foreach ( $documents as $doc ) : ?>
                        <li>
                            <?php echo esc_html( $doc['label'] ?? 'Document' ); ?> - 
                            <a href="<?php echo esc_url( add_query_arg( 'action', 'emi_download_document', add_query_arg( array( 'file' => $doc['file'], 'post' => $app_id ), admin_url( 'admin-post.php' ) ) ) ); ?>">
                                <?php _e( 'Download', 'emi-calculator' ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php _e( 'No documents uploaded', 'emi-calculator' ); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php _e( 'Application Status', 'emi-calculator' ); ?></h2>
                    </div>
                    <div class="inside">
                        <form method="post">
                            <?php wp_nonce_field( 'emi_save_status_nonce', 'emi_status_nonce' ); ?>
                            
                            <label for="emi_status"><strong><?php _e( 'Current Status:', 'emi-calculator' ); ?></strong></label><br><br>
                            <?php if ( $can_edit ) : ?>
                                <select name="emi_status" id="emi_status" style="width: 100%; padding: 8px;">
                                    <option value="Under Review" <?php selected( $status, 'Under Review' ); ?>>Under Review</option>
                                    <option value="Approved" <?php selected( $status, 'Approved' ); ?>>Approved</option>
                                    <option value="Rejected" <?php selected( $status, 'Rejected' ); ?>>Rejected</option>
                                </select>
                                <br><br>
                                <?php submit_button( __( 'Save Status', 'emi-calculator' ), 'primary' ); ?>
                            <?php else : ?>
                                <p><strong><?php echo esc_html( $status ? $status : 'Under Review' ); ?></strong></p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=emi-applications' ) ); ?>" class="button">
                <?php _e( '← Back to Applications', 'emi-calculator' ); ?>
            </a>
        </p>
    </div>
    <?php
}

// Quick Edit application page
function emi_admin_quick_edit_application_page() {
    if ( ! current_user_can( 'edit_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    if ( ! isset( $_GET['app_id'] ) ) {
        wp_die( __( 'Application ID not provided', 'emi-calculator' ) );
    }

    $app_id = intval( $_GET['app_id'] );
    $post = get_post( $app_id );

    if ( ! $post || $post->post_type !== 'emi_applications' ) {
        wp_die( __( 'Application not found', 'emi-calculator' ) );
    }

    // Handle form submission
    if ( isset( $_POST['emi_status_nonce'] ) && wp_verify_nonce( $_POST['emi_status_nonce'], 'emi_save_status_nonce' ) ) {
        $current_user = wp_get_current_user();
        $can_edit = in_array( 'administrator', $current_user->roles ) || in_array( 'emi_crm', $current_user->roles );

        if ( $can_edit && isset( $_POST['emi_status'] ) ) {
            update_post_meta( $app_id, '_emi_status', sanitize_text_field( $_POST['emi_status'] ) );
            echo '<div class="updated notice"><p>' . __( 'Status updated successfully!', 'emi-calculator' ) . '</p></div>';
        }
    }

    $status = get_post_meta( $app_id, '_emi_status', true );
    $meta = get_post_meta( $app_id );
    $current_user = wp_get_current_user();
    $can_edit = in_array( 'administrator', $current_user->roles ) || in_array( 'emi_crm', $current_user->roles );

    ?>
    <div class="wrap">
        <h1><?php _e( 'Quick Edit Application', 'emi-calculator' ); ?> #<?php echo esc_html( $app_id ); ?></h1>
        
        <div style="max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px;">
            <h2><?php echo esc_html( $meta['emi_customer_name'][0] ?? 'Application' ); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th><label><?php _e( 'Customer:', 'emi-calculator' ); ?></label></th>
                    <td><?php echo esc_html( $meta['emi_customer_name'][0] ?? '' ); ?></td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Phone:', 'emi-calculator' ); ?></label></th>
                    <td><?php echo esc_html( $meta['emi_phone'][0] ?? '' ); ?></td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Email:', 'emi-calculator' ); ?></label></th>
                    <td><?php echo esc_html( $meta['emi_email'][0] ?? '' ); ?></td>
                </tr>
            </table>

            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field( 'emi_save_status_nonce', 'emi_status_nonce' ); ?>
                
                <label for="emi_status" style="display: block; margin-bottom: 10px;"><strong><?php _e( 'Status:', 'emi-calculator' ); ?></strong></label>
                <?php if ( $can_edit ) : ?>
                    <select name="emi_status" id="emi_status" style="width: 100%; padding: 8px; margin-bottom: 15px;">
                        <option value="Under Review" <?php selected( $status, 'Under Review' ); ?>>Under Review</option>
                        <option value="Approved" <?php selected( $status, 'Approved' ); ?>>Approved</option>
                        <option value="Rejected" <?php selected( $status, 'Rejected' ); ?>>Rejected</option>
                    </select>
                    <?php submit_button( __( 'Save Status', 'emi-calculator' ), 'primary', 'submit', true ); ?>
                <?php else : ?>
                    <p><strong><?php echo esc_html( $status ? $status : 'Under Review' ); ?></strong></p>
                <?php endif; ?>
            </form>
        </div>

        <p style="text-align: center;">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=emi-applications' ) ); ?>" class="button">
                <?php _e( '← Back to Applications', 'emi-calculator' ); ?>
            </a>
        </p>
    </div>
    <?php
}

// View application page (read-only)
function emi_admin_view_application_page() {
    if ( ! current_user_can( 'edit_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    if ( ! isset( $_GET['app_id'] ) ) {
        wp_die( __( 'Application ID not provided', 'emi-calculator' ) );
    }

    $app_id = intval( $_GET['app_id'] );
    $post = get_post( $app_id );

    if ( ! $post || $post->post_type !== 'emi_applications' ) {
        wp_die( __( 'Application not found', 'emi-calculator' ) );
    }

    $meta = get_post_meta( $app_id );
    $bike_terms = wp_get_post_terms( $app_id, 'bike_models' );
    $bike_label = ! empty( $bike_terms ) ? $bike_terms[0]->name : '-';
    $documents = isset( $meta['emi_documents'] ) ? maybe_unserialize( $meta['emi_documents'][0] ) : array();
    $status = get_post_meta( $app_id, '_emi_status', true );

    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $meta['emi_customer_name'][0] ?? 'Application' ) . ' - ' . esc_html( $meta['emi_phone'][0] ?? '' ); ?></h1>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div>
                <h2><?php _e( 'Application Details (Read-Only)', 'emi-calculator' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label><?php _e( 'Application ID:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $app_id ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Customer Name:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_customer_name'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Phone:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_phone'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Email:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_email'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Bike Model:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $bike_label ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Monthly EMI:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_monthly_emi'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Tenure (months):', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( $meta['emi_tenure'][0] ?? '' ); ?></td>
                    </tr>
                    <tr>
                        <th><label><?php _e( 'Submitted:', 'emi-calculator' ); ?></label></th>
                        <td><?php echo esc_html( get_the_date( 'Y-m-d H:i:s', $post ) ); ?></td>
                    </tr>
                </table>

                <h2><?php _e( 'Documents', 'emi-calculator' ); ?></h2>
                <?php if ( ! empty( $documents ) && is_array( $documents ) ) : ?>
                    <ul>
                    <?php foreach ( $documents as $doc ) : ?>
                        <li>
                            <?php echo esc_html( $doc['label'] ?? 'Document' ); ?> - 
                            <a href="<?php echo esc_url( add_query_arg( 'action', 'emi_download_document', add_query_arg( array( 'file' => $doc['file'], 'post' => $app_id ), admin_url( 'admin-post.php' ) ) ) ); ?>">
                                <?php _e( 'Download', 'emi-calculator' ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php _e( 'No documents uploaded', 'emi-calculator' ); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php _e( 'Application Status', 'emi-calculator' ); ?></h2>
                    </div>
                    <div class="inside">
                        <p><strong><?php _e( 'Current Status:', 'emi-calculator' ); ?></strong></p>
                        <p style="font-size: 18px; color: #0073aa; margin: 10px 0;">
                            <?php echo esc_html( $status ? $status : 'Under Review' ); ?>
                        </p>
                        <p style="margin-top: 20px;">
                            <a href="<?php echo esc_url( add_query_arg( 'action', 'quick_edit', add_query_arg( 'app_id', $app_id, admin_url( 'admin.php?page=emi-applications' ) ) ) ); ?>" class="button button-primary">
                                <?php _e( 'Edit Status', 'emi-calculator' ); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=emi-applications' ) ); ?>" class="button">
                <?php _e( '← Back to Applications', 'emi-calculator' ); ?>
            </a>
        </p>
    </div>
    <?php
}

// Delete application
function emi_admin_delete_application() {
    if ( ! current_user_can( 'delete_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    if ( ! isset( $_GET['app_id'] ) || ! isset( $_GET['_wpnonce'] ) ) {
        wp_die( __( 'Invalid request', 'emi-calculator' ) );
    }

    if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'emi_delete_application_nonce' ) ) {
        wp_die( __( 'Security check failed', 'emi-calculator' ) );
    }

    $app_id = intval( $_GET['app_id'] );
    $post = get_post( $app_id );

    if ( ! $post || $post->post_type !== 'emi_applications' ) {
        wp_die( __( 'Application not found', 'emi-calculator' ) );
    }

    // Delete the post
    wp_delete_post( $app_id, true );

    // Redirect back to applications list
    wp_safe_remote_post( admin_url( 'admin.php?page=emi-applications' ) );
    wp_redirect( admin_url( 'admin.php?page=emi-applications' ) );
    exit;
}

// Admin listing page
function emi_admin_applications_page() {
    if ( ! current_user_can( 'edit_emi_applications' ) ) {
        wp_die( __( 'Permission denied', 'emi-calculator' ) );
    }

    // If editing an application
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' ) {
        emi_admin_edit_application_page();
        return;
    }

    // If quick editing an application
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'quick_edit' ) {
        emi_admin_quick_edit_application_page();
        return;
    }

    // If viewing an application
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'view' ) {
        emi_admin_view_application_page();
        return;
    }

    // If deleting an application
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' ) {
        emi_admin_delete_application();
        return;
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
                    <th><?php _e('Actions', 'emi-calculator'); ?></th>
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
                    <td><?php echo esc_html( $meta['_emi_status'][0] ?? 'Under Review' ); ?></td>
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
                    <td>
                        <a href="<?php echo esc_url( add_query_arg( 'action', 'edit', add_query_arg( 'app_id', $app->ID, admin_url( 'admin.php?page=emi-applications' ) ) ) ); ?>" class="button button-small button-primary">
                            <?php _e( 'Edit', 'emi-calculator' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'action', 'quick_edit', add_query_arg( 'app_id', $app->ID, admin_url( 'admin.php?page=emi-applications' ) ) ) ); ?>" class="button button-small">
                            <?php _e( 'Quick Edit', 'emi-calculator' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'action', 'view', add_query_arg( 'app_id', $app->ID, admin_url( 'admin.php?page=emi-applications' ) ) ) ); ?>" class="button button-small">
                            <?php _e( 'View', 'emi-calculator' ); ?>
                        </a>
                        <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', add_query_arg( 'app_id', $app->ID, admin_url( 'admin.php?page=emi-applications' ) ) ), 'emi_delete_application_nonce' ) ); ?>" class="button button-small button-secondary" onclick="return confirm('<?php _e( 'Are you sure you want to delete this application?', 'emi-calculator' ); ?>');">
                            <?php _e( 'Delete', 'emi-calculator' ); ?>
                        </a>
                    </td>
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
