<?php
global $emi_default_settings;
$emi_default_settings = [
    'emi_body_back_color' => '#ffffff',
    'emi_title' => 'EMI Calculator',
    'emi_from_back_color'=> '#ffffff',
    'emi_result_back_color'=> '#ffffff',
    'emi_intfield_title_color'=> '#000000',
    'emi_int_symb_back_color'=> '#d6d6d6',
    'emi_intf_border_color'=> '#d6d6d6',
    'emi_slider_activ_color'=> '#2a78c0',
    'emi_slider_progress_color'=> '#e6e6e6',
    'emi_slider_thumb_color'=> '#2a78c0',
    'emi_enable_chart'=> 'yes',
    'loan_emi_text'=> 'Loan EMI',
    'total_intereset_text' => 'Total Interest Payable',
    'total_payment_text' => 'Total Payment',
    'min_loan_amount' => '5000',
    'max_loan_amount' => '10000000',
    'min_interest_rate' => '1',
    'max_interest_rate' => '30',
    'min_year_loan_term' => '1',
    'max_year_loan_term' => '30',
    'min_month_loan_term' => '6',
    'max_month_loan_term' => '30',
    'emi_chart_type' => 'pie_chart',
    'emi_principal_amount_color'=> '#2d629a',
    'emi_intereset_amount_color' =>  '#c8daee',
    'principal_amou_text' => 'Principal Amount',
    'interest_amou_text' => 'Interest Amount',
    'default_loan_amount' => '50000',
    'default_interest_rate' => '15',
    'default_year_loan_term' => '5',
    'default_month_loan_term' => '10',
    'emi_currency_symbol' => '$',


   
  
];
function emi_get_setting($key) {
    global $emi_default_settings;
    return get_option($key, $emi_default_settings[$key]);
}