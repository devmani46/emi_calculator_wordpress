<?php
/**
 * Bike EMI Calculator - Admin Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="bike-emi-admin-page">
    <div class="bike-emi-page-header">
        <h1 class="bike-emi-page-title">EMI Calculator Dashboard</h1>
        <p class="bike-emi-page-subtitle">Manage your bike EMI calculator plugin</p>
    </div>

    <!-- Stats Section -->
    <div class="bike-emi-stats-grid">
        <div class="bike-emi-stat-card">
            <div class="bike-emi-stat-label">Total Applications</div>
            <p class="bike-emi-stat-value"><?php echo wp_count_posts('emi_application')->publish; ?></p>
            <div class="bike-emi-stat-change">All time</div>
        </div>

        <div class="bike-emi-stat-card success">
            <div class="bike-emi-stat-label">Approved Applications</div>
            <p class="bike-emi-stat-value">
                <?php
                $approved = count(get_posts(array(
                    'post_type' => 'emi_application',
                    'meta_query' => array(
                        array(
                            'key' => 'application_status',
                            'value' => 'approved',
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => -1
                )));
                echo $approved;
                ?>
            </p>
        </div>

        <div class="bike-emi-stat-card warning">
            <div class="bike-emi-stat-label">Pending Applications</div>
            <p class="bike-emi-stat-value">
                <?php
                $pending = count(get_posts(array(
                    'post_type' => 'emi_application',
                    'meta_query' => array(
                        array(
                            'key' => 'application_status',
                            'value' => 'pending',
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => -1
                )));
                echo $pending;
                ?>
            </p>
        </div>

        <div class="bike-emi-stat-card danger">
            <div class="bike-emi-stat-label">Rejected Applications</div>
            <p class="bike-emi-stat-value">
                <?php
                $rejected = count(get_posts(array(
                    'post_type' => 'emi_application',
                    'meta_query' => array(
                        array(
                            'key' => 'application_status',
                            'value' => 'rejected',
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => -1
                )));
                echo $rejected;
                ?>
            </p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bike-emi-card">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Quick Actions</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="<?php echo admin_url('admin.php?page=bike-emi-models'); ?>" class="bike-emi-btn">
                 Manage Bike Models
            </a>
            <a href="<?php echo admin_url('admin.php?page=bike-emi-tenure'); ?>" class="bike-emi-btn">
                 Tenure Options
            </a>
            <a href="<?php echo admin_url('admin.php?page=bike-emi-documents'); ?>" class="bike-emi-btn">
                 Required Documents
            </a>
            <a href="<?php echo admin_url('admin.php?page=bike-emi-settings'); ?>" class="bike-emi-btn">
                 Settings
            </a>
        </div>
    </div>

    <!-- Recent Applications -->
    <div class="bike-emi-card" style="margin-top: 30px;">
        <div class="bike-emi-card-header">
            <h2 class="bike-emi-card-title">Recent Applications</h2>
            <a href="<?php echo admin_url('edit.php?post_type=emi_application'); ?>" class="bike-emi-btn bike-emi-btn-secondary">View All</a>
        </div>
        <?php
        $recent_apps = get_posts(array(
            'post_type' => 'emi_application',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if (!empty($recent_apps)) {
            ?>
            <table class="bike-emi-table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Bike Model</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_apps as $app) {
                        $status = get_post_meta($app->ID, 'application_status', true) ?: 'pending';
                        ?>
                        <tr>
                            <td><?php echo esc_html(get_post_meta($app->ID, 'customer_name', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($app->ID, 'customer_email', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($app->ID, 'customer_phone', true)); ?></td>
                            <td>
                                <?php
                                $bike_id = get_post_meta($app->ID, 'bike_id', true);
                                if ($bike_id) {
                                    global $wpdb;
                                    $bike = $wpdb->get_row($wpdb->prepare(
                                        "SELECT name FROM {$wpdb->prefix}emi_bike_models WHERE id = %d",
                                        $bike_id
                                    ));
                                    echo esc_html($bike->name ?? 'N/A');
                                }
                                ?>
                            </td>
                            <td><span class="badge badge-<?php echo esc_attr($status); ?>"><?php echo esc_html(ucfirst($status)); ?></span></td>
                            <td><?php echo esc_html(get_the_date('d M Y', $app->ID)); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p style="color: #6b7280;">No applications yet.</p>';
        }
        ?>
    </div>
</div>
