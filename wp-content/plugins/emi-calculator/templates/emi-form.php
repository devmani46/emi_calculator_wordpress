<?php
// Query bike models (terms) with their price and interest meta
$terms = get_terms( array( 'taxonomy' => 'bike_models', 'hide_empty' => false ) );
$settings = get_option( 'emi_settings', array() );
$tenure_raw = $settings['tenures'] ?? '6,12,24';
$tenures = array_map( 'intval', array_filter( array_map( 'trim', explode( ',', $tenure_raw ) ) ) );

?>
<div id="emi-calculator-wrap" class="emi-calculator">
    <form id="emi-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field( 'emi_frontend_nonce', 'emi_nonce' ); ?>
        <div class="field">
            <label>Bike Model</label>
            <select name="bike_model" id="emi-bike-model" required>
                <option value=""><?php _e( 'Select a model', 'emi-calculator' ); ?></option>
                <?php foreach ( $terms as $t ) : 
                    $price = get_term_meta( $t->term_id, 'emi_price', true );
                    $interest = get_term_meta( $t->term_id, 'emi_interest', true );
                ?>
                    <option value="<?php echo esc_attr( $t->term_id ); ?>" data-price="<?php echo esc_attr( $price ); ?>" data-interest="<?php echo esc_attr( $interest ); ?>">
                        <?php echo esc_html( $t->name ); ?> - <?php echo number_format_i18n( floatval( $price ), 2 ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Tenure (months)</label>
            <select name="tenure" id="emi-tenure" required>
                <option value=""><?php _e( 'Select tenure', 'emi-calculator' ); ?></option>
                <?php foreach ( $tenures as $t ) : ?>
                    <option value="<?php echo intval( $t ); ?>"><?php echo intval( $t ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <button type="button" id="emi-calc-btn"><?php _e( 'Calculate EMI', 'emi-calculator' ); ?></button>
        </div>

        <div id="emi-results" style="display:none;">
            <p><strong><?php _e( 'Monthly EMI:', 'emi-calculator' ); ?></strong> <span id="emi-monthly"></span></p>
            <p><strong><?php _e( 'Total Payable:', 'emi-calculator' ); ?></strong> <span id="emi-total"></span></p>
            <p><strong><?php _e( 'Total Interest:', 'emi-calculator' ); ?></strong> <span id="emi-interest"></span></p>
        </div>

        <hr/>

        <div class="field">
            <label><?php _e( 'Name', 'emi-calculator' ); ?></label>
            <input type="text" name="name" required />
        </div>

        <div class="field">
            <label><?php _e( 'Phone', 'emi-calculator' ); ?></label>
            <input type="text" name="phone" required />
        </div>

        <div class="field">
            <label><?php _e( 'Email', 'emi-calculator' ); ?></label>
            <input type="email" name="email" required />
        </div>

        <div id="emi-document-fields">
            <?php
            // Show document requirements if present
            $docs_json = $settings['document_requirements'] ?? '["ID Proof","Address Proof","Income Proof"]';
            $docs = json_decode( $docs_json, true );
            if ( ! is_array( $docs ) ) $docs = array('ID Proof','Address Proof');
            foreach ( $docs as $idx => $d ) {
                echo '<div class="field"><label>' . esc_html( $d ) . '</label><input type="file" name="documents[]" required /></div>';
            }
            ?>
        </div>

        <div class="field">
            <button type="submit" id="emi-submit-btn"><?php _e( 'Submit Application', 'emi-calculator' ); ?></button>
        </div>

        <div id="emi-form-messages" role="status"></div>
    </form>
</div>
