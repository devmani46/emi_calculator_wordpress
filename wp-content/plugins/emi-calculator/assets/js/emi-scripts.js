(function($){
    $(document).ready(function(){

        function showResults(data){
            $('#emi-monthly').text(data.monthly_emi);
            $('#emi-total').text(data.total_payable);
            $('#emi-interest').text(data.total_interest);
            $('#emi-results').show();
        }

        $('#emi-calc-btn').on('click', function(e){
            e.preventDefault();
            var $sel = $('#emi-bike-model');
            var price = $sel.find('option:selected').data('price') || 0;
            var interest = $sel.find('option:selected').data('interest') || 0;
            var tenure = $('#emi-tenure').val();

            if (!price || !tenure) {
                alert('Please select bike and tenure');
                return;
            }

            $.post(emi_ajax.ajax_url, {
                action: 'emi_calculate',
                price: price,
                interest: interest,
                tenure: tenure,
                nonce: emi_ajax.nonce
            }, function(resp){
                if (resp.success) {
                    showResults(resp.data);
                } else {
                    alert(resp.data || 'Calculation error');
                }
            });

        });

        $('#emi-form').on('submit', function(e){
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('action', 'emi_submit');
            formData.append('nonce', emi_ajax.nonce);

            $('#emi-submit-btn').prop('disabled', true).text('Submitting...');

            $.ajax({
                url: emi_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp){
                    $('#emi-submit-btn').prop('disabled', false).text('Submit Application');
                    if (resp.success) {
                        $('#emi-form-messages').text('Application submitted. Reference ID: ' + resp.data.id);
                        $('#emi-form')[0].reset();
                        $('#emi-results').hide();
                    } else {
                        $('#emi-form-messages').text('Error: ' + resp.data);
                    }
                },
                error: function(){
                    $('#emi-submit-btn').prop('disabled', false).text('Submit Application');
                    $('#emi-form-messages').text('Network or server error');
                }
            });
        });

    });
})(jQuery);
