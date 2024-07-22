<?php

// Vin form page
$vinForm = '/start-appraisal';

if (isset($_POST['vin'])) {
    $vin = sanitize_text_field(trim($_POST['vin']));
    $vtype = sanitize_text_field(trim($_POST['vtype']));
} else {
    // Redirect back to the vin page with a message
    wp_redirect(home_url( $vinForm . '?message=invalid'));
}

// API URL for the vin lookup
$api = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/$vin?format=json";

// Use file_get_contents to GET the URL in question.
$response = file_get_contents($api);

// If $response is not false, decode it with json_decode
if ($response !== false) {
    $data = json_decode($response);
    $make = '';
    $model = '';
    $year = '';
    $body = '';
    $vehicleType = '';

    // Check if the decoding was successful
    if ($data !== null) {

        // Loop through each property of $data
        foreach ($data->Results as $result) {
            if ($result->Variable) {

                if ($result->Variable == "Make") {
                    $make = $result->Value;
                }

                if ($result->Variable == "Model") {
                    $model = $result->Value;
                }

                if ($result->Variable == "Model Year") {
                    $year = $result->Value;
                }

                if ($result->Variable == "Body Class") {
                    $body = $result->Value;
                }

                if ($result->Variable == "Vehicle Type") {
                    $vehicleType = $result->Value;
                }
            }
        }

        // if make, model, year, and body are empty, redirect back to the vin page with a message
        if ($make == '' && $model == '' && $year == '' && $body == '') {
            wp_redirect(home_url( $vinForm . '?message=invalid&vin=' . $vin));
        } else {
            $vtype = $vehicleType !== '' ? $vehicleType : $vtype;
            // redirect to appraisal-form with the query string fo each field
             wp_redirect(home_url( '/verify-vehicle?make=' . $make . '&model=' . $model . '&vyear=' . $year . '&body=' . $body . '&vin=' . $vin . '&vtype=' . $vtype));
        }

    } else {
        echo "Error: Unable to decode JSON response.";
        wp_redirect(home_url( $vinForm . '?message=invalid'));
    }
} else {
    echo "Error: Unable to retrieve URL.";
    wp_redirect(home_url( $vinForm . '?message=invalid'));
}