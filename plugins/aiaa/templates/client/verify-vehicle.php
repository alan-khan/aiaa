<?php
// Parsing the incoming data
if (isset($_GET['make'])) {
    $make = sanitize_text_field(trim($_GET['make']));
    $model = sanitize_text_field(trim($_GET['model']));
    $year = sanitize_text_field(trim($_GET['vyear']));
    $body = sanitize_text_field(trim($_GET['body']));
    $vin = sanitize_text_field(trim($_GET['vin']));
    $vtype = sanitize_text_field(trim($_GET['vtype']));
} else {
    wp_redirect(home_url('/start-appraisal?message=invalid'));
}
?>

<div class="welcome">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Confirm Vehicle Details</h2>
                <p>Click "Next" to proceed with an appraisal for the following vehicle:
                </p>
            </div>
        </div>
        <form action="<?php echo home_url('/new-appraisal'); ?>" method="get" name="verify-vehicle">
            <input type="hidden" name="make" value="<?php echo $make; ?>" />
            <input type="hidden" name="model" value="<?php echo $model; ?>" />
            <input type="hidden" name="vyear" value="<?php echo $year; ?>" />
            <input type="hidden" name="body" value="<?php echo $body; ?>" />
            <input type="hidden" name="vin" value="<?php echo $vin; ?>" />
            <input type="hidden" name="vtype" value="<?php echo $vtype; ?>" />
            <div class="row">
                <div class="col">
                    <h3>Start Report For:</h3>
                    <p>Make: <?php echo $make; ?></p>
                    <p>Model: <?php echo $model; ?></p>
                    <p>Year: <?php echo $year; ?></p>
                    <p>Body Type: <?php echo $body; ?></p>
                    <p>VIN: <?php echo $vin; ?></p>
                    <p>Vehicle Type: <?php echo $vtype; ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col right">
                    <a href="<?php echo home_url('/start-appraisal?vin=' . $vin); ?>">Go Back</a>
                    <input type="submit" name="vin_submit" class="primary-button" value="Next" />
                </div>
            </div>
        </form>
    </div>
</div>