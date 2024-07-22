<?php
global $wpdb;
$appraisalID = sanitize_text_field($_POST['appraisalID']);
$vin = sanitize_text_field($_POST['vin']);
$make = sanitize_text_field($_POST['make']);
$model = sanitize_text_field($_POST['model']);
$year = sanitize_text_field($_POST['vyear']);
$body = sanitize_text_field($_POST['body']);
$vtype = sanitize_text_field($_POST['vehicleType']);
$color = sanitize_text_field($_POST['color']);
$priorNadaTradeInValue = sanitize_text_field($_POST['priorNadaTradeInValue']);
$odometer = sanitize_text_field($_POST['odometer']);
$hasKeys = sanitize_text_field($_POST['hasKeys']);
$runningStatus = sanitize_text_field($_POST['runningStatus']);
$additionalInformation = sanitize_text_field($_POST['additionalInformation']);
$status = sanitize_text_field($_POST['status']);
$diminishedValue = sanitize_text_field($_POST['diminishedValue']);
$appraisedValue = sanitize_text_field($_POST['appraisedValue']);
$overview = sanitize_text_field($_POST['overview']);
$exteriorAppraisal1 = sanitize_text_field($_POST['exteriorAppraisal1']);
$exteriorAppraisal2 = sanitize_text_field($_POST['exteriorAppraisal2']);
$exteriorAppraisal3 = sanitize_text_field($_POST['exteriorAppraisal3']);
$exteriorAppraisal4 = sanitize_text_field($_POST['exteriorAppraisal4']);
$exteriorAppraisal5 = sanitize_text_field($_POST['exteriorAppraisal5']);
$exteriorAppraisalSummary = sanitize_text_field($_POST['exteriorAppraisalSummary']);
$interiorAppraisalInstruments = sanitize_text_field($_POST['interiorAppraisalInstruments']);
$interiorAppraisalInstrumentsNotes = sanitize_text_field($_POST['interiorAppraisalInstrumentsNotes']);
$interiorAppraisalUpholstery = sanitize_text_field($_POST['upholstery']);
$interiorAppraisalTrim = sanitize_text_field($_POST['trim']);
$interiorAppraisalCarpets = sanitize_text_field($_POST['carpets']);
$interiorAppraisalUpholsteryTrimCarpetsNotes = sanitize_text_field($_POST['interiorAppraisalUpholsteryTrimCarpetsNotes']);
$interiorAppraisalSummary = sanitize_text_field($_POST['interiorAppraisalSummary']);
$quickbooks = sanitize_text_field($_POST['quickbooks']);

// update the appraisals table
$wpdb->update('appraisals', array(
    'vin' => $vin,
    'make' => $make,
    'model' => $model,
    'year' => $year,
    'bodyType' => $body,
    'vehicleType' => $vtype,
    'color' => $color,
    'priorNadaTradeInValue' => $priorNadaTradeInValue,
    'odometer' => $odometer,
    'hasKeys' => $hasKeys,
    'runningStatus' => $runningStatus,
    'additionalInformation' => $additionalInformation,
    'status' => $status,
    'diminishedValueEstimate' => $diminishedValue,
    'appraisedValue' => $appraisedValue,
    'overview' => $overview,
    'exteriorAppraisal1' => $exteriorAppraisal1,
    'exteriorAppraisal2' => $exteriorAppraisal2,
    'exteriorAppraisal3' => $exteriorAppraisal3,
    'exteriorAppraisal4' => $exteriorAppraisal4,
    'exteriorAppraisal5' => $exteriorAppraisal5,
    'exteriorAppraisalSummary' => $exteriorAppraisalSummary,
    'interiorAppraisalInstruments' => $interiorAppraisalInstruments,
    'interiorAppraisalInstrumentsNotes' => $interiorAppraisalInstrumentsNotes,
    'interiorAppraisalUpholstery' => $interiorAppraisalUpholstery,
    'interiorAppraisalTrim' => $interiorAppraisalTrim,
    'interiorAppraisalCarpets' => $interiorAppraisalCarpets,
    'interiorAppraisalAdditionalNotes' => $interiorAppraisalUpholsteryTrimCarpetsNotes,
    'interiorAppraisalSummary' => $interiorAppraisalSummary,
), array('appraisalID' => $appraisalID));

$preliminaryDamage = '';
if (isset($_POST['preliminaryDamage'])) {

    // delete from appraisals_catalog where appraisalID = $appraisalID
    $wpdb->delete('appraisals_catalog', array('appraisalID' => $appraisalID));

    foreach ($_POST['preliminaryDamage'] as $term) {
        $term = sanitize_text_field($term);

        // insert into appraisals_catalog
        $wpdb->insert('appraisals_catalog', array('appraisalID' => $appraisalID, 'catalogID' => $term));

    }
}

if (preg_match('/intuit.com/', $quickbooks)) {

    include AIAA_PLUGIN_DIR . 'admin/fetch-quickbooks.php';

}
wp_redirect(admin_url('admin.php?page=aiaa_reports&success=update&vin=' . $vin));