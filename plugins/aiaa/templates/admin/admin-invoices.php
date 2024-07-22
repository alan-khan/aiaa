<script>
    function submitForm() {
        document.getElementById("updateInvoices").innerHTML = "Processing updates...";
    }
</script>
<?php
if (current_user_can('administrator') || current_user_can('manager')) {

    global $wpdb;

    if ( isset($_GET["action"]) && $_GET["action"] == "updateInvoices" ) {
        include AIAA_PLUGIN_DIR . 'admin/fetch-update-quickbooks.php';
    }

    if (isset($_GET['deleteInvoice'])) {
        if (isset($_GET['id'])) {
            global $wpdb;
            $id = sanitize_text_field($_GET['id']);

            // update status of appraisals to Deleted
            $wpdb->delete('appraisal_invoice', array('invoiceID' => $id));
            $wpdb->delete('invoices', array('invoiceID' => $id));
            $appraisalDeleted = true;
        }
    }

    $where = '';

    if (isset($_GET['search'])) {
        $search = sanitize_text_field($_GET['search']);
        $where .= "WHERE a.vin LIKE '%$search%'";
    }

    $invoiced = 0;
    $paid = 0;
    $overdue = 0;
    $invoicesFilters = $wpdb->get_results(" SELECT * FROM invoices WHERE status != 'Deleted'; ");

    foreach ($invoicesFilters as $invoiceFilter) {
        if ($invoiceFilter->status === 'Invoiced') {
            $invoiced++;
        } elseif ($invoiceFilter->status === 'Paid') {
            $paid++;
        } elseif ($invoiceFilter->status === 'Overdue') {
            $overdue++;
        }
    }

    if( isset($_GET['filter']) ) {
        $filter = sanitize_text_field($_GET['filter']);
        $where .= "WHERE inv.status = '$filter'";
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

        .overdue {
            color: red;
        }

        .paid {
            color: green;
        }

        .searchBox {
            text-align: right;
            float: right;
            margin-bottom: 20px;
        }

        .filters {
            float: left;
        }

        #loading {
            display: block;
            position: fixed;
            z-index: 999;
            height: 100%;
            width: 100%;
            background: rgba(255, 255, 255, 0.8) url('/wp-content/plugins/aiaa/templates/images/loading__.gif') 50% 50% no-repeat;
        }
    </style>

    <div class="wrap">
        <h1>
            Invoices
            <a href="<?php echo admin_url('admin.php?page=aiaa_invoices&action=updateInvoices'); ?>" class="button button-primary" id="updateInvoices" onclick="submitForm();">Update Invoices Status</a>
        </h1>
        <p>List of all the invoices</p>

        <?php if (isset($_GET['success'])) { ?>
        <div class="success">The Invoice has been updated successfully.</div>
    <?php } ?>
        <div class="searchBox">
        <form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <input type="hidden" name="page" value="aiaa_invoices">
            <input type="text" name="search" placeholder="Search by VIN"
                   value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <input type="submit" value="Search" class="button">
            <a href="<?php echo admin_url('admin.php?page=aiaa_invoices'); ?>" class="button">Reset</a>
        </form>
        </div>
        <div class="filters">
            <a href="<?php echo admin_url('admin.php?page=aiaa_invoices'); ?>">All (<?php echo $invoiced + $paid + $overdue; ?>)</a> |
            <a href="<?php echo admin_url('admin.php?page=aiaa_invoices&filter=Invoiced'); ?>">Invoiced (<?php echo $invoiced; ?>)</a> |
            <a href="<?php echo admin_url('admin.php?page=aiaa_invoices&filter=Paid'); ?>">Paid (<?php echo $paid; ?>)</a> |
            <a href="<?php echo admin_url('admin.php?page=aiaa_invoices&filter=Overdue'); ?>">Overdue (<?php echo $overdue; ?>)</a>
        </div>
        <div class="clear"></div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th>Company Name</th>
                <th>Invoice ID</th>
                <th>VIN</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            global $wpdb;
            $invoices = $wpdb->get_results("
                SELECT inv.*, a.vin
                FROM invoices AS inv
                INNER JOIN appraisal_invoice AS ai ON ai.invoiceID = inv.invoiceID
                INNER JOIN appraisals AS a ON a.appraisalID = ai.appraisalID
                $where
                ORDER BY inv.dueDate DESC;
            ");

            foreach ($invoices as $invoice) {
                $date = new DateTime($invoice->dueDate);
                $formattedDate = $date->format('F j, Y');

                $invoiceStatusClass = '';
                if ($invoice->status === 'Paid') {
                    $invoiceStatusClass = 'paid';
                } elseif ($invoice->status === 'Overdue') {
                    $invoiceStatusClass = 'overdue';
                }

                if ( $invoice->status !== 'Paid' && $invoice->dueDate <= date("Y-m-d") ) {
                    $invoiceStatusClass = 'overdue';
                    $invoice->status = 'Overdue';
                }

                // query to get the company name
                $companyNameQuery = $wpdb->get_results("
                    SELECT c.name
                    FROM company AS c
                    INNER JOIN appraisals AS a ON a.companyID = c.companyID
                    INNER JOIN appraisal_invoice AS ai ON ai.appraisalID = a.appraisalID
                    WHERE a.vin = '" . $invoice->vin . "'
                ");
                $companyName = $companyNameQuery[0]->name;
                ?>
                <tr>
                    <td><?php echo $companyName; ?></td>
                    <td><?php echo $invoice->invoiceID; ?></td>
                    <td><?php echo $invoice->vin; ?></td>
                    <td><?php echo $formattedDate; ?></td>
                    <td><?php echo $invoice->amount; ?></td>
                    <td>
                        <span class="<?php echo $invoiceStatusClass; ?>"><?php echo $invoice->status; ?></span>
                    </td>
                    <td>
                        <a href="<?php echo $invoice->quickbooksLink; ?>" class="button button-primary" target="_blank">View
                            Invoice</a>
                        <!--<a href="<?php echo admin_url('admin.php?page=aiaa_edit_invoice&id=' . $invoice->invoiceID); ?>" class="button button-secondary">Edit</a>-->
                        <a href="<?php echo admin_url('admin.php?page=aiaa_invoices&deleteInvoice&id=' . $invoice->invoiceID) ?>"
                           onclick="return window.confirm('Are you sure you want to delete the invoice number <?php echo $invoice->invoiceID; ?> for VIN <?php echo $invoice->vin; ?>?')"
                           class="button button-danger">Delete</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
    </div>
    <?php
}
