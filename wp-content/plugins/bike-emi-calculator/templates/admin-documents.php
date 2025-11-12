<?php
/**
 * Bike EMI Calculator - Required Documents Management Template
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="bike-emi-admin-page">
    <div class="bike-emi-page-header">
        <h1 class="bike-emi-page-title">Required Documents</h1>
        <p class="bike-emi-page-subtitle">Manage documents required for EMI applications</p>
    </div>

    <!-- Add/Edit Form -->
    <div class="bike-emi-card">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Add New Document</h2>
        </div>
        <form id="document_form" method="post">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="bike-emi-form-group required">
                    <label for="doc_name">Document Name</label>
                    <input type="text" id="doc_name" name="name" placeholder="e.g., Aadhar Card" required>
                </div>
                <div class="bike-emi-form-group required">
                    <label for="doc_type">Document Type</label>
                    <select id="doc_type" name="doc_type" required>
                        <option value="">Select Type</option>
                        <option value="pdf">PDF</option>
                        <option value="image">Image (JPG/PNG)</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="bike-emi-form-group">
                    <label for="doc_status">Status</label>
                    <select id="doc_status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="bike-emi-form-group">
                <label for="doc_description">Description</label>
                <textarea id="doc_description" name="description"></textarea>
            </div>
            <input type="hidden" name="doc_id" value="">
            <button type="submit" class="bike-emi-btn">Save Document</button>
        </form>
    </div>

    <!-- Documents List -->
    <div class="bike-emi-card" style="margin-top: 30px;">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Required Documents List</h2>
        </div>
        <?php
        global $wpdb;
        $documents = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}emi_required_documents ORDER BY created_at DESC");

        if (!empty($documents)) {
            ?>
            <table class="bike-emi-table">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc) {
                        $status_class = $doc->status === 'active' ? 'active' : 'inactive';
                        ?>
                        <tr>
                            <td><?php echo esc_html($doc->name); ?></td>
                            <td><span class="badge"><?php echo esc_html(ucfirst($doc->doc_type)); ?></span></td>
                            <td><span class="badge badge-<?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($doc->status)); ?></span></td>
                            <td><?php echo esc_html(date('d M Y', strtotime($doc->created_at))); ?></td>
                            <td>
                                <button class="bike-emi-btn bike-emi-btn-secondary bike-emi-btn-small btn-edit" data-id="<?php echo intval($doc->id); ?>" data-type="document">Edit</button>
                                <button class="bike-emi-btn bike-emi-btn-danger bike-emi-btn-small btn-delete" data-id="<?php echo intval($doc->id); ?>" data-type="document">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p style="color: #6b7280; text-align: center; padding: 20px;">No documents found. Add one to get started!</p>';
        }
        ?>
    </div>
</div>
