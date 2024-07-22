<?php
if (isset($_GET['deleteCompany'])) {
    if (isset($_GET['id'])) {
        global $wpdb;
        $id = sanitize_text_field($_GET['id']);

        $wpdb->delete('locations', array('companyID' => $id));

        $wpdb->delete('user_company', array('companyID' => $id));

        $result = $wpdb->delete('company', array('companyID' => $id));

        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=aiaa_companies&error=delete'));
        } else {
            wp_redirect(admin_url('admin.php?page=aiaa_companies&success=delete'));
        }
    }
}

if (current_user_can('administrator') || current_user_can('manager')) {
    global $wpdb;

    $companies = $wpdb->get_results("
        SELECT c.*, l.*
        FROM company as c
        INNER JOIN locations as l ON c.companyID = l.companyID
        ORDER BY c.name ASC
        ");
    ?>

    <div class="wrap">
        <h1>Companies
            <a href="<?php echo admin_url('admin.php?page=aiaa_add_company'); ?>" class="page-title-action">Add New
                Company</a>
            <a href="<?php echo admin_url('admin.php?page=aiaa_add_client'); ?>" class="page-title-action">Add New
                Client</a>
        </h1>
        <p>This page lists all Companies saved in the database.</p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th>Company Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($companies) {
                foreach ($companies as $company) {
                    ?>
                    <tr>
                        <td><?php echo $company->name; ?></td>
                        <td><?php echo $company->email; ?></td>
                        <td><?php echo $company->phone; ?></td>
                        <td><?php echo $company->address; ?></td>
                        <td><?php echo $company->city; ?></td>
                        <td><?php echo $company->state; ?></td>
                        <td><?php echo $company->zip; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_edit_company&id=' . $company->companyID); ?>" class="button button-secondary">Edit</a>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_companies&deleteCompany&id=' . $company->companyID) ?>" onclick="return window.confirm('Are you sure you want to delete the company <?php echo $company->name;?>?')" class="button button-danger">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    echo '<h1>Sorry, you do not have permission to view this page.</h1>';
}