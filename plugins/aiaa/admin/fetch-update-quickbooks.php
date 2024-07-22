<?php
error_log('Updating invoices status');
global $wpdb;

// retrieve all records from invoices and appraisals_invoice table
$invoices = $wpdb->get_results("SELECT * FROM invoices WHERE status != 'Paid' AND quickbooksLink IS NOT NULL");
// loop through each record
foreach ($invoices as $invoice) {
    $quickbooks = $invoice->quickbooksLink;
    $appraisalID = $wpdb->get_results("SELECT appraisalID FROM appraisal_invoice WHERE invoiceID = '$invoice->invoiceID'")[0]->appraisalID;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $quickbooks);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $invoicePageContent = curl_exec($ch);
    curl_close($ch);


    $pattern = '/<ul.*invoice-info">(.*?)<\/ul>/';

    preg_match($pattern, $invoicePageContent, $matches);

    if (!empty($matches)) {
        $patternLI = '/<li(.*?)>(.*?)<\/li>/';
        preg_match_all($patternLI, $matches[1], $matches2);

        if (!empty($matches2)) {
            $invoiceNumber = null;
            $dueDate = null;
            $amount = null;
            foreach ($matches2[0] as $match) {

                $match = strip_tags($match);
                $match = trim($match);

                if (strpos($match, 'Invoice amount') !== false) {
                    $amount = str_replace('Invoice amount', '', $match);
                } elseif (strpos($match, 'Invoice') !== false) {
                    $invoiceNumber = str_replace('Invoice', '', $match);
                } elseif (strpos($match, 'Due date') !== false) {
                    $dueDate = str_replace('Due date', '', $match);
                }
            }

            if ($dueDate) {
                $dueDate = date('Y-m-d', strtotime($dueDate));
            }


            if ($invoiceNumber) {

                // check payment status, if available
                $patternStatus = '/<div.*payment-amount-wrapper">(.*?)<\/span>/';
                preg_match($patternStatus, $invoicePageContent, $matchesStatus);

                $paymentStatus = $matchesStatus[1] ? trim(strip_tags($matchesStatus[1])) : null;


                if ($paymentStatus !== 'Paid' && $dueDate <= date("Y-m-d")) {
                    $statusToBeSaved = 'Overdue';
                } elseif ($paymentStatus === 'Paid') {
                    $statusToBeSaved = 'Paid';
                } else {
                    $statusToBeSaved = 'Invoiced';
                }

                // update the invoice status in the database
                $wpdb->update('invoices', array('status' => $statusToBeSaved), array('invoiceID' => $invoiceNumber));

            }
        }
    }
}
