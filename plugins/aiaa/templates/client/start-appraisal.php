<div class="loader">
    <div class="center">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<style>
    .error-message {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
        padding: 15px;
        margin-bottom: 20px;
        margin-top: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
</style>
<?php
// Process form submission
if ( isset($_POST['vin']) ) {
    include AIAA_PLUGIN_DIR . 'client/vin-lookup.php';
    exit;
}

// Get VIN from URL
$vin = isset($_GET['vin']) ? sanitize_text_field($_GET['vin']) : '';
?>
<div class="welcome">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="alert alert-danger" hidden>
                    <strong>Whoops!</strong> Please fill in the following fields:
                    <div class="errors"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h2>Welcome!</h2>
                <p>To get started with an appraisal, please select the type of vehicle and enter VIN below.</p>
            </div>
        </div>

        <?php if (isset($_GET['message']) && $_GET['message'] === 'invalid') { ?>
            <div class="row">
                <div class="col">
                    <div class="error-message">
                        <p>Invalid VIN (<?php echo $vin; ?>). Please try again.</p>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (isset($_COOKIE['message'])): ?>
            <div class="row alert-success"><?php echo esc_html($_COOKIE['message']); ?></div>
        <?php endif; ?>
        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="startAppraisalValidation"
            method="post" name="vehicle_vin">
            <div class="row">
                <div class="col">
                    <select name="vtype" class="valid">
                        <option value="">Vehicle Type</option>
                        <option value="Car | Truck">Car / Truck</option>
                        <option value="Motorcycle / Scooter">Motorcycle / Scooter</option>
                        <option value="Trailer">Trailer</option>
                        <option value="Heavy Duty Commercial Vehicle">Heavy Duty Commercial Vehicle</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <input type="text" name="vin" class="valid" placeholder="VEHICHLE VIN"
                        value="<?php echo $vin; ?>" />
                    <input type="submit" name="vin_submit" class="primary-button" value="Next" />
                    or <a href="<?php echo home_url('/new-appraisal?manual'); ?>" class="blue">Enter car information
                        manually</a>
                </div>
            </div>
        </form>
    </div>
</div>