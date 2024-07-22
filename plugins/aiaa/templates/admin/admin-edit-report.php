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

    textarea {
        width: 100%; /* Ensure inputs take up full box width */
        height: 100px;
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

    .uploadImage {
        width: 70px;
        height: 70px;
        border-radius: 10px;
    }

    .toZoom {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .images {
        display: flex;
        gap: 10px;
    }

    .toZoom:hover {
        opacity: 0.7;
    }

    .modal {
        display: none; /* Hidden by default */
        z-index: 1; /* Sit on top */
        width: 50%; /* Full width */
        height: 50%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0, 0, 0); /* Fallback color */
        background-color: rgba(0, 0, 0, 0.9); /* Black w/ opacity */
        position: relative;
    }

    /* Modal Content (image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 100%;
        height: 50%;
    }

    /* Add Animation */
    .modal-content {
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @keyframes zoom {
        from {
            transform: scale(0.1)
        }
        to {
            transform: scale(1)
        }
    }

    /* The Close Button */
    .close {
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        position: absolute;
        right: 7px;
        top: 9px;

    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

</style>
<?php
global $wpdb;

if (isset($_POST['submit'])) {
    include AIAA_PLUGIN_DIR . 'admin/edit-appraisal.php';
}


if ($_GET['appraisalID'] || isset($_POST['appraisalID'])) {

    $appraisalID = isset($_GET['appraisalID']) ? sanitize_text_field($_GET['appraisalID']) : sanitize_text_field($_POST['appraisalID']);

    $query = $wpdb->prepare("SELECT * FROM appraisals WHERE appraisalID = %s", $appraisalID);

    $result = $wpdb->get_row($query);
    if (!$result) {
        wp_redirect(admin_url('admin.php?page=aiaa_reports'));
    }

    $meta_id = get_post_meta($appraisalID);

    $vin = $result->vin;
    $make = $result->make;
    $model = $result->model;
    $year = $result->year;
    $body = $result->bodyType;
    $vtype = $result->vehicleType;
    $color = $result->color;
    $priorNadaTradeInValue = $result->priorNadaTradeInValue;
    $odometer = $result->odometer;

    $terms = $wpdb->get_results("SELECT * FROM catalog ORDER BY name ASC");

    $savedTerms[] = '';
    $savedAppraisalCatalog = $wpdb->get_results("SELECT catalogID FROM appraisals_catalog WHERE appraisalID = $appraisalID");
    foreach ($savedAppraisalCatalog as $term) {
        $savedTerms[] = $term->catalogID;
    }

    // Retrieve Invoice
    $appraisalInvoice = $wpdb->get_row("SELECT * FROM appraisal_invoice WHERE appraisalID = $appraisalID");
    if( $appraisalInvoice ) {
        $searchInvoice = $wpdb->get_row("SELECT * FROM invoices WHERE invoiceID = $appraisalInvoice->invoiceID");
        if( $searchInvoice ) {
            $quickbooksLink = $searchInvoice->quickbooksLink;
        } else {
            $quickbooksLink = '';
        }
    } else {
        $quickbooksLink = '';
    }
    ?>

    <div class="wrap">
        <h1>Report</h1>
        <hr/>
        <h3>User Provided Information</h3>
        <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="appraisalID" value="<?php echo $appraisalID ?>">
            <p>
                <label for="vin">VIN</label>
                <input type="text" name="vin" id="vin" value="<?php echo $vin; ?>" required/>
            </p>
            <p>
                <label for="make">Make</label>
                <input type="text" name="make" id="make" value="<?php echo $make; ?>" required/>
            </p>
            <p>
                <label for="make">Model</label>
                <input type="text" name="model" id="model" value="<?php echo $model; ?>" required/>
            </p>
            <p>
                <label for="vyear">Year</label>
                <input type="number" name="vyear" id="vyear" value="<?php echo $year; ?>" required/>
            </p>
            <p>
                <label for="body">Body Style</label>
                <input type="text" name="body" id="body" value="<?php echo $body; ?>" required/>
            </p>
            <p>
                <label for="vehicleType">Vehicle Type</label>
                <input type="text" name="vehicleType" id="vehicleType" value="<?php echo $vtype; ?>" required/>
            </p>
            <p>
                <label for="color">Color</label>
                <input type="text" name="color" id="color" value="<?php echo $color; ?>" required/>
            </p>
            <p>
                <label for="priorNadaTradeInValue">NADA Loan Value</label>
                <input type="text" name="priorNadaTradeInValue" id="priorNadaTradeInValue"
                       value="<?php echo $priorNadaTradeInValue; ?>"/>
            </p>
            <p>
                <label for="hasKeys">Do you have keys to the vehicle?</label>
                <input type="radio"
                       name="hasKeys" <?php echo isset($result) ? ($result->hasKeys == 1 ? 'checked' : '') : ''; ?>
                       value="1"> <span> Yes</span>
                <input type="radio"
                       name="hasKeys" <?php echo isset($result) ? ($result->hasKeys == 0 ? 'checked' : '') : ''; ?>
                       value="0"> <span> No</span>
            </p>

            <p>
                <label for="odometer">*Vehicle Mileage</label>
                <input type="text" name="odometer" id="odometer" class="odometer valid"
                       value="<?php echo isset($result) ? ($result->odometer == 'Unknown' ? 'Unknown' : (isset($result->odometer) ? $result->odometer : '')) : ''; ?>"
                       placeholder="Vehicle Mileage"/>
            </p>

            <p>
                <label for="runningStatus">Run Status</label>
                <input type="checkbox"
                       name="runningStatus" <?php echo isset($result) ? ($result->runningStatus == 'Vehicle Run' ? 'checked' : '') : ''; ?>
                       value="Vehicle Run" class="valid"> <span>
                        Vehicle Run</span>
                <input type="checkbox"
                       name="runningStatus" <?php echo isset($result) ? ($result->runningStatus == 'Vehicle does not start' ? 'checked' : '') : ''; ?>
                       value="Vehicle does not start" class="valid">
                <span> Vehicle does not start</span>
                <input type="checkbox"
                       name="runningStatus" <?php echo isset($result) ? ($result->runningStatus == 'Unknown' ? 'checked' : '') : ''; ?>
                       value="Unknown" class="valid"> <span>Unknown</span>
            </p>

            <p>
                <label>Additional Information</label>
                <textarea
                        name="additionalInformation"><?php echo isset($result) ? $result->additionalInformation : ''; ?></textarea>
            </p>
            <p>
                <label>Images</label>
            <div class="images">
                <!-- IMAGES GO HERE -->
                <?php
                $isThereImage = false;
                foreach ($meta_id as $key => $value) {
                    $start_value = explode('_', $key)[0];
                    $image_url = wp_get_attachment_url($value[0]);
                    $key_array = ['front_attachment_id', 'rear_attachment_id', 'passenger_attachment_id', 'driver_attachment_id', 'other_attachment_id'];
                    if (in_array($key, $key_array)) {
                        $isThereImage = true;
                        ?>
                        <div class="col">
                            <p><?php echo ucfirst($start_value) ?></p>
                            <img src="<?php echo $image_url ?>" class="toZoom uploadImage">
                        </div>
                        <?php
                    }
                }

                if (!$isThereImage) {
                    echo 'No images found';
                }
                ?>
            </div>
            <div class="modal" id="imageView">
                <span class="close">&times;</span>
                <img class="modal-content" id="modal-content">
            </div>
            </p>

            <p>
                <label>Documents</label>
            <div class="images">

                <!-- IMAGES GO HERE -->
                <?php
                $isThereDoc = false;
                foreach ($meta_id as $key => $value) {
                    $start_value = explode('_', $key)[0];
                    $doc_url = wp_get_attachment_url($value[0]);
                    $key_array = ['nada-1_attachment_id', 'nada-2_attachment_id'];
                    if (in_array($key, $key_array)) {
                        $isThereDoc = true;
                        ?>
                        <a href="<?php echo $doc_url ?>" target="_blank"><?php echo substr(strrchr($doc_url, '/'), 1) ?></a><br/>
                        <?php
                    }
                }

                if (!$isThereDoc) {
                    echo 'No documents found.';
                }
                ?>

            </div>
            </p>
            <hr/>
            <h3>Appraiser Provided Information</h3>

            <p>
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="Pending" <?php echo $result->status == 'Pending' ? 'selected' : ''; ?>>Pending
                    </option>
                    <option value="Completed" <?php echo $result->status == 'Completed' ? 'selected' : ''; ?>>Completed
                    </option>
                    <option value="Needs Info" <?php echo $result->status == 'Needs Info' ? 'selected' : ''; ?>>Needs
                        Info
                    </option>
                    <option value="Deleted" <?php echo $result->status == 'Deleted' ? 'selected' : ''; ?>>Deleted
                    </option>
                </select>
            </p>

            <p>
                <label for="diminishedValue">Diminished Value Estimate</label>
                <input type="number" name="diminishedValue" id="diminishedValue"
                       value="<?php echo $result->diminishedValueEstimate; ?>"/>
            </p>
            <p>
                <label for="appraisalValue">Appraised Vehicle Value</label>
                <input type="number" name="appraisedValue" id="appraisedValue"
                       value="<?php echo $result->appraisedValue; ?>"/>
            </p>
            <p>
                <label for="overview">Overview</label>
                <textarea name="overview"><?php echo $result->overview; ?></textarea>
            </p>

            <!-- EXTERIOR APPRAISAL -->
            <p>
                <label for="exteriorAppraisal1">Exterior Appraisal (Front)</label>
                <textarea name="exteriorAppraisal1"><?php echo $result->exteriorAppraisal1; ?></textarea>
            </p>
            <p>
                <label for="exteriorAppraisal2">Exterior Appraisal (Drive side)</label>
                <textarea name="exteriorAppraisal2"><?php echo $result->exteriorAppraisal2; ?></textarea>
            </p>
            <p>
                <label for="exteriorAppraisal3">Exterior Appraisal (Passenger side)</label>
                <textarea name="exteriorAppraisal3"><?php echo $result->exteriorAppraisal3; ?></textarea>
            </p>
            <p>
                <label for="exteriorAppraisal4">Exterior Appraisal (Rear)</label>
                <textarea name="exteriorAppraisal4"><?php echo $result->exteriorAppraisal4; ?></textarea>
            </p>
            <p>
                <label for="exteriorAppraisal5">Exterior Appraisal (Hood)</label>
                <textarea name="exteriorAppraisal5"><?php echo $result->exteriorAppraisal5; ?></textarea>
            </p>

            <p>
                <label for="exteriorAppraisalSummary">Exterior Appraisal Summary</label>
                <select name="exteriorAppraisalSummary" id="exteriorAppraisalSummary">
                    <option value="">Select option</option>
                    <option value="Good" <?php echo $result->exteriorAppraisalSummary == 'Good' ? 'selected' : ''; ?>>
                        Good
                    </option>
                    <option value="Average" <?php echo $result->exteriorAppraisalSummary == 'Average' ? 'selected' : ''; ?>>
                        Average
                    </option>
                    <option value="Poor" <?php echo $result->exteriorAppraisalSummary == 'Poor' ? 'selected' : ''; ?>>
                        Poor
                    </option>
                </select>
            </p>

            <!-- INTERIOR APPRAISAL -->
            <p>
                <label for="interiorAppraisalInstruments">Interior Appraisal Instruments (list defects)</label>
                <textarea
                        name="interiorAppraisalInstruments"><?php echo $result->interiorAppraisalInstruments; ?></textarea>
            </p>

            <p>
                <label for="interiorAppraisalInstrumentsNotes">Interior Appraisal Instruments - NOTES</label>
                <textarea
                        name="interiorAppraisalInstrumentsNotes"><?php echo $result->interiorAppraisalInstrumentsNotes; ?></textarea>
            </p>

            <p>
                <label for="upholstery">Upholstery</label>
                <select name="upholstery" id="upholstery">
                    <option value="">Select option</option>
                    <option value="Defect" <?php echo $result->interiorAppraisalUpholstery == 'Defect' ? 'selected' : ''; ?>>
                        Defect
                    </option>
                    <option value="OK" <?php echo $result->interiorAppraisalUpholstery == 'OK' ? 'selected' : ''; ?>>
                        OK
                    </option>
                </select>
            </p>

            <p>
                <label for="trim">Trim</label>
                <select name="trim" id="trim">
                    <option value="">Select option</option>
                    <option value="Defect" <?php echo $result->interiorAppraisalTrim == 'Defect' ? 'selected' : ''; ?>>
                        Defect
                    </option>
                    <option value="OK" <?php echo $result->interiorAppraisalTrim == 'OK' ? 'selected' : ''; ?>>OK
                    </option>
                </select>
            </p>

            <p>
                <label for="trim">Carpets</label>
                <select name="carpets" id="carpets">
                    <option value="">Select option</option>
                    <option value="Defect" <?php echo $result->interiorAppraisalCarpets == 'Defect' ? 'selected' : ''; ?>>
                        Defect
                    </option>
                    <option value="OK" <?php echo $result->interiorAppraisalCarpets == 'OK' ? 'selected' : ''; ?>>OK
                    </option>
                </select>
            </p>

            <p>
                <label for="interiorAppraisalUpholsteryTrimCarpetsNotes">Interior Appraisal Instruments (Upholstery,
                    trim, and carpet) - NOTES</label>
                <textarea
                        name="interiorAppraisalUpholsteryTrimCarpetsNotes"><?php echo $result->interiorAppraisalAdditionalNotes; ?></textarea>
            </p>

            <p>
                <label for="interiorAppraisalSummary">Interior Appraisal Summary</label>
                <select name="interiorAppraisalSummary" id="interiorAppraisalSummary">
                    <option value="">Select option</option>
                    <option value="Good" <?php echo $result->interiorAppraisalSummary == 'Good' ? 'selected' : ''; ?>>
                        Good
                    </option>
                    <option value="Average" <?php echo $result->interiorAppraisalSummary == 'Average' ? 'selected' : ''; ?>>
                        Average
                    </option>
                    <option value="Poor" <?php echo $result->interiorAppraisalSummary == 'Poor' ? 'selected' : ''; ?>>
                        Poor
                    </option>
                </select>
            </p>

            <!-- MECHANICAL APPRAISAL -->
            <p>
                <label for="preliminaryDamage">Preliminary Damage/Condition Report:</label>
                <?php
                if ($terms) {
                    foreach ($terms as $term) {
                        ?>
                        <input type="checkbox"
                               name="preliminaryDamage[]"
                               value="<?php echo $term->catalogID; ?>" <?php echo in_array($term->catalogID, $savedTerms) ? 'checked' : ''; ?>>
                        <span><?php echo $term->name; ?></span>
                        <br/>
                        <?php
                    }
                }
                ?>
            </p>
            <p>
                <label for="appraisalValue">Quickbooks Link</label>
                <input type="text" name="quickbooks" id="quickbooks"
                       value="<?php echo $quickbooksLink; ?>"/>
            </p>

            <p>
                <input type="submit" id="submit" name="submit" class="button  button-primary" value="Update Appraisal" onclick="submitForm()"/>
                <button type="button" class="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aiaa_reports'); ?>'">Cancel</button>
            </p>
        </form>
    </div>
    <?php
} else {
    wp_redirect(admin_url('admin.php?page=aiaa_reports'));
}
