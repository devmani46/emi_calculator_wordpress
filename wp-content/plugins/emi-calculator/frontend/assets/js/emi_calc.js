jQuery(document).ready(function(event) {
  loadResult();

  function emi_Graph() { 
    var emi_months_years = jQuery("input[name='emi_months_years']:checked").val();
    var currency = 'INR';
    var month;
    if (emi_months_years == 'years') {
      months = jQuery('#emi_tenure').val();
      var month = months*12;
    } else {
      var month = jQuery('#emi_tenure').val();
    }

    var rate = jQuery("#emi_interest_rate").val();
    var min_interest_rate = parseFloat(emi_calc_style.emi_min_interest_rate);
    // jQuery("#emi_interest_rate").attr("value", min_interest_rate);
    // jQuery("#emi_interest_rate").on("change", function() {
      
    // if(rate < min_interest_rate){
    //     jQuery(this).val(min_interest_rate);
    //   }
    // });

    var pamt = jQuery("#emi_loan_amount").val();
    var pamt1 = parseFloat(pamt);
    var monthlyInterestRatio = (rate/100)/12;
    var monthlyInterest = (monthlyInterestRatio * pamt);
    var top = Math.pow((1 + monthlyInterestRatio), month);
    var emi = ((pamt * monthlyInterestRatio) * (top/(top-1)));
    var result_emi = emi.toFixed(0);
    var totalAmount = (emi * month).toFixed(0);
    var total_interest = (totalAmount - pamt).toFixed(0);
    var total_interestRounded = parseFloat((totalAmount - pamt).toFixed(0));

  if(emi_calc_style.emi_calc_with_chart == 'yes'){
    if(emi_calc_style.emi_calc_chart_type == 'doughnut_chart'){
      var ctx = document.getElementById('myChart').getContext('2d');
      emi_charts = new Chart(ctx, {
          type: "doughnut",
          data: {
            labels: [emi_calc_style.emi_principal_chart_text,emi_calc_style.emi_interest_chart_text],
            datasets: [{
            data: [
                    [pamt], 
                    [total_interest]
                  ],
              backgroundColor: [
                emi_calc_style.emi_principal_chart_color,
                emi_calc_style.emi_intereset_chart_color
              ],
              borderColor: [
                emi_calc_style.emi_principal_chart_color,
                emi_calc_style.emi_intereset_chart_color
              ],
              borderWidth: 3
            }]
          },
          options: {
            plugins: {
              legend: {
                labels: {
                    font: {
                        size: 14
                    }
                }
              }
            }, 
            cutout:140,
            responsive: true,
            maintainAspectRatio: false,
          }
       
        });
      }else if(emi_calc_style.emi_calc_chart_type == 'bar_chart'){
        var ctx = document.getElementById('myChart').getContext('2d');
        emi_charts = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Amount in Rs'],
            datasets: [{
                label: emi_calc_style.emi_principal_chart_text,
                data: [pamt],
                backgroundColor: emi_calc_style.emi_principal_chart_color,
              },
              {
                label: emi_calc_style.emi_interest_chart_text,
                data: [total_interest],
                backgroundColor: emi_calc_style.emi_intereset_chart_color,
              }
            ]
          },
        });
      }else if(emi_calc_style.emi_calc_chart_type == 'pie_chart'){
        var ctx = document.getElementById('myChart').getContext('2d');
        var emiData = {
            labels: [emi_calc_style.emi_principal_chart_text,emi_calc_style.emi_interest_chart_text],
            datasets: [
                {
                    data: [pamt, total_interest],
                    backgroundColor: [
                        emi_calc_style.emi_principal_chart_color,
                        emi_calc_style.emi_intereset_chart_color
                    ],
                    borderColor:  [emi_calc_style.emi_principal_chart_color, emi_calc_style.emi_intereset_chart_color],
                    borderWidth: [3,3]
                }]
        };
        emi_charts = new Chart(ctx, {
          type: 'pie',
          data: emiData
        });
      }else if(emi_calc_style.emi_calc_chart_type == 'polar_area_chart'){
        var ctx = document.getElementById('myChart').getContext('2d');
        var emisData = {
          labels: [emi_calc_style.emi_principal_chart_text,emi_calc_style.emi_interest_chart_text],
          datasets: [{
            data: [pamt, total_interest],
            backgroundColor: [
              emi_calc_style.emi_principal_chart_color,
              emi_calc_style.emi_intereset_chart_color
            ]
          }]
        };

        emi_charts = new Chart(ctx, {
          type: 'polarArea',
          data: emisData
        });
      }
    }
  }


  function loadResult() {
    /* Loan Year Tenure Change */
    var emi_months_years = jQuery("input[name='emi_months_years']:checked").val();
    var currency = 'INR';
    if (emi_months_years == 'years') {
      jQuery(".year_emi").css('display','block');
      jQuery(".month_emi").css('display','none');
      var months = jQuery('#emi_tenure').val();
      var month = months*12;

      var min_year_Tenure = parseFloat(emi_calc_style.yearly_min_loan_term);
      var max_year_Tenure = parseFloat(emi_calc_style.yearly_max_loan_term);

      // jQuery("#emi_tenure").attr("value", min_year_Tenure);
      jQuery("#emi_tenure").on("change", function() {
        if(jQuery('#emi_tenure') < min_year_Tenure){
          jQuery(this).val(min_year_Tenure);
        } 
        if(jQuery('#emi_tenure') > max_year_Tenure){
          jQuery(this).val(max_year_Tenure);
        }
      });

      if(months < min_year_Tenure){
        console.log('year min');
        var month = jQuery("#emi_tenure").val(min_year_Tenure);
      }else if(months > max_year_Tenure){
        console.log('year max');
        var month = jQuery("#emi_tenure").val(max_year_Tenure);
      }
    } else if (emi_months_years == 'months') {
      /* Loan Month Tenure Change */
      jQuery(".year_emi").css('display','none');
      jQuery(".month_emi").css('display','block');
      var month = jQuery('#emi_tenure').val();
      var min_year_Tenure = parseFloat(emi_calc_style.yearly_min_loan_term);

      var min_month_Tenure = parseFloat(emi_calc_style.monthly_min_loan_term);
      var max_month_Tenure = parseFloat(emi_calc_style.monthly_max_loan_term);
      if (month == min_year_Tenure) {
        month = min_month_Tenure;
      }else{
        month = jQuery('#emi_tenure').val();
      }

      jQuery("#emi_tenure").attr("value", min_month_Tenure);

      console.log('min_month_Tenure: ', min_month_Tenure);
    }
    
    var pamt = jQuery("#emi_loan_amount").val();
    var rate = jQuery("#emi_interest_rate").val();
    var pamt1 = parseFloat(pamt);
    var monthlyInterestRatio = (rate/100)/12;
    var monthlyInterest = (monthlyInterestRatio * pamt);
    var top = Math.pow((1 + monthlyInterestRatio), month);
    var emi = ((pamt * monthlyInterestRatio) * (top/(top-1)));
    result_emi = emi.toFixed(0);
    totalAmount = (emi * month).toFixed(0);
    total_interest = (totalAmount - pamt).toFixed(0);
    var total_interestRounded = parseFloat((totalAmount - pamt).toFixed(0));

    jQuery("#result_emi").html((result_emi).toLocaleString('en-IN') + ' ' + currency);
    jQuery("#total_interest").html((total_interest).toLocaleString('en-IN') + ' ' + currency);
    jQuery("#total_payments").html(Math.round(totalAmount).toLocaleString('en-IN') + ' ' + currency);

    emi_Graph();
  }

  var loan_amount = jQuery('#emi_loan_amount');
  var interest_rate = jQuery('#emi_interest_rate');
  var emi_tenure = jQuery('#emi_tenure');
  var months_years = jQuery("input[name='emi_months_years']");

  var emi_loanamount_slider = jQuery('#emi_loanamount_slider');
  var emi_interest_rate_slider = jQuery('#emi_interest_rate_slider');
  var emi_year_tenure_slider = jQuery('#emi_year_tenure_slider');
  var emi_month_tenure_slider = jQuery('#emi_month_tenure_slider');

  var amount_int = document.getElementById('emi_interest_rate');

  emi_loanamount_slider.rangeslider({
    polyfill: false
  }).on('input', function() {
    loan_amount[0].value = this.value;
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  loan_amount.on('input', function() {
    var emi_loan_amo = parseInt(this.value);
    var min_loan_amount = parseFloat(emi_calc_style.emi_min_loan_amount);
    var max_loan_amount = parseFloat(emi_calc_style.emi_max_loan_amount);
    // console.log(sip_max_invested_amount);
    if (emi_loan_amo < min_loan_amount) this.value = min_loan_amount;
    if (emi_loan_amo > max_loan_amount) this.value = max_loan_amount;
    if (emi_loan_amo) {
      emi_loanamount_slider.val(this.value).change();
    }
    // emi_loanamount_slider.val(this.value).change();
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  emi_interest_rate_slider.rangeslider({
    polyfill: false
  }).on('input', function() {
    interest_rate[0].value = this.value;
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  interest_rate.on('input', function() {
    var emi_loan_inte = parseInt(this.value);
    var min_interest_rate = parseFloat(emi_calc_style.emi_min_interest_rate);
    var max_interest_rate = parseFloat(emi_calc_style.emi_max_interest_rate);
    // console.log(max_interest_rate);
    if (emi_loan_inte < min_interest_rate) this.value = min_interest_rate;
    if (emi_loan_inte > max_interest_rate) this.value = max_interest_rate;
    if (emi_loan_inte) {
      emi_interest_rate_slider.val(this.value).change();
    }
    // emi_interest_rate_slider.val(this.value).change();
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  emi_year_tenure_slider.rangeslider({
    polyfill: false
  }).on('input', function() {
    emi_tenure[0].value = this.value;
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  emi_month_tenure_slider.rangeslider({
    polyfill: false
  }).on('input', function() {
    emi_tenure[0].value = this.value;
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  emi_tenure.on('input', function() {
    var emi_months_years = jQuery("input[name='emi_months_years']:checked").val();
    var month_loan_ten = parseInt(this.value);
    if (emi_months_years == 'years') {
      emi_year_tenure_slider.val(this.value).change();
    }else if(emi_months_years == 'months'){
      var min_month_Tenure = parseFloat(emi_calc_style.monthly_min_loan_term);
      var max_month_Tenure = parseFloat(emi_calc_style.monthly_max_loan_term);
      if (month_loan_ten < min_month_Tenure) {
        this.value = min_month_Tenure;
      }
      if (month_loan_ten > max_month_Tenure) {
        this.value = max_month_Tenure;
      }
      emi_month_tenure_slider.val(this.value).change();
    }

    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });

  loan_amount.on('input', function() {
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });
  interest_rate.on('input', function() {
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });
  emi_tenure.on('input', function() {
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });
  months_years.on('input', function() {
    if(emi_calc_style.emi_calc_with_chart == 'yes'){
      emi_charts.destroy();
    }
    loadResult();
  });
})