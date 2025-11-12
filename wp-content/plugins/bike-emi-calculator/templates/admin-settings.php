<?php
/**
 * Bike EMI Calculator - Settings Management Template
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="bike-emi-admin-page">
    <div class="bike-emi-page-header">
        <h1 class="bike-emi-page-title">EMI Calculator Settings</h1>
        <p class="bike-emi-page-subtitle">Configure plugin settings and preferences</p>
    </div>

    <!-- Settings Form -->
    <div class="bike-emi-card">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Plugin Configuration</h2>
        </div>
        <form id="settings_form" method="post">
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                
                <!-- Loan Amount Settings -->
                <div style="padding: 20px; background-color: #f3f4f6; border-radius: 8px;">
                    <h3 style="margin-top: 0; color: #1f2937;">Loan Amount Settings</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div class="bike-emi-form-group required">
                            <label for="min_loan">Minimum Loan Amount (Rs)</label>
                            <input type="number" id="min_loan" name="min_loan_amount" value="<?php echo esc_attr(get_option('bike_emi_min_loan', 100000)); ?>" step="1000" required>
                        </div>
                        <div class="bike-emi-form-group required">
                            <label for="max_loan">Maximum Loan Amount (Rs)</label>
                            <input type="number" id="max_loan" name="max_loan_amount" value="<?php echo esc_attr(get_option('bike_emi_max_loan', 1000000)); ?>" step="1000" required>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div style="padding: 20px; background-color: #f3f4f6; border-radius: 8px;">
                    <h3 style="margin-top: 0; color: #1f2937;">Email Notifications</h3>
                    <div class="bike-emi-form-group">
                        <label>
                            <input type="checkbox" name="enable_notifications" value="1" <?php checked(get_option('bike_emi_enable_notifications'), 1); ?>>
                            Enable Email Notifications
                        </label>
                    </div>
                    <div class="bike-emi-form-group required">
                        <label for="notification_email">Notification Email Address</label>
                        <input type="email" id="notification_email" name="notification_email" value="<?php echo esc_attr(get_option('bike_emi_notification_email', get_option('admin_email'))); ?>" required>
                    </div>
                </div>

                <!-- SMS Settings -->
                <div style="padding: 20px; background-color: #f3f4f6; border-radius: 8px;">
                    <h3 style="margin-top: 0; color: #1f2937;">SMS Notifications</h3>
                    <div class="bike-emi-form-group">
                        <label>
                            <input type="checkbox" name="enable_sms" value="1" <?php checked(get_option('bike_emi_enable_sms'), 1); ?>>
                            Enable SMS Notifications
                        </label>
                    </div>
                    <div class="bike-emi-form-group">
                        <label for="sms_api_key">SMS API Key</label>
                        <input type="password" id="sms_api_key" name="sms_api_key" value="<?php echo esc_attr(get_option('bike_emi_sms_api_key', '')); ?>" placeholder="Enter your SMS provider API key">
                        <small style="color: #6b7280; display: block; margin-top: 5px;">Keep this secure. This is used to send SMS notifications to customers.</small>
                    </div>
                </div>

                <!-- Shortcode Information -->
                <div style="padding: 20px; background-color: #dbeafe; border-radius: 8px; border-left: 4px solid #2563eb;">
                    <h3 style="margin-top: 0; color: #1f2937;"> Shortcode Usage</h3>
                    <p style="margin: 10px 0; color: #374151;">To display the EMI Calculator on your website, use this shortcode:</p>
                    <code style="display: block; padding: 10px; background-color: #ffffff; border-radius: 4px; margin: 10px 0; color: #2563eb; font-weight: bold;">[bike_emi_calculator title="Your Title"]</code>
                </div>

            </div>

            <button type="submit" class="bike-emi-btn bike-emi-btn-large" style="margin-top: 20px;">Save Settings</button>
        </form>
    </div>

    <!-- Additional Information -->
    <div class="bike-emi-card" style="margin-top: 30px;">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title"> Documentation & Support</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="padding: 15px; border-left: 3px solid #2563eb;">
                <h4 style="margin: 0 0 10px 0; color: #1f2937;">Getting Started</h4>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Add bike models, tenure options, and required documents to configure the calculator for your use case.</p>
            </div>
            <div style="padding: 15px; border-left: 3px solid #16a34a;">
                <h4 style="margin: 0 0 10px 0; color: #1f2937;">FAQ</h4>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">For common questions and answers, visit our documentation or contact support.</p>
            </div>
            <div style="padding: 15px; border-left: 3px solid #ea580c;">
                <h4 style="margin: 0 0 10px 0; color: #1f2937;">Support</h4>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Need help? Contact our support team or check the documentation for detailed guides.</p>
            </div>
        </div>
    </div>
</div>
