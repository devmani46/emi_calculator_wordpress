<?php
add_shortcode( 'emi_calculator', 'emi_calculator_create' );
function emi_calculator_create( $atts ) {
    ob_start();
	$emi_title = emi_get_setting('emi_title');
    $emi_body_back_color = emi_get_setting('emi_body_back_color');
    $emi_from_back_color = emi_get_setting('emi_from_back_color');
    $emi_result_back_color = emi_get_setting('emi_result_back_color');
    $emi_intfield_title_color = emi_get_setting('emi_intfield_title_color');
    $emi_int_symb_back_color = emi_get_setting('emi_int_symb_back_color');
    $emi_intf_border_color = emi_get_setting('emi_intf_border_color');
    $emi_slider_activ_color = emi_get_setting('emi_slider_activ_color');
    $emi_slider_progress_color = emi_get_setting('emi_slider_progress_color');
    $emi_slider_thumb_color = emi_get_setting('emi_slider_thumb_color');
    $emi_enable_chart = emi_get_setting('emi_enable_chart');
    $loan_emi_text = emi_get_setting('loan_emi_text');
    $total_intereset_text = emi_get_setting('total_intereset_text');
    $total_payment_text = emi_get_setting('total_payment_text');
    $min_loan_amount = emi_get_setting('min_loan_amount');
    $max_loan_amount = emi_get_setting('max_loan_amount');
    $min_interest_rate = emi_get_setting('min_interest_rate');
    $max_interest_rate = emi_get_setting('max_interest_rate');
    $min_year_loan_term = emi_get_setting('min_year_loan_term');
    $max_year_loan_term = emi_get_setting('max_year_loan_term');
    $min_month_loan_term = emi_get_setting('min_month_loan_term');
    $max_month_loan_term = emi_get_setting('max_month_loan_term');
	$default_loan_amount = emi_get_setting('default_loan_amount');
	$default_interest_rate = emi_get_setting('default_interest_rate');
	$default_year_loan_term = emi_get_setting('default_year_loan_term');
	$default_month_loan_term = emi_get_setting('default_month_loan_term');
	$emi_currency_symbol = emi_get_setting('emi_currency_symbol');

	

	?>
	<style type="text/css">
		.emi_calculator_info {
			background-color: <?php echo esc_attr($emi_body_back_color); ?>;
		}
		.emi_calculator_col {
			background-color: <?php echo esc_attr($emi_from_back_color); ?>;
		}
		.emi_calculator_col2 {
			background-color: <?php echo esc_attr($emi_result_back_color); ?>;
		}
		.emi_calculator_info label {
			color: <?php echo esc_attr($emi_intfield_title_color); ?> !important;
		}
		.emi_input_group_symbol span, .emi-tenure-radio input[type=radio]:not(old):checked+.form-label {
			background-color: <?php echo esc_attr($emi_int_symb_back_color); ?>;
		}
		input.form-control, .emi_input_group_symbol span, .emi-tenure-radio input[type=radio]:not(old)+.form-label {
			border-color: <?php echo esc_attr($emi_intf_border_color); ?> !important;
		}
		#emi_range .rangeslider__fill {
	    background: <?php echo esc_attr($emi_slider_activ_color); ?>;
		}
		#emi_range .rangeslider {
	    background: <?php echo esc_attr($emi_slider_progress_color); ?>;
		}
		#emi_range .rangeslider__handle {
	    background: <?php echo esc_attr($emi_slider_thumb_color); ?>;
		}
	</style>
	<h1 class="heading"  id="primecap"><?php echo esc_attr($emi_title); ?></h1>
		<div class="emi_calculator_info">
		
			<div class="emi_calculator_col">
			
			        <div class="emi_error_msg">
			          <span class="text-danger" id="emi_msg"></span>
			        </div>
			        <div class="emi_loan_field">
									<label class="emi_control_label" for="loanamount"><?php echo esc_html('Loan Amount','emi-calculator'); ?></label>
							    <div class="emi_form_group">
							        <div class="emi_input_group">
							            <input type="number" id="emi_loan_amount" class="form-control" placeholder="Loan Amount" value="<?php echo esc_attr($default_loan_amount); ?>">
							            <div class="emi_input_group_symbol">
							                <span class="input-group-text"><?php echo esc_html($emi_currency_symbol,'emi-calculator'); ?></span>
							            </div>
							        </div>
							    </div>
							</div>
							<div id="emi_range">
								<input type="range" id="emi_loanamount_slider" value="<?php echo esc_attr($default_loan_amount); ?>" min="<?php echo esc_attr($min_loan_amount); ?>" max="<?php echo esc_attr($max_loan_amount); ?>" step="1">
							</div>

							<div class="emi_loan_field">
									<label class="emi_control_label" for="loaninterest"><?php echo esc_html('Interest Rate','emi-calculator'); ?></label>
							    <div class="emi_form_group">
							        <div class="emi_input_group">
							            <input type="number" id="emi_interest_rate" class="form-control" placeholder="Interest Rate" value="<?php echo esc_attr($default_interest_rate); ?>" min="<?php echo esc_attr($min_interest_rate); ?>" max="<?php echo esc_attr($max_interest_rate); ?>">
							            <div class="emi_input_group_symbol">
							                <span class="input-group-text"><?php echo esc_html('%','emi-calculator'); ?></span>
							            </div>
							        </div>
							    </div>
							</div>
							<div id="emi_range">
								<input type="range" id="emi_interest_rate_slider" value="<?php echo esc_attr($default_interest_rate); ?>" min="<?php echo esc_attr($min_interest_rate); ?>" max="<?php echo esc_attr($max_interest_rate); ?>">
							</div>

							<div class="emi_loan_field">
									<label class="emi_control_label" for="loanterm"><?php echo esc_html('Loan Tenure','emi-calculator'); ?></label>
							    <div class="emi_form_group">
							        <div class="emi_input_group">
							            <input type="number" name="emi_tenure_year" id="emi_tenure" class="form-control" value="<?php echo esc_html($default_year_loan_term); ?>">
										
							            
							            <div class="tenure-choice">
                              <div class="emi-tenure-radio">
                                  <input type="radio" name="emi_months_years" id="years" value="years" checked="checked">
                                  <label for="years" class="form-label form-label1">
                                      <span class="icon-name"><?php echo esc_html('Yr','emi-calculator'); ?></span>
                                  </label>
                              </div>
                              <div class="emi-tenure-radio">
                                  <input type="radio" name="emi_months_years" id="months" value="months">
                                  <label for="months" class="form-label form-label2">
                                      <span class="icon-name"><?php echo esc_html('Mo','emi-calculator'); ?></span>
                                  </label>
                              </div>
                          </div>
							        </div>
							    </div>
							</div>
							<div id="emi_range" class="year_emi">
								<input type="range" id="emi_year_tenure_slider" value="<?php echo esc_attr($default_year_loan_term); ?>" min="<?php echo esc_attr($min_year_loan_term); ?>" max="<?php echo esc_attr($max_year_loan_term); ?>">
							</div>
							<div id="emi_range" class="month_emi">
								<input type="range" id="emi_month_tenure_slider" value="<?php echo esc_attr($min_month_loan_term);?>" min="<?php echo esc_attr($min_month_loan_term); ?>" max="<?php echo esc_attr($max_month_loan_term); ?>">
							</div>
			</div>

				<div class="emi_calculator_col2">
	        <div class="emi_calculator_result">
	        	<div id="emi_payment_summary">
	        		<div class="emi_align_center" id="emiamount">
	        			<h4><?php echo esc_attr($loan_emi_text); ?></h4>
	        			<p><span id="result_emi"></span></p>
	        		</div>
	        		<div class="emi_align_center" id="emitotalinterest">
	        			<h4><?php echo esc_attr($total_intereset_text); ?> </h4>
	        			<p><span id="total_interest"></span></p>
	        		</div>
	        		<div class="emi_align_center" id="emitotalamount" class="column-last">
	        			<h4><?php echo esc_attr($total_payment_text); ?><br><?php echo esc_html('(Principal + Interest)','emi-calculator'); ?></h4>
	        			<p><span id="total_payments"></span></p>
	        		</div>
	        	</div>
	        	<?php if($emi_enable_chart == 'yes'){ ?>
		        	<div id="emi_chart_summery" class="emi_chart_box">
						<div class="emi_chart">
		            	<canvas id="myChart" width="400" height="400"></canvas>
		            </div>
		          </div>
	          <?php }else{?>
	          	<style type="text/css">
					.emi_calculator_result {
						display: block;
					}
					#emi_payment_summary {
						display: flex;
					justify-content: space-around;
					max-width: 100%;
					}
					@media only screen and (max-width: 768px) {
						#emi_payment_summary {
						flex-direction: column;
						}
					}
				</style>
	          <?php }?>
	        </div>
				</div>
		</div>
	<?php
	$content = ob_get_clean();
	return $content;
}
