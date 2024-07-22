<?php

global $wpdb;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $quickbooks);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$invoicePageContent = curl_exec($ch);
curl_close($ch);


$pattern = '/<ul.*invoice-info">(.*?)<\/ul>/';

preg_match($pattern, $invoicePageContent, $matches);
//var_dump($matches);
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


            $invoiceExists = $wpdb->get_results("SELECT * FROM invoices WHERE invoiceID = '$invoiceNumber'");

            if ($invoiceExists[0]) {
                $savedStatus = $invoiceExists[0]->status;
            }

            if ($savedStatus) {
                $statusToBeSaved = $savedStatus;
            } elseif ($paymentStatus === 'Paid') {
                $statusToBeSaved = 'Paid';
            } else {
                $statusToBeSaved = 'Invoiced';
            }

            // insert to invoices table
            $wpdb->replace('invoices', array(
                'invoiceID' => $invoiceNumber,
                'name' => '',
                'dueDate' => $dueDate,
                'amount' => $amount,
                'status' => $statusToBeSaved,
                'quickbooksLink' => $quickbooks,
            ));

        // upsert the appraisals_invoice table
        if (!$invoiceExists[0]) {
            $wpdb->replace('appraisal_invoice', array(
                'appraisalID' => $appraisalID,
                'invoiceID' => $invoiceNumber,
            ));
        }
    }


    }
}
