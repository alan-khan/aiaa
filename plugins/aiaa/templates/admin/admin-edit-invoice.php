<script>
    function submitForm() {
        document.getElementById("submit").value = "Processing...";
    }
</script>
<style>
    input[type="email"],
    input[type="password"],
    input[type="text"] {
        width: 100%; /* Ensure inputs take up full box width */
        max-width: 500px;
    }

    label {
        display: block;
        margin-top: 1em;
    }

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
</style>
<?php

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    wp_redirect(admin_url('admin.php?page=aiaa_invoices&selectId'));
}

if (current_user_can('administrator') || current_user_can('manager')) {

global $wpdb;

$error_message = [];


if (isset($_POST['submit']) && $_POST['action'] == 'invoices') {

    $status = sanitize_text_field($_POST['status']);
    $quickbooks = sanitize_text_field($_POST['quickbooks']);

    // Update Invoice
    $invoice_data = array(
        'status' => $status,
        'quickbooksLink' => $quickbooks,
    );

   $wpdb->update('invoices', $invoice_data, array('invoiceID' => sanitize_text_field($_POST['id'])));

   // Include fetch-quickbooks.php to check quickbooks again
    if (preg_match('/intuit.com/', $quickbooks)) {
        include AIAA_PLUGIN_DIR . 'admin/fetch-quickbooks.php';
    }

    wp_redirect(admin_url('admin.php?page=aiaa_invoices&success=update'));
    exit;

}
$invoiceID = sanitize_text_field($_GET['id']);
$invoice_result = $wpdb->get_results("
                SELECT inv.*, a.vin, c.name 
                FROM invoices AS inv
                INNER JOIN appraisal_invoice AS ai ON ai.invoiceID = inv.invoiceID
                INNER JOIN appraisals AS a ON a.appraisalID = ai.appraisalID
                INNER JOIN company AS c ON c.companyID = a.companyID
                WHERE inv.invoiceID = '" . $invoiceID . "'
        ");

    $date = new DateTime($invoice_result[0]->dueDate);
    $formattedDate = $date->format('F j, Y');

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php if (isset($_POST['submit']) && sizeof($error_message) == 0) { ?>
        <div class="success">The Invoice has been updated successfully.</div>
    <?php } else if (!isset($_POST['submit']) || (isset($_POST['submit']) && sizeof($error_message) > 0)) {

        if (sizeof($error_message) > 0) { ?>
            <div class="error">
                <p>Please correct the following error(s):</p>

                <?php

                echo '<ul>';

                foreach ($error_message as $message) {
                    echo '<li>' . $message . '</li>';
                }

                echo '</ul>';
                ?>
            </div>
        <?php }
    }
    ?>
    <form id="formInvoice" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <input name="action" type="hidden" value="invoices"/>
        <input name="id" type="hidden" value="<?php echo $invoiceID; ?>"/>
        <p>
            <strong>Company Name: </strong> <?php echo $invoice_result[0]->name; ?>
        </p>
        <p>
            <strong>Invoice ID: </strong> <?php echo $invoice_result[0]->invoiceID; ?>
        </p>
        <p>
            <strong>VIN: </strong> <?php echo $invoice_result[0]->vin; ?>
        </p>
        <p>
            <strong>Due Date: </strong> <?php echo $formattedDate; ?>
        </p>
        <p>
            <strong>Amount: </strong> <?php echo $invoice_result[0]->amount; ?>
        </p>
        <p>
            <label for="quickbooks">Quickbooks Link</label>
            <input type="text" name="quickbooks" id="quickbooks" value="<?php echo $invoice_result[0]->quickbooksLink; ?>"
                   required>
        </p>
        <p>
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="Invoiced" <?php echo ( $invoice_result[0]->dueDate <= date("Y-m-d") && $invoice_result[0]->status === 'Invoiced' ) ? 'selected' : ''; ?>>Invoiced</option>
                <option value="Paid" <?php echo ( $invoice_result[0]->dueDate <= date("Y-m-d") && $invoice_result[0]->status === 'Paid' ) ? 'selected' : ''; ?>>Paid</option>
                <option value="Overdue" <?php echo ( $invoice_result[0]->dueDate <= date("Y-m-d") || $invoice_result[0]->status === 'Overdue' ) ? 'selected' : ''; ?>>Overdue</option>

            </select>
        </p>

        <p>
            <input type="submit" id="submit" name="submit" class="button  button-primary" value="Update Invoice" onclick="submitForm()"/>
            <button type="button" class="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aiaa_invoices'); ?>'">Cancel</button>
        </p>
    </form>


    </form>
</div>
<?php
} else {
    echo 'You do not have permission to view this page.';
}