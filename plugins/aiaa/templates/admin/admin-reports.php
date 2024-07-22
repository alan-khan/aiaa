<?php
if (isset($_GET['deleteAppraisal'])) {
    if (isset($_GET['id'])) {
        global $wpdb;
        $id = sanitize_text_field($_GET['id']);

        // update status of appraisals to Deleted
        $wpdb->update('appraisals', array('status' => 'Deleted'), array('appraisalID' => $id));
        $appraisalDeleted = true;
    }
}
?>
    <style>
        .error {
            border: 1px solid red;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .success {
            border: 1px solid green;
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .searchBox {
            text-align: right;
            float: right;
            margin-bottom: 20px;
        }
    </style>

<?php
if (current_user_can('administrator') || current_user_can('manager')) {
    global $wpdb;

    $where = '';
    $orderby = "ORDER BY app.createdAt DESC";

    if (isset($_GET['search'])) {
        $search = sanitize_text_field($_GET['search']);
        $where .= " AND app.vin LIKE '%$search%' AND app.status NOT IN ('Draft')";
    }

    if (isset($_GET['orderby'])) {
        $orderby = sanitize_text_field($_GET['orderby']);

        if ($orderby === 'company') {
            $orderby = 'ORDER BY cp.name ASC';
        } else if ($orderby === 'status') {
            $orderby = 'ORDER BY app.status DESC';
        }

    }

    $appraisals = $wpdb->get_results("
        SELECT app.*, cp.name
        FROM appraisals AS app
        INNER JOIN company AS cp ON (app.companyID = cp.companyID)
        INNER JOIN locations AS l ON (app.locationID = l.locationID AND l.companyID = cp.companyID)
        WHERE app.status != 'Draft'
        $where
        $orderby
        ");
    ?>
    <div class="wrap">
        <h1>Reports</h1>

        <?php if (isset($_GET['success'])) { ?>
            <div class="success">The appraisal for VIN <?php echo $_GET['vin']; ?> has been updated successfully.</div>
        <?php } ?>

        <?php if (isset($appraisalDeleted)) { ?>
            <div class="success">The appraisal has been deleted successfully.</div>
        <?php } ?>

        <p>On this page you will find a list of all submitted Appraisal requests.</p>
        <div class="searchBox">
            <form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type="hidden" name="page" value="aiaa_reports">
                <input type="text" name="search" placeholder="Search by VIN"
                       value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <select name="orderby">
                    <option value="">Sort by</option>
                    <option value="company">Company Name</option>
                    <option value="status">Status</option>
                </select>
                <input type="submit" value="Search" class="button">
                <a href="<?php echo admin_url('admin.php?page=aiaa_reports'); ?>" class="button">Reset</a>
            </form>
        </div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th><a href="<?php echo admin_url('admin.php?page=aiaa_reports&orderby=company'); ?>">Company Name</a>
                </th>
                <th>VIN</th>
                <th>Make</th>
                <th>Model</th>
                <th>Year</th>
                <th><a href="<?php echo admin_url('admin.php?page=aiaa_reports&orderby=status'); ?>">Status</a></th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($appraisals) {
                foreach ($appraisals as $appraisal) {
                    ?>
                    <tr>
                        <td><?php echo $appraisal->name; ?></td>
                        <td><?php echo $appraisal->vin; ?></td>
                        <td><?php echo $appraisal->make; ?></td>
                        <td><?php echo $appraisal->model; ?></td>
                        <td><?php echo $appraisal->year; ?></td>
                        <td <?php if ($appraisal->status === 'Deleted') {
                            echo 'style="color:red;"';
                        } ?>><?php echo $appraisal->status; ?></td>
                        <td>
                            <?php if ($appraisal->status != 'Deleted') { ?>
                                <a href="<?php echo admin_url('admin.php?page=aiaa_edit_report&appraisalID=' . $appraisal->appraisalID); ?>"
                                   class="button button-primary">View</a>
                                <a href="<?php echo admin_url('admin.php?page=aiaa_reports&deleteAppraisal&id=' . $appraisal->appraisalID) ?>"
                                   onclick="return window.confirm('Are you sure you want to delete the appraisal for VIN <?php echo $appraisal->vin; ?>?')"
                                   class="button button-danger">Delete</a>
                            <?php } ?>
                            <?php if ($appraisal->status == 'Completed') { ?>
                                <a href="<?php echo home_url('pdf?appraisalID=' . $appraisal->appraisalID); ?>" target="_blank" class="button download">PDF</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="7">No Appraisals found.</td>
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