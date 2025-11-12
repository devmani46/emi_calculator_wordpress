/**
 * Bike EMI Calculator - Frontend JavaScript
 */

(function ($) {
    'use strict';

    const BikeEMICalculator = {
        init: function () {
            this.cacheElements();
            this.bindEvents();
            this.initializeCalculator();
        },

        cacheElements: function () {
            this.$calculator = $('.bike-emi-calculator');
            this.$bikeSelect = $('#bike_model');
            this.$principalSlider = $('#principal_amount');
            this.$tenureSlider = $('#tenure_months');
            this.$principalDisplay = $('#principal_display');
            this.$tenureDisplay = $('#tenure_display');
            this.$monthlyEMI = $('#monthly_emi');
            this.$totalAmount = $('#total_amount');
            this.$totalInterest = $('#total_interest');
            this.$applicationForm = $('#emi_application_form');
            this.$submitBtn = $('#submit_emi_btn');
            this.$alerts = $('.emi-alert');
        },

        bindEvents: function () {
            const self = this;

            // Slider events
            if (this.$principalSlider.length) {
                this.$principalSlider.on('input', function () {
                    self.updatePrincipalDisplay();
                    self.calculateEMI();
                });
            }

            if (this.$tenureSlider.length) {
                this.$tenureSlider.on('input', function () {
                    self.updateTenureDisplay();
                    self.calculateEMI();
                });
            }

            // Bike model change
            if (this.$bikeSelect.length) {
                this.$bikeSelect.on('change', function () {
                    self.calculateEMI();
                });
            }

            // Form submission
            if (this.$applicationForm.length) {
                this.$applicationForm.on('submit', function (e) {
                    e.preventDefault();
                    self.submitApplication();
                });
            }

            // Alert close button
            $('.emi-alert-close').on('click', function () {
                $(this).closest('.emi-alert').fadeOut();
            });
        },

        initializeCalculator: function () {
            if (this.$principalSlider.length) {
                this.updatePrincipalDisplay();
                this.updateTenureDisplay();
                this.calculateEMI();
            }
        },

        updatePrincipalDisplay: function () {
            const value = this.$principalSlider.val();
            this.$principalDisplay.text(this.formatCurrency(value));
        },

        updateTenureDisplay: function () {
            const months = this.$tenureSlider.val();
            const years = (months / 12).toFixed(1);
            this.$tenureDisplay.text(months + ' months (' + years + ' years)');
        },

        calculateEMI: function () {
            const principal = parseFloat(this.$principalSlider.val()) || 0;
            const tenure = parseFloat(this.$tenureSlider.val()) || 0;
            const bikeId = this.$bikeSelect.val();

            if (!bikeId || principal <= 0 || tenure <= 0) {
                this.$monthlyEMI.text('₹0');
                this.$totalAmount.text('₹0');
                this.$totalInterest.text('₹0');
                return;
            }

            // Get interest rate from selected bike
            const $selectedOption = this.$bikeSelect.find('option:selected');
            const interestRate = parseFloat($selectedOption.data('interest-rate')) || 0;

            // EMI Formula: [P x R x (1+R)^N] / [(1+R)^N-1]
            const monthlyRate = interestRate / 100 / 12;
            const emi = (principal * monthlyRate * Math.pow(1 + monthlyRate, tenure)) / 
                        (Math.pow(1 + monthlyRate, tenure) - 1);
            const totalAmount = emi * tenure;
            const totalInterest = totalAmount - principal;

            // Display results
            this.$monthlyEMI.text(this.formatCurrency(emi));
            this.$totalAmount.text(this.formatCurrency(totalAmount));
            this.$totalInterest.text(this.formatCurrency(totalInterest));
        },

        submitApplication: function () {
            const self = this;

            // Validate form
            if (!this.validateForm()) {
                return;
            }

            // Show loading state
            this.$submitBtn.prop('disabled', true);
            this.$submitBtn.html('<span class="emi-loading"></span>Submitting...');

            // Prepare form data
            const formData = new FormData(this.$applicationForm[0]);
            formData.append('action', 'submit_emi_application');
            formData.append('nonce', emiData.nonce);

            // Submit via AJAX
            $.ajax({
                url: emiData.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        self.showAlert('success', response.data.message || 'Application submitted successfully!');
                        self.$applicationForm[0].reset();
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    } else {
                        self.showAlert('error', response.data.message || 'Error submitting application.');
                    }
                },
                error: function () {
                    self.showAlert('error', 'An error occurred while submitting the application.');
                },
                complete: function () {
                    self.$submitBtn.prop('disabled', false);
                    self.$submitBtn.html('Submit Application');
                }
            });
        },

        validateForm: function () {
            let isValid = true;
            this.$applicationForm.find('[required]').each(function () {
                const $field = $(this);
                const value = $field.val().trim();

                if (!value) {
                    $field.closest('.emi-form-group').addClass('error');
                    $field.closest('.emi-form-group').find('.emi-form-error').text('This field is required');
                    isValid = false;
                } else {
                    // Validate email
                    if ($field.attr('type') === 'email' && !this.isValidEmail(value)) {
                        $field.closest('.emi-form-group').addClass('error');
                        $field.closest('.emi-form-group').find('.emi-form-error').text('Please enter a valid email');
                        isValid = false;
                    }
                    // Validate phone
                    else if ($field.attr('type') === 'tel' && !this.isValidPhone(value)) {
                        $field.closest('.emi-form-group').addClass('error');
                        $field.closest('.emi-form-group').find('.emi-form-error').text('Please enter a valid phone number');
                        isValid = false;
                    } else {
                        $field.closest('.emi-form-group').removeClass('error');
                    }
                }
            }.bind(this));

            return isValid;
        },

        isValidEmail: function (email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        isValidPhone: function (phone) {
            const re = /^[0-9]{10}$/;
            return re.test(phone.replace(/\D/g, ''));
        },

        formatCurrency: function (value) {
            return '₹' + parseFloat(value).toLocaleString('en-IN', {
                maximumFractionDigits: 2,
                minimumFractionDigits: 2
            });
        },

        showAlert: function (type, message) {
            const alertClass = 'emi-alert-' + type;
            const $alert = $('<div class="emi-alert ' + alertClass + ' show">')
                .html('<span class="emi-alert-close">&times;</span>' + message)
                .prependTo(this.$calculator);

            $alert.find('.emi-alert-close').on('click', function () {
                $alert.fadeOut(function () {
                    $alert.remove();
                });
            });

            setTimeout(function () {
                $alert.fadeOut(function () {
                    $alert.remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        BikeEMICalculator.init();
    });

    // File upload handling
    $(document).on('click', '.emi-file-upload', function () {
        $(this).find('input[type="file"]').click();
    });

    $(document).on('change', '.emi-file-upload input[type="file"]', function () {
        const files = this.files;
        const $fileList = $(this).closest('.emi-form-group').find('.emi-file-list');

        if (files.length > 0) {
            let html = '';
            for (let i = 0; i < files.length; i++) {
                html += '<div class="emi-file-item"><span class="emi-file-item-name">' + 
                        files[i].name + '</span><span class="emi-file-remove">&times;</span></div>';
            }
            $fileList.html(html);

            // File remove
            $fileList.find('.emi-file-remove').on('click', function () {
                $(this).closest('.emi-file-item').remove();
                $(this).closest('.emi-form-group').find('input[type="file"]')[0].value = '';
            });
        }
    });

})(jQuery);
