<?php
add_action( 'admin_menu', 'emi_calculator_generator_admin_menu' );
add_action( 'admin_init', 'emi_calculator_generator_settings' );

function emi_calculator_generator_admin_menu() {
    add_menu_page(
        'Emi Calculator', // page <title>Title</title>
        'Emi Calculator', // menu link text
        'manage_options', // capability to access the page
        'emi_calculator_generator', // page URL slugs
        'emi_calculator_generator_page', // callback function /w content
        'dashicons-calculator', // menu icon
        14
    );
}

function emi_calculator_generator_page() {
    ?>
    <div class="wrap">
        <h1>EMI Calculator Settings</h1>

        <div  class="support-banner-main">
                <div class="support-banner">
                    <h2>Need Help?</h2>
                    <p>If you need assistance configuring the EMI Calculator settings, please visit our <a href="https://appcalculate.com/contact-us/" target="_blank" >Support Page</a>.</p>
                </div>
            
                <div  class="support-banner">
                    <h3>Shortcode Information</h3>
                    <p>You can use the following shortcode to display the plugin on any page or post:</p>
                    <pre>[emi_calculator]</pre>
                    <p>Simply copy and paste the above shortcode where you want the calculator to appear.</p>
                </div>
            </div>
        <h2 class="nav-tab-wrapper">
            <a href="#general_settings" class="nav-tab nav-tab-active" data-tab="general_settings">General Settings</a>
            <a href="#translation_settings" class="nav-tab" data-tab="translation_settings">Translation Settings</a>
            <a href="#appearance_settings" class="nav-tab" data-tab="appearance_settings">Appearance Settings</a>
        </h2>
        <form method="post" action="options.php">
            <?php
             settings_fields( 'emi_calculator_generator' );
            // do_settings_sections( 'emi_calculator_generator' );
            // submit_button();

             ?>

            <!-- General Settings -->
            <div id="general_settings" class="tab-content" style="display: block;">
                <?php do_settings_sections('emi_calculator_settings_general'); ?>
            </div>

            <!-- Appearance Settings -->
            <div id="appearance_settings" class="tab-content" style="display: none;">
                <?php do_settings_sections('emi_calculator_settings_appearance'); ?>
            </div>
           <!-- Translation Settings -->
             <div id="translation_settings" class="tab-content" style="display: none;">
                <?php do_settings_sections('emi_calculator_settings_translation'); ?>
            </div>

            <?php submit_button('Save Changes', 'primary');
            ?>
        </form>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll(".nav-tab");
            const tabContents = document.querySelectorAll(".tab-content");

            tabs.forEach(tab => {
                tab.addEventListener("click", function(e) {
                    e.preventDefault();

                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove("nav-tab-active"));
                    tab.classList.add("nav-tab-active");

                    // Hide all tab contents
                    tabContents.forEach(content => content.style.display = "none");

                    // Show the selected tab content
                    const selectedTab = tab.getAttribute("data-tab");
                    document.getElementById(selectedTab).style.display = "block";
                });
            });
        });
    </script>
    <style>
        .tab-content { margin-top: 20px; }
        .nav-tab-wrapper { border-bottom: 1px solid #ccc; }
        .nav-tab-active { color: #0073aa; }
         /* Optional: Styling for the support banner */
         .support-banner-main{
            display: flex;
            gap: 20px;
         }
        .support-banner {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 0;
            font-size: 16px;
        }

        .support-banner a {
            color: #0073aa;
        }
    </style>

    </div>
    
    <?php
}

// Register settings and fields
function emi_calculator_generator_settings() {
    // Register the settings
    register_setting( 'emi_calculator_generator', 'emi_title' );
    register_setting( 'emi_calculator_generator', 'emi_body_back_color' );
    register_setting( 'emi_calculator_generator', 'emi_from_back_color' );
    register_setting( 'emi_calculator_generator', 'emi_result_back_color' );
    register_setting( 'emi_calculator_generator', 'emi_int_symb_back_color' );
    register_setting( 'emi_calculator_generator', 'emi_intf_border_color' );
    register_setting( 'emi_calculator_generator', 'emi_chart_type' );
    register_setting( 'emi_calculator_generator', 'emi_principal_amount_color' );
    register_setting( 'emi_calculator_generator', 'emi_intereset_amount_color' );
    register_setting( 'emi_calculator_generator', 'emi_slider_activ_color' );
    register_setting( 'emi_calculator_generator', 'emi_slider_progress_color' );
    register_setting( 'emi_calculator_generator', 'emi_slider_thumb_color' );
    register_setting( 'emi_calculator_generator', 'emi_enable_chart' );
    register_setting( 'emi_calculator_generator', 'loan_emi_text' );
    register_setting( 'emi_calculator_generator', 'total_intereset_text' );
    register_setting( 'emi_calculator_generator', 'total_payment_text' );
    register_setting( 'emi_calculator_generator', 'principal_amou_text' );
    register_setting( 'emi_calculator_generator', 'interest_amou_text' );
    register_setting( 'emi_calculator_generator', 'min_loan_amount' );
    register_setting( 'emi_calculator_generator', 'max_loan_amount' );
    register_setting( 'emi_calculator_generator', 'min_interest_rate' );
    register_setting( 'emi_calculator_generator', 'max_interest_rate' );
    register_setting( 'emi_calculator_generator', 'min_year_loan_term' );
    register_setting( 'emi_calculator_generator', 'max_year_loan_term' );
    register_setting( 'emi_calculator_generator', 'min_month_loan_term' );
    register_setting( 'emi_calculator_generator', 'max_month_loan_term' );
    register_setting( 'emi_calculator_generator', 'emi_intfield_title_color' );
    register_setting( 'emi_calculator_generator', 'default_loan_amount' );
    register_setting( 'emi_calculator_generator', 'default_interest_rate' );
    register_setting( 'emi_calculator_generator', 'default_year_loan_term' );
    register_setting( 'emi_calculator_generator', 'default_month_loan_term' );
    register_setting( 'emi_calculator_generator', 'emi_currency_symbol' );

    // Add settings sections and fields
    add_settings_section( 'emi_calculator_general_section', 'General Settings', null, 'emi_calculator_settings_general' );
    add_settings_section( 'emi_calculator_appearance_section', 'Apperence Settings', null, 'emi_calculator_settings_appearance' );
    add_settings_section( 'emi_calculator_translation_section', 'Translation Settings', null, 'emi_calculator_settings_translation' );
    
    add_settings_section( 'emi_calculator_color_section', 'Color Settings', null, 'emi_calculator_settings_appearance' );
    // Body Background Color
    add_settings_field( 'emi_body_back_color', 'Body Background Color', 'emi_body_back_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_color_section' );
    // Form Background Color
    add_settings_field( 'emi_from_back_color', 'Form Background Color', 'emi_from_back_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_color_section' );
    // Result Background Colore
    add_settings_field( 'emi_result_back_color', 'Result Background Color', 'emi_result_back_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_color_section' );
    // Input Field Symbol Background Color
    add_settings_field( 'emi_int_symb_back_color', 'Input Field Symbol Background Color', 'emi_int_symb_back_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_color_section' );
    // Input Field Border Color
    add_settings_field( 'emi_intf_border_color', 'Input Field Border Color', 'emi_intf_border_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_color_section' );
    //text color
    add_settings_field( 'emi_intfield_title_color', 'Text Color', 'emi_intf_title_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_color_section' );

    
    // Chart Style
    add_settings_section( 'emi_calculator_chart_section', 'Chart Style', null, 'emi_calculator_settings_appearance' );
    add_settings_field( 'emi_chart_type', 'Select Chart Type', 'emi_chart_type_field', 'emi_calculator_settings_appearance', 'emi_calculator_chart_section' );
    add_settings_field( 'emi_principal_amount_color', 'Principal Amount Color', 'emi_principal_amount_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_chart_section' );
    add_settings_field( 'emi_intereset_amount_color', 'Interest Amount Color', 'emi_intereset_amount_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_chart_section' );

    // Slider Style
    add_settings_section( 'emi_calculator_slider_section', 'Slider Style', null, 'emi_calculator_settings_appearance' );
    add_settings_field( 'emi_slider_activ_color', 'Slider Active Color', 'emi_slider_activ_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_slider_section' );
    add_settings_field( 'emi_slider_progress_color', 'Slider Progress Color', 'emi_slider_progress_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_slider_section' );
    add_settings_field( 'emi_slider_thumb_color', 'Slider Thumb Color', 'emi_slider_thumb_color_field', 'emi_calculator_settings_appearance', 'emi_calculator_slider_section' );

    // Result Display Options
    add_settings_section( 'emi_calculator_result_section', 'Result Display Options', null, 'emi_calculator_settings_appearance' );
    add_settings_field( 'emi_enable_chart', 'Display Result with Chart', 'emi_enable_chart_field', 'emi_calculator_settings_appearance', 'emi_calculator_result_section' );

    // Text Settings
    add_settings_section( 'emi_calculator_text_section', '', null, 'emi_calculator_settings_translation' );
    add_settings_field( 'emi_title', 'Calculator Title', 'loan_emi_title_field', 'emi_calculator_settings_translation', 'emi_calculator_text_section' );
    add_settings_field( 'loan_emi_text', 'Loan EMI Text', 'loan_emi_text_field', 'emi_calculator_settings_translation', 'emi_calculator_text_section' );
    add_settings_field( 'total_intereset_text', 'Total Interest Text', 'total_intereset_text_field', 'emi_calculator_settings_translation', 'emi_calculator_text_section' );
    add_settings_field( 'total_payment_text', 'Total Payment Text', 'total_payment_text_field', 'emi_calculator_settings_translation', 'emi_calculator_text_section' );
    add_settings_field( 'principal_amou_text', 'Principal Amount Text', 'principal_amou_text_field', 'emi_calculator_settings_translation', 'emi_calculator_text_section' );
    add_settings_field( 'interest_amou_text', 'Interest Amount Text', 'interest_amou_text_field', 'emi_calculator_settings_translation', 'emi_calculator_text_section' );

    // Form Settings
    add_settings_section( 'emi_calculator_form_section', '', null, 'emi_calculator_settings_general' );
    add_settings_field( 'default_loan_amount', 'Default Loan Amount', 'default_loan_amount_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );

    add_settings_field( 'emi_currency_symbol', 'Currency Symbol', 'emi_currency_symbol_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'min_loan_amount', 'Minimum Loan Amount', 'min_loan_amount_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'max_loan_amount', 'Maximum Loan Amount', 'max_loan_amount_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'default_interest_rate', 'Default Interest Rate', 'default_interest_rate_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'min_interest_rate', 'Minimum Interest Rate', 'min_interest_rate_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'max_interest_rate', 'Maximum Interest Rate', 'max_interest_rate_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'min_year_loan_term', 'Minimum Year Loan Term', 'min_year_loan_term_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'default_year_loan_term', 'Default Year Loan Term', 'default_year_loan_term_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'max_year_loan_term', 'Maximum Year Loan Term', 'max_year_loan_term_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    // add_settings_field( 'default_month_loan_term', 'Default Month Loan Term', 'default_month_loan_term_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'min_month_loan_term', 'Minimum Month Loan Term', 'min_month_loan_term_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );
    add_settings_field( 'max_month_loan_term', 'Maximum Month Loan Term', 'max_month_loan_term_field', 'emi_calculator_settings_general', 'emi_calculator_form_section' );      
}

// Define the field display functions
function emi_body_back_color_field() {
    $value = emi_get_setting( 'emi_body_back_color' );
    echo '<input type="color" name="emi_body_back_color" value="' . esc_attr( $value ) . '" class="color-picker" data-default-color="#ffffff" data-alpha-enabled="true">';
}

function emi_from_back_color_field() {
    $value = emi_get_setting( 'emi_from_back_color');
    echo '<input type="color" name="emi_from_back_color" value="' . esc_attr( $value ) . '" class="color-picker" data-default-color="#ffffff" data-alpha-enabled="true">';
}

function emi_result_back_color_field() {
    $value = emi_get_setting( 'emi_result_back_color' );
    echo '<input type="color" name="emi_result_back_color" value="' . esc_attr( $value ) . '" class="color-picker" data-default-color="#ffffff" data-alpha-enabled="true">';
}

function emi_int_symb_back_color_field() {
    $value = emi_get_setting( 'emi_int_symb_back_color');
    echo '<input type="color" name="emi_int_symb_back_color" value="' . esc_attr( $value ) . '" class="color-picker" data-default-color="#ffffff" data-alpha-enabled="true">';
}

function emi_intf_border_color_field() {
    $value = emi_get_setting( 'emi_intf_border_color');
    echo '<input type="color" name="emi_intf_border_color" value="' . esc_attr( $value ) . '" class="color-picker" data-default-color="#000000" data-alpha-enabled="true">';
}


function emi_intf_title_color_field() {
    $value = emi_get_setting( 'emi_intfield_title_color');
    echo '<input type="color" name="emi_intfield_title_color" value="' . esc_attr( $value ) . '" class="color-picker" data-default-color="#000000" data-alpha-enabled="true">';
}


// Define chart type radio buttons
function emi_chart_type_field() {
    $value = emi_get_setting( 'emi_chart_type');
    echo '<input type="radio" name="emi_chart_type" value="doughnut_chart" ' . checked( 'doughnut_chart', $value, false ) . '> Doughnut Chart ';
    echo '<input type="radio" name="emi_chart_type" value="bar_chart" ' . checked( 'bar_chart', $value, false ) . '> Bar Chart ';
    echo '<input type="radio" name="emi_chart_type" value="pie_chart" ' . checked( 'pie_chart', $value, false ) . '> Pie Chart';
    echo '<input type="radio" name="emi_chart_type" value="polar_area_chart" ' . checked( 'polar_area_chart', $value, false ) . '> Polar Area Chart';
}

function emi_principal_amount_color_field() {
    $value = emi_get_setting( 'emi_principal_amount_color');
    echo '<input type="color" name="emi_principal_amount_color" value="' . esc_attr( $value ) . '" class="color-picker"  data-alpha-enabled="true">';
}

function emi_intereset_amount_color_field() {
    $value = emi_get_setting( 'emi_intereset_amount_color');
    echo '<input type="color" name="emi_intereset_amount_color" value="' . esc_attr( $value ) . '" class="color-picker"  data-alpha-enabled="true">';
}

function emi_slider_activ_color_field() {
    $value = emi_get_setting( 'emi_slider_activ_color');
    echo '<input type="color" name="emi_slider_activ_color" value="' . esc_attr( $value ) . '" class="color-picker"  data-alpha-enabled="true">';
}

function emi_slider_progress_color_field() {
    $value = emi_get_setting( 'emi_slider_progress_color');
    echo '<input type="color" name="emi_slider_progress_color" value="' . esc_attr( $value ) . '" class="color-picker"  data-alpha-enabled="true">';
}

function emi_slider_thumb_color_field() {
    $value = emi_get_setting( 'emi_slider_thumb_color');
    echo '<input type="color" name="emi_slider_thumb_color" value="' . esc_attr( $value ) . '" class="color-picker"  data-alpha-enabled="true">';
}

function emi_enable_chart_field() {
    $value = emi_get_setting( 'emi_enable_chart');
    echo '<input type="checkbox" name="emi_enable_chart" value="yes" ' . checked( 'yes', $value, false ) . '> Display Result With Chart ';
}

function loan_emi_text_field() {
    $value = emi_get_setting( 'loan_emi_text');
    echo '<div style="display: flex;">';
    echo '<input type="text" name="loan_emi_text" value="' . esc_attr( $value ) . '" disabled>';
    echo '<input type="hidden"  name="loan_emi_text" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p>This option is available in <a href="https://appcalculate.com/product/emi-calculator/" target="_blank">Pro Version</a>. </p>';
    echo '</div>'; 
}

function emi_currency_symbol_field() {
    $value = emi_get_setting( 'emi_currency_symbol');
    echo '<input type="text" name="emi_currency_symbol" value="' . esc_attr( $value ) . '">';
}

function loan_emi_title_field() {
    $value = emi_get_setting( 'emi_title');
    echo '<div style="display: flex;">';
    echo '<input type="text" name="emi_title" value="' . esc_attr( $value ) . '"disabled>';
    echo '<input type="hidden"  name="emi_title" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p>This option is available in <a href="https://appcalculate.com/product/emi-calculator/" target="_blank">Pro Version</a>. </p>';
    echo '</div>'; 
}

function total_intereset_text_field() {
    $value = emi_get_setting( 'total_intereset_text');
    echo '<div style="display: flex;">';
    echo '<input type="text" name="total_intereset_text" value="' . esc_attr( $value ) . '"disabled>';
    echo '<input type="hidden"  name="total_intereset_text" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p>This option is available in <a href="https://appcalculate.com/product/emi-calculator/" target="_blank">Pro Version</a>. </p>';
    echo '</div>'; 
}

function total_payment_text_field() {
    $value = emi_get_setting( 'total_payment_text');
    echo '<div style="display: flex;">';
    echo '<input type="text" name="total_payment_text" value="' . esc_attr( $value ) . '" disabled>';
    echo '<input type="hidden"  name="total_payment_text" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p>This option is available in <a href="https://appcalculate.com/product/emi-calculator/" target="_blank">Pro Version</a>. </p>';
    echo '</div>'; 
}

function principal_amou_text_field() {
    $value = emi_get_setting( 'principal_amou_text');
    echo '<div style="display: flex;">';
    echo '<input type="text" name="principal_amou_text" value="' . esc_attr( $value ) . '"disabled>';
    echo '<input type="hidden"  name="principal_amou_text" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p>This option is available in <a href="https://appcalculate.com/product/emi-calculator/" target="_blank">Pro Version</a>. </p>';
    echo '</div>'; 
}

function interest_amou_text_field() {
    $value = emi_get_setting( 'interest_amou_text');
    echo '<div style="display: flex;">';
    echo '<input type="text" name="interest_amou_text" value="' . esc_attr( $value ) . '"disabled>';
    echo '<input type="hidden"  name="interest_amou_text" value="' . esc_attr( $value ) . '" class="regular-text" />';
    echo '<p>This option is available in <a href="https://appcalculate.com/product/emi-calculator/" target="_blank">Pro Version</a>. </p>';
    echo '</div>'; 
}

function default_loan_amount_field() {
    $value = emi_get_setting( 'default_loan_amount');
    echo '<input type="number" name="default_loan_amount" value="' . esc_attr( $value ) . '">';
}


function min_loan_amount_field() {
    $value = emi_get_setting( 'min_loan_amount');
    echo '<input type="number" name="min_loan_amount" value="' . esc_attr( $value ) . '">';
}

function max_loan_amount_field() {
    $value = emi_get_setting( 'max_loan_amount');
    echo '<input type="number" name="max_loan_amount" value="' . esc_attr( $value ) . '">';
}

function default_interest_rate_field() {
    $value = emi_get_setting( 'default_interest_rate');
    echo '<input type="number" name="default_interest_rate" value="' . esc_attr( $value ) . '" step="0.1">';
}

function min_interest_rate_field() {
    $value = emi_get_setting( 'min_interest_rate');
    echo '<input type="number" name="min_interest_rate" value="' . esc_attr( $value ) . '" step="0.1">';
}

function max_interest_rate_field() {
    $value = emi_get_setting( 'max_interest_rate');
    echo '<input type="number" name="max_interest_rate" value="' . esc_attr( $value ) . '" step="0.1">';
}

function default_year_loan_term_field() {
    $value = emi_get_setting('default_year_loan_term');
    echo '<input type="number" name="default_year_loan_term" value="' . esc_attr( $value ) . '">';
}

function min_year_loan_term_field() {
    $value = emi_get_setting( 'min_year_loan_term');
    echo '<input type="number" name="min_year_loan_term" value="' . esc_attr( $value ) . '">';
}

function max_year_loan_term_field() {
    $value = emi_get_setting( 'max_year_loan_term');
    echo '<input type="number" name="max_year_loan_term" value="' . esc_attr( $value ) . '">';
}

function default_month_loan_term_field() {
    $value = emi_get_setting( 'default_month_loan_term');
    echo '<input type="number" name="default_month_loan_term" value="' . esc_attr( $value ) . '">';
}

function min_month_loan_term_field() {
    $value = emi_get_setting( 'min_month_loan_term');
    echo '<input type="number" name="min_month_loan_term" value="' . esc_attr( $value ) . '">';
}

function max_month_loan_term_field() {
    $value =emi_get_setting( 'max_month_loan_term');
    echo '<input type="number" name="max_month_loan_term" value="' . esc_attr( $value ) . '">';
}
