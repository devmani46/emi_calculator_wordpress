<?php
/**
* Plugin Name: EMI Calculator
* Description: This plugin allows you to Create EMI Calculator.
* Version: 1.1
* Copyright: 2023
* Text Domain: emi-calculator
* License: GPLv3 or later
*/

// Include function files
include_once('backend/backend.php');
include_once('frontend/frontend.php');
include_once('default.php');

add_action( 'wp_enqueue_scripts', 'EMI_calculator_loadScriptStyle' );

function EMI_calculator_loadScriptStyle() {
    // Get file modification times for versioning (cache busting)
    $emi_calc_js_version = filemtime( plugin_dir_path( __FILE__ ) . 'frontend/assets/js/emi_calc.js' );
    $rangeslider_js_version = filemtime( plugin_dir_path( __FILE__ ) . 'frontend/assets/js/rangeSlider.min.js' );
    $chart_js_version = filemtime( plugin_dir_path( __FILE__ ) . 'frontend/assets/js/chart.js' );
    $emi_calc_css_version = filemtime( plugin_dir_path( __FILE__ ) . 'frontend/assets/css/emi_calc.css' );
    $rangeslider_css_version = filemtime( plugin_dir_path( __FILE__ ) . 'frontend/assets/css/rangeslider.min.css' );

    // Enqueue scripts and styles with versioning
    wp_enqueue_script( 'jquery-emi-calculator', plugins_url( 'frontend/assets/js/emi_calc.js', __FILE__ ), array('jquery'), $emi_calc_js_version, true );
    wp_enqueue_style( 'emi_calc_css', plugins_url( 'frontend/assets/css/emi_calc.css', __FILE__ ), false, $emi_calc_css_version );
    wp_enqueue_script( 'rangeslider-min-js', plugins_url( 'frontend/assets/js/rangeSlider.min.js', __FILE__ ), array('jquery'), $rangeslider_js_version, true );
    wp_enqueue_style( 'rangeslider-css', plugins_url( 'frontend/assets/css/rangeslider.min.css', __FILE__ ), false, $rangeslider_css_version );
    wp_enqueue_script( 'jquery-calculator-chart', plugins_url( 'frontend/assets/js/chart.js', __FILE__ ), array('jquery'), $chart_js_version, true );

    // Localized variables to be used in the JavaScript
    $emi_color_var = array(
         'emi_title' => emi_get_setting('emi_title'),
        'emi_principal_chart_color' => emi_get_setting('emi_principal_amount_color'),
        'emi_intereset_chart_color' => emi_get_setting('emi_intereset_amount_color'),
        'emi_calc_chart_type' =>emi_get_setting('emi_chart_type'),
        'emi_calc_with_chart' => emi_get_setting('emi_enable_chart'),
        'emi_principal_chart_text' => emi_get_setting('principal_amou_text'),
        'emi_interest_chart_text' => emi_get_setting('interest_amou_text'),
        'emi_min_loan_amount' => emi_get_setting('min_loan_amount'),
        'emi_max_loan_amount' => emi_get_setting('max_loan_amount'),
        'emi_min_interest_rate' => emi_get_setting('min_interest_rate'),
        'emi_max_interest_rate' => emi_get_setting('max_interest_rate'),
        'yearly_min_loan_term' => emi_get_setting('min_year_loan_term'),
        'yearly_max_loan_term' => emi_get_setting('max_year_loan_term'),
        'monthly_min_loan_term' => emi_get_setting('min_month_loan_term'),
        'monthly_max_loan_term' => emi_get_setting('max_month_loan_term'),
    );

    wp_localize_script( 'jquery-emi-calculator', 'emi_calc_style', $emi_color_var );
}
