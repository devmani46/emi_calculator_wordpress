<?php
/**
 * Register CPT: emi_applications
 * Register Taxonomy: bike_models (with term meta for price & interest)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Hook registration
add_action( 'init', 'emi_register_cpt_and_tax', 0 );

function emi_register_cpt_and_tax() {

    /**
     * --------------------------
     * Custom Post Type: EMI Applications
     * --------------------------
     */
    $labels = array(
        'name'               => __( 'EMI Applications', 'emi-calculator' ),
        'singular_name'      => __( 'EMI Application', 'emi-calculator' ),
        'menu_name'          => __( 'EMI Applications', 'emi-calculator' ),
        'add_new'            => __( 'Add New', 'emi-calculator' ),
        'add_new_item'       => __( 'Add New EMI Application', 'emi-calculator' ),
        'edit_item'          => __( 'Edit EMI Application', 'emi-calculator' ),
        'view_item'          => __( 'View EMI Application', 'emi-calculator' ),
        'all_items'          => __( 'All EMI Applications', 'emi-calculator' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => false, // handled in custom admin menu
        'supports'           => array( 'title' ),
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'has_archive'        => false,
        'rewrite'            => false,
    );

    register_post_type( 'emi_applications', $args );

    /**
     * --------------------------
     * Custom Taxonomy: Bike Models
     * --------------------------
     */
    $labels = array(
        'name'              => __( 'Bike Models', 'emi-calculator' ),
        'singular_name'     => __( 'Bike Model', 'emi-calculator' ),
        'search_items'      => __( 'Search Bike Models', 'emi-calculator' ),
        'all_items'         => __( 'All Bike Models', 'emi-calculator' ),
        'edit_item'         => __( 'Edit Bike Model', 'emi-calculator' ),
        'update_item'       => __( 'Update Bike Model', 'emi-calculator' ),
        'add_new_item'      => __( 'Add New Bike Model', 'emi-calculator' ),
        'new_item_name'     => __( 'New Bike Model Name', 'emi-calculator' ),
        'menu_name'         => __( 'Bike Models', 'emi-calculator' ),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_menu'      => true, // ensures it appears in sidebar
        'rewrite'           => false,
        'capabilities'      => array(
            'manage_terms' => 'manage_categories',
            'edit_terms'   => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts',
        ),
    );

    register_taxonomy( 'bike_models', array( 'emi_applications' ), $args );
}

/**
 * --------------------------
 * Add Custom Meta Fields to Bike Models (Price & Interest Rate)
 * --------------------------
 */

add_action( 'bike_models_add_form_fields', 'emi_bike_model_add_fields' );
add_action( 'bike_models_edit_form_fields', 'emi_bike_model_edit_fields', 10, 2 );
add_action( 'created_bike_models', 'emi_save_bike_model_meta' );
add_action( 'edited_bike_models', 'emi_save_bike_model_meta' );

function emi_bike_model_add_fields() {
    ?>
    <div class="form-field">
        <label for="emi_price"><?php _e( 'Price (numeric)', 'emi-calculator' ); ?></label>
        <input type="number" name="emi_price" id="emi_price" value="" required />
        <p class="description"><?php _e( 'Enter the base price for this bike model.', 'emi-calculator' ); ?></p>
    </div>
    <div class="form-field">
        <label for="emi_interest"><?php _e( 'Interest Rate (annual %)', 'emi-calculator' ); ?></label>
        <input type="number" step="0.01" name="emi_interest" id="emi_interest" value="" required />
        <p class="description"><?php _e( 'Annual interest rate e.g. 10.5', 'emi-calculator' ); ?></p>
    </div>
    <?php
}

function emi_bike_model_edit_fields( $term, $taxonomy ) {
    $price    = get_term_meta( $term->term_id, 'emi_price', true );
    $interest = get_term_meta( $term->term_id, 'emi_interest', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="emi_price"><?php _e( 'Price (numeric)', 'emi-calculator' ); ?></label></th>
        <td>
            <input type="number" name="emi_price" id="emi_price" value="<?php echo esc_attr( $price ); ?>" required />
            <p class="description"><?php _e( 'Enter the base price for this bike model.', 'emi-calculator' ); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="emi_interest"><?php _e( 'Interest Rate (annual %)', 'emi-calculator' ); ?></label></th>
        <td>
            <input type="number" step="0.01" name="emi_interest" id="emi_interest" value="<?php echo esc_attr( $interest ); ?>" required />
            <p class="description"><?php _e( 'Annual interest rate e.g. 10.5', 'emi-calculator' ); ?></p>
        </td>
    </tr>
    <?php
}

function emi_save_bike_model_meta( $term_id ) {
    if ( isset( $_POST['emi_price'] ) ) {
        update_term_meta( $term_id, 'emi_price', floatval( $_POST['emi_price'] ) );
    }
    if ( isset( $_POST['emi_interest'] ) ) {
        update_term_meta( $term_id, 'emi_interest', floatval( $_POST['emi_interest'] ) );
    }
}

/**
 * --------------------------
 * Ensure Bike Models appear in main admin menu (top-level)
 * --------------------------
 */
add_action( 'admin_menu', 'emi_force_show_bike_models_menu' );

function emi_force_show_bike_models_menu() {
    global $submenu;
    if ( isset( $submenu['edit-tags.php?taxonomy=bike_models'] ) ) {
        // Already visible, do nothing
        return;
    }
    add_menu_page(
        __( 'Bike Models', 'emi-calculator' ),
        __( 'Bike Models', 'emi-calculator' ),
        'manage_categories',
        'edit-tags.php?taxonomy=bike_models',
        '',
        'dashicons-buddicons-replies',
        25
    );
}
