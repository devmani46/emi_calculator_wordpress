<?php
/**
 * Bike EMI Calculator - Tenure Options Management Template
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="bike-emi-admin-page">
    <div class="bike-emi-page-header">
        <h1 class="bike-emi-page-title">Tenure Options</h1>
        <p class="bike-emi-page-subtitle">Manage available tenure periods for EMI calculations</p>
    </div>

    <!-- Add/Edit Form -->
    <div class="bike-emi-card">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Add New Tenure Option</h2>
        </div>
        <form id="tenure_form" method="post">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="bike-emi-form-group required">
                    <label for="months">Months</label>
                    <input type="number" id="months" name="months" min="1" required>
                </div>
                <div class="bike-emi-form-group required">
                    <label for="years">Years</label>
                    <input type="number" id="years" name="years" step="0.5" min="0" required>
                </div>
                <div class="bike-emi-form-group required">
                    <label for="label">Display Label</label>
                    <input type="text" id="label" name="label" placeholder="e.g., 12 Months" required>
                </div>
                <div class="bike-emi-form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="tenure_id" value="">
            <button type="submit" class="bike-emi-btn">Save Tenure Option</button>
        </form>
    </div>

    <!-- Tenure Options List -->
    <div class="bike-emi-card" style="margin-top: 30px;">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Tenure Options List</h2>
        </div>
        <?php
        global $wpdb;
        $tenures = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}emi_tenure_options ORDER BY months ASC");

        if (!empty($tenures)) {
            ?>
            <table class="bike-emi-table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Months</th>
                        <th>Years</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tenures as $tenure) {
                        $status_class = $tenure->status === 'active' ? 'active' : 'inactive';
                        ?>
                        <tr>
                            <td><?php echo esc_html($tenure->label); ?></td>
                            <td><?php echo intval($tenure->months); ?></td>
                            <td><?php echo floatval($tenure->years); ?></td>
                            <td><span class="badge badge-<?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($tenure->status)); ?></span></td>
                            <td><?php echo esc_html(date('d M Y', strtotime($tenure->created_at))); ?></td>
                            <td>
                                <button class="bike-emi-btn bike-emi-btn-secondary bike-emi-btn-small btn-edit" data-id="<?php echo intval($tenure->id); ?>" data-type="tenure">Edit</button>
                                <button class="bike-emi-btn bike-emi-btn-danger bike-emi-btn-small btn-delete" data-id="<?php echo intval($tenure->id); ?>" data-type="tenure">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p style="color: #6b7280; text-align: center; padding: 20px;">No tenure options found. Add one to get started!</p>';
        }
        ?>
    </div>
</div>
