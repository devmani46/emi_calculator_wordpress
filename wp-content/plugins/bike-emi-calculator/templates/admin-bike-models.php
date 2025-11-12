<?php
/**
 * Bike EMI Calculator - Bike Models Management Template
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="bike-emi-admin-page">
    <div class="bike-emi-page-header">
        <h1 class="bike-emi-page-title">Bike Models</h1>
        <p class="bike-emi-page-subtitle">Manage available bike models for EMI calculations</p>
    </div>

    <!-- Add/Edit Form -->
    <div class="bike-emi-card">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Add New Bike Model</h2>
        </div>
        <form id="bike_form" method="post">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="bike-emi-form-group required">
                    <label for="name">Bike Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="bike-emi-form-group required">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="bike-emi-form-group required">
                    <label for="interest_rate">Interest Rate (%)</label>
                    <input type="number" id="interest_rate" name="interest_rate" step="0.01" required>
                </div>
                <div class="bike-emi-form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url">
                </div>
                <div class="bike-emi-form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="bike-emi-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <input type="hidden" name="bike_id" value="">
            <button type="submit" class="bike-emi-btn bike-emi-btn-large">Save Bike Model</button>
        </form>
    </div>

    <!-- Models List -->
    <div class="bike-emi-card" style="margin-top: 30px;">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Bike Models List</h2>
            <input type="text" id="search-input" placeholder="Search models..." style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
        </div>
        <?php
        global $wpdb;
        $models = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}emi_bike_models ORDER BY created_at DESC");

        if (!empty($models)) {
            ?>
            <table class="bike-emi-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Interest Rate</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($models as $model) {
                        $status_class = $model->status === 'active' ? 'active' : 'inactive';
                        ?>
                        <tr>
                            <td><?php echo esc_html($model->name); ?></td>
                            <td>₹<?php echo number_format($model->price, 2); ?></td>
                            <td><?php echo esc_html($model->interest_rate); ?>%</td>
                            <td><span class="badge badge-<?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($model->status)); ?></span></td>
                            <td><?php echo esc_html(date('d M Y', strtotime($model->created_at))); ?></td>
                            <td>
                                <button class="bike-emi-btn bike-emi-btn-secondary bike-emi-btn-small btn-edit" data-id="<?php echo intval($model->id); ?>" data-type="bike_model">Edit</button>
                                <button class="bike-emi-btn bike-emi-btn-danger bike-emi-btn-small btn-delete" data-id="<?php echo intval($model->id); ?>" data-type="bike_model">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p style="color: #6b7280; text-align: center; padding: 20px;">No bike models found. Add one to get started!</p>';
        }
        ?>
    </div>
</div>
