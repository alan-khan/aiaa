<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
global $wpdb;
if (isset($_GET['company_id'])) {

    $company_id = $_GET['company_id'];

    // Prepare the SQL query with a parameterized query to avoid SQL injection
    $query = $wpdb->prepare("
        SELECT *
        FROM appraisals as a
        WHERE a.companyID = %d
    ", $company_id);

    // Execute the query
    $appraisal = $wpdb->get_results($query);
    $html = '';
    // Output or process the results as needed
    foreach ($appraisal as $appre) {
        $html .= '<option value="' . esc_attr($appre->appraisalID) . '">' . esc_html($appre->vin) . '</option>';
    }

    echo $html;
}


