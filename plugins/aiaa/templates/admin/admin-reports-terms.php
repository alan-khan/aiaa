<?php
if (current_user_can('administrator') || current_user_can('manager')) {
    global $wpdb;

    if (isset($_GET['deleteTerm'])) {
        if (isset($_GET['id'])) {
            global $wpdb;
            $id = sanitize_text_field($_GET['id']);

            $wpdb->delete('appraisals_catalog', array('catalogID' => $id));
            $wpdb->delete('catalog', array('catalogID' => $id));
            wp_redirect(admin_url('admin.php?page=aiaa_report_terms&error=delete'));

        }
    }


    $terms = $wpdb->get_results("
        SELECT * FROM catalog ORDER BY name ASC
        ");
    ?>
    <div class="wrap">
        <h1>
            Preliminary Damage Terms
            <a href="<?php echo admin_url('admin.php?page=aiaa_add_report_term'); ?>" class="page-title-action">Add New
                Term</a>
        </h1>
        <p>On this page you will find a list of all Preliminary Damage Terms.</p>

        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th>Term</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($terms) {
                foreach ($terms as $term) {
                    ?>
                    <tr>
                        <td><?php echo $term->name; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_edit_report_term&catalogID=' . $term->catalogID); ?>"
                               class="button button-secondary">Edit</a>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_report_terms&deleteTerm&id=' . $term->catalogID) ?>"
                               onclick="return window.confirm('Are you sure you want to delete the term <?php echo $term->name; ?>?')"
                               class="button button-danger">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td>No Preliminary Damage Terms found.</td>
                    <td></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    echo 'You do not have permission to view this page.';
}