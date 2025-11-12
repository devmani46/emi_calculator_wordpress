/**
 * Bike EMI Calculator - Admin JavaScript
 */

(function ($) {
    'use strict';

    const BikeEMIAdmin = {
        init: function () {
            this.cacheElements();
            this.bindEvents();
        },

        cacheElements: function () {
            this.$body = $('body');
            this.$bikeForm = $('#bike_form');
            this.$tenureForm = $('#tenure_form');
            this.$documentForm = $('#document_form');
            this.$settingsForm = $('#settings_form');
            this.$deleteButtons = $('.btn-delete');
            this.$editButtons = $('.btn-edit');
            this.$modal = $('.bike-emi-modal');
            this.$modalClose = $('.bike-emi-modal-close');
        },

        bindEvents: function () {
            const self = this;

            // Form submissions
            if (this.$bikeForm.length) {
                this.$bikeForm.on('submit', function (e) {
                    e.preventDefault();
                    self.saveBikeModel($(this));
                });
            }

            if (this.$tenureForm.length) {
                this.$tenureForm.on('submit', function (e) {
                    e.preventDefault();
                    self.saveTenureOption($(this));
                });
            }

            if (this.$documentForm.length) {
                this.$documentForm.on('submit', function (e) {
                    e.preventDefault();
                    self.saveDocument($(this));
                });
            }

            if (this.$settingsForm.length) {
                this.$settingsForm.on('submit', function (e) {
                    e.preventDefault();
                    self.saveSettings($(this));
                });
            }

            // Delete buttons
            this.$deleteButtons.on('click', function () {
                self.deleteItem($(this));
            });

            // Edit buttons
            this.$editButtons.on('click', function () {
                self.editItem($(this));
            });

            // Modal close
            this.$modalClose.on('click', function () {
                $(this).closest('.bike-emi-modal').removeClass('show');
            });

            // Modal close on background click
            this.$modal.on('click', function (e) {
                if ($(e.target).hasClass('bike-emi-modal')) {
                    $(this).removeClass('show');
                }
            });

            // Search and filter
            this.bindSearchFilter();

            // Data table initialization
            this.initDataTables();
        },

        saveBikeModel: function ($form) {
            const self = this;
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).html('<span class="emi-loading"></span>Saving...');

            const formData = {
                action: 'save_bike_model',
                nonce: wp_nonce_var.nonce,
                id: $form.find('[name="bike_id"]').val(),
                name: $form.find('[name="name"]').val(),
                description: $form.find('[name="description"]').val(),
                price: $form.find('[name="price"]').val(),
                interest_rate: $form.find('[name="interest_rate"]').val(),
                image_url: $form.find('[name="image_url"]').val(),
                status: $form.find('[name="status"]').val()
            };

            $.post(ajaxurl, formData, function (response) {
                if (response.success) {
                    self.showAlert('success', response.data.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    self.showAlert('error', response.data.message);
                }
                $submitBtn.prop('disabled', false).text(originalText);
            }).fail(function () {
                self.showAlert('error', 'An error occurred while saving.');
                $submitBtn.prop('disabled', false).text(originalText);
            });
        },

        saveTenureOption: function ($form) {
            const self = this;
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).html('<span class="emi-loading"></span>Saving...');

            const formData = {
                action: 'save_tenure_option',
                nonce: wp_nonce_var.nonce,
                id: $form.find('[name="tenure_id"]').val(),
                months: $form.find('[name="months"]').val(),
                years: $form.find('[name="years"]').val(),
                label: $form.find('[name="label"]').val(),
                status: $form.find('[name="status"]').val()
            };

            $.post(ajaxurl, formData, function (response) {
                if (response.success) {
                    self.showAlert('success', response.data.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    self.showAlert('error', response.data.message);
                }
                $submitBtn.prop('disabled', false).text(originalText);
            }).fail(function () {
                self.showAlert('error', 'An error occurred while saving.');
                $submitBtn.prop('disabled', false).text(originalText);
            });
        },

        saveDocument: function ($form) {
            const self = this;
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).html('<span class="emi-loading"></span>Saving...');

            const formData = {
                action: 'save_required_document',
                nonce: wp_nonce_var.nonce,
                id: $form.find('[name="doc_id"]').val(),
                name: $form.find('[name="name"]').val(),
                doc_type: $form.find('[name="doc_type"]').val(),
                description: $form.find('[name="description"]').val(),
                status: $form.find('[name="status"]').val()
            };

            $.post(ajaxurl, formData, function (response) {
                if (response.success) {
                    self.showAlert('success', response.data.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    self.showAlert('error', response.data.message);
                }
                $submitBtn.prop('disabled', false).text(originalText);
            }).fail(function () {
                self.showAlert('error', 'An error occurred while saving.');
                $submitBtn.prop('disabled', false).text(originalText);
            });
        },

        saveSettings: function ($form) {
            const self = this;
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).html('<span class="emi-loading"></span>Saving...');

            const formData = {
                action: 'save_emi_settings',
                nonce: wp_nonce_var.nonce,
                min_loan_amount: $form.find('[name="min_loan_amount"]').val(),
                max_loan_amount: $form.find('[name="max_loan_amount"]').val(),
                enable_notifications: $form.find('[name="enable_notifications"]').is(':checked') ? 1 : 0,
                notification_email: $form.find('[name="notification_email"]').val(),
                enable_sms: $form.find('[name="enable_sms"]').is(':checked') ? 1 : 0,
                sms_api_key: $form.find('[name="sms_api_key"]').val()
            };

            $.post(ajaxurl, formData, function (response) {
                if (response.success) {
                    self.showAlert('success', response.data.message || 'Settings saved successfully!');
                } else {
                    self.showAlert('error', response.data.message || 'Error saving settings.');
                }
                $submitBtn.prop('disabled', false).text(originalText);
            }).fail(function () {
                self.showAlert('error', 'An error occurred while saving.');
                $submitBtn.prop('disabled', false).text(originalText);
            });
        },

        deleteItem: function ($button) {
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            const self = this;
            const itemId = $button.data('id');
            const itemType = $button.data('type');

            $button.prop('disabled', true).html('<span class="emi-loading"></span>');

            const formData = {
                action: 'delete_emi_item',
                nonce: wp_nonce_var.nonce,
                item_id: itemId,
                item_type: itemType
            };

            $.post(ajaxurl, formData, function (response) {
                if (response.success) {
                    self.showAlert('success', 'Item deleted successfully!');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    self.showAlert('error', response.data.message || 'Error deleting item.');
                    $button.prop('disabled', false).text('Delete');
                }
            }).fail(function () {
                self.showAlert('error', 'An error occurred while deleting.');
                $button.prop('disabled', false).text('Delete');
            });
        },

        editItem: function ($button) {
            const itemId = $button.data('id');
            const itemType = $button.data('type');

            // This would typically load the item data and show a modal or edit form
            // For now, we'll just navigate to the edit page
            const editUrl = $button.data('edit-url') || '#';
            window.location.href = editUrl;
        },

        bindSearchFilter: function () {
            const self = this;
            const $searchInput = $('#search-input');

            if ($searchInput.length) {
                $searchInput.on('keyup', function () {
                    const searchTerm = $(this).val().toLowerCase();
                    const $rows = $('.bike-emi-table tbody tr');

                    $rows.each(function () {
                        const rowText = $(this).text().toLowerCase();
                        if (rowText.includes(searchTerm)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            }
        },

        initDataTables: function () {
            // Initialize data tables if DataTables plugin is available
            if ($.fn.dataTable && $('.bike-emi-table').length) {
                $('.bike-emi-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    order: [[0, 'desc']]
                });
            }
        },

        showAlert: function (type, message) {
            const alertClass = 'bike-emi-alert-' + type;
            const $alert = $('<div class="bike-emi-alert ' + alertClass + ' show">' + message + '</div>')
                .prependTo('.bike-emi-admin-page, body');

            setTimeout(function () {
                $alert.fadeOut(function () {
                    $alert.remove();
                });
            }, 4000);
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        BikeEMIAdmin.init();
    });

    // Bulk actions handler
    $(document).on('click', '.button-primary', function () {
        const $bulkAction = $('select[name="bulk_action"]');
        const $checkedItems = $('input[name="item_ids[]"]:checked');

        if ($bulkAction.val() && $checkedItems.length > 0) {
            // Handle bulk actions
        }
    });

})(jQuery);
