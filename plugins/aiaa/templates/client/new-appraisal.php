<?php
global $wpdb;

// Have to handle the delete of image here due to GoDaddy's security constraint
if (isset($_GET['deleteImage']) && isset($_GET['id']) && isset($_GET['filekey'])) {
    include AIAA_PLUGIN_DIR . 'client/delete-appraisal-image.php';
    exit;
}

if (isset($_GET['make'])) {
    $make = sanitize_text_field(trim($_GET['make']));
    $model = sanitize_text_field(trim($_GET['model']));
    $year = sanitize_text_field(trim($_GET['vyear']));
    $body = sanitize_text_field(trim($_GET['body']));
    $vin = sanitize_text_field(trim($_GET['vin']));
    $vtype = sanitize_text_field(trim($_GET['vtype']));
    $appraisalID = isset($_GET['appraisalID']) ? sanitize_text_field(trim($_GET['appraisalID'])) : null;
    $message_content = "Thank you for your submission. We'll get to work on it!";

    if ($appraisalID) {
        $query = $wpdb->prepare("SELECT * FROM appraisals WHERE appraisalID = %s", $appraisalID);
    } else {
        $query = $wpdb->prepare("SELECT * FROM appraisals WHERE vin = %s", $vin);
    }
    $result = $wpdb->get_row($query);
    if (!empty($result)) {
        if ($result->status !== 'Draft') {
            $message_content = 'Your appraisal form has been updated .';
        }

        $meta_id = get_post_meta($result->appraisalID);
        $imgId = [];
        foreach ($meta_id as $key => $value) {
            $imgId[$key] = $value[0];
            $image_url[$key] = wp_get_attachment_url($value[0]);
        }

        $imageDelUrl = esc_url($_SERVER['REQUEST_URI']) . '&deleteImage&appr=' . $result->appraisalID . '&id=';
        $frontImg = $imageDelUrl . (isset($imgId['front_attachment_id']) ? $imgId['front_attachment_id'] . '&filekey=front' : '');
        $rearImg = $imageDelUrl . (isset($imgId['rear_attachment_id']) ? $imgId['rear_attachment_id'] . '&filekey=rear' : '');
        $passengerImg = $imageDelUrl . (isset($imgId['passenger_attachment_id']) ? $imgId['passenger_attachment_id'] . '&filekey=passenger' : '');
        $driverImg = $imageDelUrl . (isset($imgId['driver_attachment_id']) ? $imgId['driver_attachment_id'] . '&filekey=driver' : '');
        $otherImg = $imageDelUrl . (isset($imgId['other_attachment_id']) ? $imgId['other_attachment_id'] . '&filekey=other' : '');
//        $topImg = $imageDelUrl . (isset($imgId['top_attachment_id']) ? $imgId['top_attachment_id'] . '&filekey=top' : '');
        $interiorImg = $imageDelUrl . (isset($imgId['interior_attachment_id']) ? $imgId['interior_attachment_id'] . '&filekey=interior' : '');
        $nada1Img = $imageDelUrl . (isset($imgId['nada-1_attachment_id']) ? $imgId['nada-1_attachment_id'] . '&filekey=nada-1' : '');
        $nada2Img = $imageDelUrl . (isset($imgId['nada-2_attachment_id']) ? $imgId['nada-2_attachment_id'] . '&filekey=nada-2' : '');

    }

} elseif (isset($_GET['manual'])) {

    $make = '';
    $model = '';
    $year = '';
    $body = '';
    $vin = '';
    $vtype = '';

} else {
    wp_redirect(home_url('/start-appraisal?message=invalid'));
}

if ((isset($_POST['submit']) && !empty($_POST['make'])) || (isset($_POST['submit_exit']) && !empty($_POST['make']))) {

    $current_date = current_time('mysql');
    $table_name = 'appraisals';
    $existing_data = '';
    $user_id = get_current_user_id();
    $company_id_query = $wpdb->prepare("SELECT companyID FROM user_company WHERE userID = %d", $user_id);
    $company_id = $wpdb->get_var($company_id_query);
    $location_query = $wpdb->prepare("SELECT locationID FROM locations WHERE companyID = %d", $company_id);
    $location_id = $wpdb->get_var($location_query);
    $data = array(
        'companyID' => $company_id,
        'locationID' => $location_id,
        'make' => sanitize_text_field($_POST['make']),
        'model' => sanitize_text_field($_POST['model']),
        'year' => sanitize_text_field($_POST['vyear']),
        'vin' => sanitize_text_field($_POST['vin']),
        'odometer' => sanitize_text_field($_POST['odometer']),
        'color' => sanitize_text_field($_POST['color']),
        'bodyType' => sanitize_text_field($_POST['body']),
        'vehicleType' => sanitize_text_field($_POST['vehicleType']),
        'hasKeys' => isset($_POST['hasKeys']) ? sanitize_text_field($_POST['hasKeys']) : true,
        'runningStatus' => isset($_POST['runningStatus']) ? sanitize_text_field($_POST['runningStatus']) : 'Unknown',
        'additionalInformation' => isset($_POST['additionalInformation']) ? sanitize_text_field($_POST['additionalInformation']) : '',
        'priorNadaTradeInValue' => isset($_POST['priorNadaTradeInValue']) ? sanitize_text_field($_POST['priorNadaTradeInValue']) : 0,
        'priorNadaTradeInValueMSL' => isset($_POST['priorNadaTradeInValueMSL']) && $_POST['priorNadaTradeInValueMSL'] == 'I have not obtained an MSL' ? 1 : 0,
        'priorNadaTradeInValueUnknown' => isset($_POST['priorNadaTradeInValueUnknown']) && $_POST['priorNadaTradeInValueUnknown'] == 'The NADA value is UNKNOWN' ? 1 : 0,
        'createdBy' => $user_id,
        'updatedBy' => $user_id,
        'approvedBy' => null,
        'status' => isset($_POST['submit']) ? 'Pending' : 'Draft',
        'createdAt' => $current_date,
        'updatedAt' => $current_date,
    );
    if (!empty($_POST['appraisalID'])) {
        $existing_data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE appraisalID = %d", sanitize_text_field($_POST['appraisalID']))
        );
    }

    if ($existing_data) {
        $where = array('appraisalID' => sanitize_text_field($_POST['appraisalID']));
        $appraisalinserted = $wpdb->update($table_name, $data, $where);
        $last_inserted_id = sanitize_text_field($_POST['appraisalID']);
    } else {
        $appraisalinserted = $wpdb->insert($table_name, $data);
        $last_inserted_id = $wpdb->insert_id;
    }

    if ($appraisalinserted === false) {
        echo "<div class='submitMessage alert-danger'>$wpdb->last_error</div>";
    } else {
        if (!empty($_FILES)) {

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            // Handle multiple file uploads
            $attachment_ids = array();
            foreach ($_FILES as $file_key => $file) {
                $attachment_id = media_handle_upload($file_key, $last_inserted_id);
                if (is_wp_error($attachment_id)) {
                    // Handle error
                    $error_message = $attachment_id->get_error_message();
                } else {
                    // File uploaded successfully
                    update_post_meta($last_inserted_id, $file_key . '_attachment_id', $attachment_id);
                    $attachment_ids[] = $attachment_id;

                }
            }
        }
        if (isset($_POST['submit'])) {
            setcookie('message', $message_content, time() + 5, '/');
            wp_redirect(home_url('/my-account'));

        }
        if (isset($_POST['submit_exit'])) {
            setcookie('message', 'Your appraisal form has been saved as a Draft.', time() + 5, '/');
            wp_redirect(home_url('/my-account'));

        }
    }
}
$image_path = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMsAAAD5CAMAAAC+lzGnAAAAyVBMVEX19fX/IRb///8sLCz/AAD1+vr1/Pz6qaf/DwD/HRD7i4j/TUf4vLv0///6q6n6+volJSWysrIYGBjt7e0FBQU/Pz9hYWESEhLY2Ng5OTm/v7/Ly8v/GAn17ewnJydnZ2f30tEeHh725eT6oJ3329r/Lyb8c2/4xcT9YFv9amb8e3f/trT7h4T6mpj+SUP4y8p5eXmkpKSTk5P7kY7/KiD+Ny/9VE725ub8bGjm5ub/4+L+QTr5trT8f3tUVFRKSkp2dnaXl5eqqqoG9JSjAAANx0lEQVR4nO2daV/iPBeHQ0lLigRcmMHRVtlXARcWFR3H+/t/qCdpkjaFqixN2zw//q9UoOTy5JycLD0FxqZeR8XBapZLQfP+cFxaRjRpK4H1P9TfEYTIsdNAydk2It8+K7bjYCnNIUoHQyZCcPV2KMt0DlMHYbJhp3cQSzcrJFQ27Lt7s7zmUNrtDwvB0p4svSwZhcmGd3uxlGDoMg6NJ4kLrcdPONiDpSejIIj6xVY5ebWKgzkhkmH6O7O8SigQFesWtiwzeVmWhd3eUKbZBYax5HzTOrAFLBOkKNMCTxINWuzG0kXBv8G10gRhstw+3AOGskz9D8InnDYHEy4HYRWd7MAyFx+DrYygENPUJZjJ1ixvMGtWobLaO8OAwCxOP0MoJAbIMKvtWOq+Wdy0mx+W2Q5SdjTfiuWdBzE4zkAEC8lsyjCVLVjE+2HaTd9UCKbzIwwQQz66y5pZAIWxHX8U/xEGjDgLrKc62n8hc5kLYGY/TGhAkbsLyqBZAIWZBTC572HAwMliQA5kulvDgJWdXXfxZLqdAMb+bsEJ8HUw1MoqSwjGRt/AADFQFrLo+kwmmKMApqk1SxgGvmrNQrTaAkYXFjD5GUYblhBM9HqzPizmiQRT15sFmIsfYDRiCcNM9WYBVv9bGK1YCEyw1gQ3NjX0YgHW4BsYzVgAlmFKerMA/PwljHYsAHclmDe9WQAeSjAjvVkAfpdgynqzAHwnwRT0ZgnDtPRmAbgYAaMpC8BPmzC6sgA83hhntGUJw7yqZ6H70VjVYhVu+TB2RzWL6T6tIFwVm4poJBjvhIZCFmvqnd+yESyqgikEMK8qWegenQ29YxRwocgZA8s4fZUseG7bTgk0afBEA0Ur79g/mkAMo4zF7MEcbJPOhelZG/imyDJmh++coXd1LNYdQmyPHQ+cnJ1T5DKm2Dq2kToWfOLAJfs6eoAAllS5DN9AIp1MHQtyVsxJSBAgXaCryjDiBAwcqfMXiN5585eQntZVtr8jNo+Lylhc6G9PufTbeIeLX5jv7DkDZSxLCMv8koxlqshhLO4w9kIly4hfsumxqIrKFj84Yq8U9jHfLtT31W2IWnxX356oG/d9f2GRBqk6buOznKgbKyHiKaU3vujN4g8p1hhpzoInaMHGSuvZScRfFNplgPjwiL2Dg8om4UmwFJE4KOgNzMoSsgRYzDJkx7h4JqturEyAZQrZ8GiyiSxsxnt9Xwmw0ITMC8rW0PsuZUcgk2DBjuMFMub69lzV+bQkWEgg82zhZZYki1WV8yfC0kLU+U12aw160pmFxC8KYD0hpSE5ERaSkdFZMpn3Kw1jCbE8I9g0mbvYjrIpciIsJD9GLfym2PWTYSHJi3PCFxZRS9mWSDIsmHQyl91lqPBUejIsZgk6fL0XqjsxnFAfMxE/ja/QXZJisfx7axTuICZlF399tB3/xYUSYgEW21GwHYU3PiTFgicei7KFcaqk+lib9TF/BVOFkvL9Ox6RlSVjIMGYzNxlpvI+ocTGyhxzF/1ZcF9sv6nae6FKhsX1t0VVbYlTJTN/afEv6djKFsdAQixsASbnLKbQ7uidj4mdd1jAfaTwdvpE1mGGYnAh82QbtrVeu2BmIVNLYJVIL9OYxSzD4MJkpozeFfWyJPaS+LY727gwSSwrqfF/9SzC8/l9zvQwmaIRUz2L7/l8Wx+PoTNRYpgE+pg4pSJ+p+eW7lS4jHIWi5+ElG4/d5Eal1HOwsd8eaLvHfRrxh+ZVbPwjYqcs5B6FXEZuxPjl3CpZhHZfvhADx4gFH/5BtUsTWYWG4Uvaq6c+BMzxSzi8mj9kLWbi9//VdtFLPGtr1mYTTJkhv2f14OjIj/t8V1qWURADnk+f6kH7Zn3EyXAltls90rl1nj8NB63yqW6S5F2/Da1LPz8c9QeJQlmxP+x2ZyOnronM1E/0RP9MddvtfFOTVHKIpZfIvb1iDFw10GTuU8QrgnpkA8hOC/s0tmUsvDd1vVtcIIB6oUhrcJhezctwHn/fVzu1Zsu1bJZL40XXtFBGzql7aOdShb/VHqQF1NztN/uVt6/npgiZy969SXGYX+n71oWvQJq9g6pm0oWvFbViPh4uzXwijJTjk63QGaZaGhGh2bcnLFNweK2MCpZmsIsPXJFE7ulIaK1pSlMf9xzSeyy2sQ6di+6seaSz0e33eFUyCL2wpwJtnCz0CcIlKMzLLdNzOOtuZyTpLkbXUoTs7ZtXaxKIYtYrIT15pg4CDUI6o6aODRsmGAAcwgVojqa2EzbtnygOhZ+/IVoQgziQLga16OGP1wgmDBXAJtxu75bJ1Nol6C6BgFZFNYMIjWhvSLxDKKn5drQKIYnz9+2kDIWkb7kKEh5vZnhNlstMpqQLvg8tWTj4AWfL2y5Bq2KxQIzXprtpPAtCHu326VjI4KzcWA+/9a8dP3Fcou8VChJuba6HG4/UxpaxH5cN+lNsq4omoCGW84NVLBQEu4sNtq6hClu3pFg5+HAyXN3Ii6Btp5Nx89iuXdBAWf4tsN8ywKjBWR3yDqiNKcDF1v/N+Jm8UgcwbLrpJ4kB2/dHKud7nhV2xep5ZaMBJ6UxemX3Yvk0rSyVygOB/3BcFxyd5nBxMnCSSY9P5iW95vRB3Pl3b4/NhYLFDkJLgcbLkkqLhbTbFGSVY90Cj8RU7A2+Z1iYsG9GbQZCcC8NljiZZhjYbGWfULSKXmOavGq5fYq6fqlcbAQ/0AkbeeeuvRPvSXbw+JgMd0FdOBQTKf8GJZ8WdmDWfCUDGnzuohYor4J2lzdU65DWUgya8OiPxCIqeAOeVh8OpAFv0MHTQMTuDyPUnns5UsdxoKHEE3c4P3WhDtLKs9dOIgFkyxdPjst6rQp2CfaRoewkJEEPstbd3wi6MyVNPXn9hzA4sJQtLLESjhapuAs4CAWa4jkaGX5q8dp1fc/gMWF8njoPwwE9tKqvb4/i9mD0lTLf0iLqoM7W+gAljIMqqP4D8+BO8xp49YBLG++XUzxUCM7vQ4GDvKXJkTePo+J2wsejFE9zecUHMCCBwgWTWzVu5CP9iduShGM6aDxhe6nTBBfDUOpP/vqEBaz6SCbl/2BsJj6s9UOysdM8MS3svujze2TxHVgzm9Z7V6p17Z2O1SgSDHMkfc6u6JCSd0vloSOLNnUkSWbOrJkU0eWbOrIkk0dWbKpI0s2dWTJpo4s2dSRJZs6smxIfih2hSj04jcvbXw4eGtaLJWrv7+5bm/vHx+uXqS2XASv/Xt8uL5Yb2bl9vem/l7vDhMTy1ntNFCjUav++s9vy0U1/NLHAwi10/jTON1QNUWW83xYN7VfLxXBsvbSee1Bbqjx6ya/oVqGWPL505uLaBba0o+LoKkZZalS1c5PGcxHRWY5r9Vq540bAfrit5WzNGqyqlcps/y5eCG6PLutsf8t60qM5fzs+vr66vNvlYHe5P0PM5bG43VILzs3IlaWmz+G90vF+I+ZogECltolDcgV45KDNm6NEMv5mRexfe2OooKFNu++4bX/rCKzsLcaDwzU70WCZZ/mJ8DC2396v8lC2v7pWebml6EHi/H3xm/uOgt/MS+GkKyzVFgn8xxmg6Vyyaz2z9CD5dFjqUayAOPDM0xNE7t8y1J5aEjjYdZZjHtvGGlE+T7tZJ73Nz71YPnj+f5HpO8T1SSHyTgL9+5GZEz2m89Qo8bKDLFUKty5r75i+e11wRupj4VymD0SmPhZvDSlcv3BmpqX8rEwyz/vDVWJRc4t90ksY2bJ5z8/Px/vb29qLBuunn3FUrnfZJFUywJLg+qUt0hkj5EsjcyzyDr/G5pXRtnlXGa5kSbImWK5aVQfRXui/OX2NIgVPKj9vfX1e49JZews556I8/56fFnLmcMsH6z54Zh80IJS3Cx/zv4jOru6DK0bRfUxr4uxKUFmx8qosS6C5YUNpQ+ZZol6MSIfY2bUJLeUFZHzs6Gyxn/TmuVCTi31ZjE+G/KIqDNL5YWtXeQ1WYeRtcZSATyJPtOexXj58HqYv6SkJYtB94wuH0USHayXacdyent//+93vtpgCZuUPurHkqcJsJ/cV8+Ct2rIIqlRu5beqTHL6Xn1/kJud+ZYqnSefvMFi7xHVPv9cBF+m5H3XvgvIyzg5cpTdHOufF1fvlQ2JicV9trl7t+6prjOKny3rvXTFtEha2KyjucusqkjSzZ1ZMmmjizZ1P8py4yxoOQLIcUk8XA5ewH4Y1o2ys9rI0vUax6Afrhys34Sj85Ad0BYCOlqF1FbD7XAKLUiaPFIlPXNwR54FVjKHl6uVvwBzF5VTUM8NM/R0y7+Q//mBjCGgqugo2F4dXLSr8aERTwr10Y/fzJ7ElXnc/CVsBj86RM5pU9mVCTMy7jlnIVBWUY+WtrlUXYWvvPbPvVYjJwt/vCmF4wo30iL0BqMRURo6v86weBi0PA2ZzH6QUXqbgYqvmwny+0HKHeGYKmI2tgkADgjSwcai1YIFo125obPYtR9wpwNZ60m9h5mklVZFm4/OXKTmxJLEMu8gQbOh623UlY1GndnMOhJLIZJLEZBgqE44Yf/ZEtIBqFF6YwwC7FM6A3ayBZWkViMOkI/fzJzQrlXY5PFqPS1M40Nu9KZLYnFMN5srWhs2JkaX7GQEGBDXXoagp1RuPFrLIbRG9AnTmXbPF6Y7U7Xm77BQjRtdU/Sbu53mi2GhXpEu/8HOgJ+CrqcNOAAAAAASUVORK5CYII=';
?>

<div class="loader">
    <div class="center">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<div class="welcome-message">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Request New Appraisal</h2>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="alert alert-danger" hidden>
                    <strong>Whoops!</strong> Please fill in the following fields:
                </div>
            </div>
        </div>
    </div>
</div>
<div class="appraisal">
    <div class="container">
        <p>Please complete the fields below as accurately and completely as possible. * indicates a required field.</p>
        <form action="" method="post" name="vehicle_vin"
              id="<?php echo isset($result) ? 'editAppraisalValidation' : 'newAppraisalValidation'; ?>"
              enctype="multipart/form-data" autocomplete="on">
            <h3>Vehicle Information</h3>
            <div class="row">
                <div class="col">
                    <?php if (!isset($_GET['manual'])) { ?>
                        <p>VIN: <span><?php echo $vin; ?></span></p>
                        <p>Make: <span><?php echo $make; ?></span></p>
                        <p>Model: <span><?php echo $model; ?></span></p>
                        <p>Year: <span><?php echo $year; ?></span></p>
                        <p>Body Style: <span><?php echo $body; ?></span></p>
                        <p>Vehicle Type: <span><?php echo $vtype; ?></span></p>
                        <input type="hidden" name="make" class="valid" value="<?php echo $make; ?>"/>
                        <input type="hidden" name="model" class="valid" value="<?php echo $model; ?>"/>
                        <input type="hidden" name="vyear" class="valid" value="<?php echo $year; ?>"/>
                        <input type="hidden" name="body" class="valid" value="<?php echo $body; ?>"/>
                        <input type="hidden" name="vin" class="valid" value="<?php echo $vin; ?>"/>
                        <input type="hidden" name="vehicleType" class="valid" value="<?php echo $vtype; ?>"/>
                        <input type="hidden" name="appraisalID"
                               value="<?php echo isset($result) ? $result->appraisalID : ''; ?>"/>
                        <input type="hidden" id="status" name="status"
                               value="<?php echo isset($result) ? $result->status : ''; ?>"/>
                    <?php } else { ?>
                        <p>VIN: <input type="text" name="vin" class="valid" value="<?php echo $vin; ?>"/></p>
                        <p>*Make: <input type="text" name="make" class="valid" value="<?php echo $make; ?>"/></p>
                        <p>*Model: <input type="text" name="model" class="valid" value="<?php echo $model; ?>"/></p>
                        <p>*Year: <input type="number" name="vyear" class="valid" value="<?php echo $year; ?>"/></p>
                        <p>*Body Style: <input type="text" name="body" class="valid" value="<?php echo $body; ?>"/></p>
                        <p>*Vehicle Type: <input type="text" name="vehicleType" class="valid"
                                                 value="<?php echo $vtype; ?>"/></p>
                        <input type="hidden" name="appraisalID"
                               value="<?php echo isset($result) ? $result->appraisalID : ''; ?>"/>

                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>*Exterior Color</label>
                    <input type="text" value="<?php echo isset($result) ? $result->color : ''; ?>" class="valid"
                           name="color"
                           placeholder="Exterior Color"/>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>
                        *NADA Loan Value
                        <i class="fa fa-info" id="info-icon"></i>
                        <div id="tooltip" style="display: none;">
                            The NADA Loan Value is the estimated value of a particular vehicle based on National Automobile Dealers Association standards. This is the vehicle's estimated value before any considerations for damage or other devaluation factors.
                            <br/><br/>
                            <strong>This value is found on the Mechanic's or Storage Lien report (MSL) you received from the DMV.</strong> If you have not obtained an MSL, select "I have not obtained an MSL." If the NADA value is "Unknown," select "The NADA value is UNKNOWN."
                        </div>
                    </label>
                    <input type="text" step="any" class="valid priorNadaTradeInValue"
                           value="<?php echo isset($result) ? $result->priorNadaTradeInValue : '$0.00'; ?>"
                           name="priorNadaTradeInValue" id="priorNadaTradeInValue"/>
                    <input type="checkbox" name="priorNadaTradeInValueMSL"
                           id="priorNadaTradeInValueMSL" <?php echo isset($result) ? ($result->priorNadaTradeInValueMSL == 1 ? 'checked' : '') : ''; ?>
                           value="I have not obtained an MSL" class="valid"> <span>I have not obtained an MSL</span>
                    <input type="checkbox" name="priorNadaTradeInValueUnknown"
                           id="priorNadaTradeInValueUnknown" <?php echo isset($result) ? ($result->priorNadaTradeInValueUnknown == 1 ? 'checked' : '') : ''; ?>
                           value="The NADA value is UNKNOWN" class="valid"> <span>The NADA value is UNKNOWN</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>*Do you have keys to the vehicle?</label>
                    <input type="radio"
                           name="hasKeys" <?php echo isset($result) ? ($result->hasKeys == 1 ? 'checked' : '') : ''; ?>
                           value="1" class="valid"> <span> Yes</span>
                    <input type="radio"
                           name="hasKeys" <?php echo isset($result) ? ($result->hasKeys == 0 ? 'checked' : '') : ''; ?>
                           value="0" class="valid"> <span> No</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>*Vehicle Mileage</label>
                    <input type="checkbox"
                           id="odometerUnknown" <?php echo isset($result) ? ($result->odometer == 'Unknown' ? 'checked' : '') : ''; ?>
                           value="Unknown" class="valid"> <span>
                        Mileage
                        Unknown</span>
                    <input type="text" name="odometer" id="odometer" class="odometer valid"
                           value="<?php echo isset($result) ? ($result->odometer == 'Unknown' ? 'Unknown' : (isset($result->odometer) ? $result->odometer : '')) : ''; ?>"
                           placeholder="Vehicle Mileage"/>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>*Run Status</label>
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
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Additional Information</label>
                    <p class="text">What else can you tell us about this vehicle?</p>
                    <textarea name="additionalInformation"
                              placeholder="Any additional information if wish to provide?"><?php echo isset($result) ? $result->additionalInformation : ''; ?></textarea>
                </div>
            </div>
            <div class="row upload">
                <div class="col">
                    <label>*Upload Photo:</label>
                    <p style="color: #5C5C5C;">
                        Please upload full-color <strong>photos of all four sides of the vehicle and one of the interior</strong>. For exterior photos: Please do your best to show the entire side of the vehicle in one photo.
                    </p>
                    <p style="color: #5C5C5C;">
                        You can add additional photos for any specific damage or mechanical malfunctions. The more detail, the better. If you are using this web app on a device, you can take pictures by clicking the + icon and selecting "Take Photo" or upload them from your device's camera roll or your computer's hard drive.
                    </p>
                    <div class="row">
                        <div class="col part">
                            <label>
                                *Front
                                <i class="fa fa-info" id="photo-front"></i>
                                <div id="tooltip-photo-front" style="display: none;">
                                    <img src="<?php echo '../wp-content/plugins/aiaa/templates/images/front.png';?>" />
                                </div>
                            </label>
                            <input type="hidden" id="front"
                                   value="<?php echo isset($image_url['front_attachment_id']) ? $image_url['front_attachment_id'] : ''; ?>">
                            <input type="hidden" id="rear"
                                   value="<?php echo isset($image_url['rear_attachment_id']) ? $image_url['rear_attachment_id'] : ''; ?>">
                            <input type="hidden" id="driver"
                                   value="<?php echo isset($image_url['driver_attachment_id']) ? $image_url['driver_attachment_id'] : ''; ?>">
                            <input type="hidden" id="passenger"
                                   value="<?php echo isset($image_url['passenger_attachment_id']) ? $image_url['passenger_attachment_id'] : ''; ?>">
                            <input type="hidden" id="top"
                                   value="<?php echo isset($image_url['top_attachment_id']) ? $image_url['top_attachment_id'] : ''; ?>">
                            
                                   <div class="drop-area">
                                <div class="drop-icon <?php echo isset($image_url['front_attachment_id']) ? 'hidden' : ''; ?> ">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>

                                <input type="file" name="front" class="fileInput valid">
                                <span class="file-name" data-url="<?php echo isset($result) ? $frontImg : ''; ?>">
                                    <?php
                                    // Check if PDF file
                                    if(isset($image_url['front_attachment_id']) && pathinfo($image_url['front_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                        echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                                    } else {
                                        // Assuming it's an image file
                                        echo isset($image_url['front_attachment_id']) ? '<img src="' . $image_url['front_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="col part">
                            <label>
                                *Rear
                                <i class="fa fa-info" id="photo-rear"></i>
                                <div id="tooltip-photo-rear" style="display: none;">
                                    <img src="<?php echo '../wp-content/plugins/aiaa/templates/images/rear.png';?>" />
                                </div>
                            </label>
                            <div class="drop-area">
                                <div class="drop-icon <?php echo isset($image_url['rear_attachment_id']) ? 'hidden' : ''; ?> ">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                                <input type="file" name="rear" class="fileInput valid">
                                <span class="file-name"
                                      data-url="<?php echo isset($result) ? $rearImg : ''; ?>">
                                    <?php
                                    // Check if PDF file
                                    if(isset($image_url['rear_attachment_id']) && pathinfo($image_url['rear_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                        echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                                    } else {
                                        echo isset($image_url['rear_attachment_id']) ? '<img src="' . $image_url['rear_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="col part">
                            <label>
                                *Passenger
                                <i class="fa fa-info" id="photo-passenger"></i>
                                <div id="tooltip-photo-passenger" style="display: none;">
                                    <img src="<?php echo '../wp-content/plugins/aiaa/templates/images/passanger.png';?>" />
                                </div>
                            </label>
                            <div class="drop-area">
                                <div class="drop-icon <?php echo isset($image_url['passenger_attachment_id']) ? 'hidden' : ''; ?> ">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                                <input type="file" name="passenger" class="fileInput valid">
                                <span class="file-name" data-url="<?php echo isset($result) ? $passengerImg : ''; ?>">
                                    <?php
                                    // Check if PDF file
                                    if(isset($image_url['passenger_attachment_id']) && pathinfo($image_url['passenger_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                        echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                                    } else {
                                        // Assuming it's an image file
                                        echo isset($image_url['passenger_attachment_id']) ? '<img src="' . $image_url['passenger_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                     
                    </div>

                    <div class="row">
                    <div class="col part">
                            <label>
                                *Driver
                                <i class="fa fa-info" id="photo-driver"></i>
                                <div id="tooltip-photo-driver" style="display: none;">
                                    <img src="<?php echo '../wp-content/plugins/aiaa/templates/images/driver.png';?>" />
                                </div>
                            </label>
                            <div class="drop-area">
                                <div class="drop-icon <?php echo isset($image_url['driver_attachment_id']) ? 'hidden' : ''; ?> ">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                                <input type="file" name="driver" class="fileInput valid">
                                <span class="file-name" data-url="<?php echo isset($result) ? $driverImg : ''; ?>">
                                    <?php
                                    if(isset($image_url['driver_attachment_id']) && pathinfo($image_url['driver_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                        echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                                    } else {
                                        echo isset($image_url['driver_attachment_id']) ? '<img src="' . $image_url['driver_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                   
                        <div class="col part">
                            <label>
                            Interior 
                                <i class="fa fa-info" id="photo-interior"></i>
                                <div id="tooltip-photo-interior" style="display: none;">
                                A picture that displays the general condition of the interior, usually taken through the driver side front door. If there is any specific interior damage, please include a picture of it.
                                </div>
                            </label>
                            <div class="drop-area">
                                <div class="drop-icon <?php echo isset($image_url['interior_attachment_id']) ? 'hidden' : ''; ?> ">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                                <input type="file" name="interior" class="fileInput valid">
                                <span class="file-name" data-url="<?php echo isset($result) ? $interiorImg : ''; ?>">
                                    <?php
                                    // Check if PDF file
                                    if(isset($image_url['interior_attachment_id']) && pathinfo($image_url['interior_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                        echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                                    } else {
                                        // Assuming it's an image file
                                        echo isset($image_url['interior_attachment_id']) ? '<img src="' . $image_url['interior_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="col part">
                            <label>
                                Other
                                <i class="fa fa-info" id="photo-other"></i>
                                <div id="tooltip-photo-other" style="display: none;">
                                    A picture that displays the general condition of the interior, usually taken through the driver side front door. If there is any specific interior damage, please include a picture of it.
                                </div>
                            </label>
                            <div class="drop-area">
                                <div class="drop-icon <?php echo isset($image_url['other_attachment_id']) ? 'hidden' : ''; ?> ">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                                <input type="file" name="other" class="fileInput valid">
                                <span class="file-name" data-url="<?php echo isset($result) ? $otherImg : ''; ?>">
                                    <?php
                                    // Check if PDF file
                                    if(isset($image_url['other_attachment_id']) && pathinfo($image_url['other_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                        echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                                    } else {
                                        // Assuming it's an image file
                                        echo isset($image_url['other_attachment_id']) ? '<img src="' . $image_url['other_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    </div>


            </div>
            <div class="row">
                <div class="col">
                    <label>Documents</label>
                    <p class="text">Any additional documentation such as repair estimates, NADA documentation, etc.
                        may
                        be uploaded here. </p>
                </div>
            </div>
            <div class="row">
                <div class="col part">
                    <div class="drop-area side">
                        <div class="drop-icon <?php echo isset($image_url['nada-1_attachment_id']) ? 'hidden' : ''; ?> ">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        </div>
                        <input type="file" name="nada-1" class="fileInput valid" style="display: none;"/>
                        <span class="file-name" data-url="<?php echo isset($result) ? $nada1Img : ''; ?>">
                            <?php
                            // Check if PDF file
                            if(isset($image_url['nada-1_attachment_id']) && pathinfo($image_url['nada-1_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                            } else {
                                // Assuming it's an image file
                                echo isset($image_url['nada-1_attachment_id']) ? '<img src="' . $image_url['nada-1_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="drop-area side">
                        <div class="drop-icon <?php echo isset($image_url['nada-2_attachment_id']) ? 'hidden' : ''; ?> ">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </div>
                        <input type="file" name="nada-2" class="fileInput valid" style="display: none;"/>
                        <span class="file-name" data-url="<?php echo isset($result) ? $nada2Img : ''; ?>">
                            <?php
                            // Check if PDF file
                            if(isset($image_url['nada-2_attachment_id']) && pathinfo($image_url['nada-2_attachment_id'], PATHINFO_EXTENSION) === 'pdf') {
                                echo '<img src="'.$image_path.'" class="pdfIcon"> <i class="fa fa-times"></i>';
                            } else {
                                // Assuming it's an image file
                                echo isset($image_url['nada-2_attachment_id']) ? '<img src="' . $image_url['nada-2_attachment_id'] . '" class="uploadImage"> <i class="fa fa-times"></i>' : '';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <input type="submit"
                           name="submit_exit"
                           class="save <?php echo isset($result) ? 'data_submit' : 'new_apraisal'; ?>"
                           value="Save & Exit"
                           style="<?php echo isset($result) ? (($result->status === 'Pending') ? 'display:none' : '') : ''; ?>"/>

                    <input type="submit"
                           name="submit"
                           class="submit <?php echo isset($result) ? (($result->status == 'Pending') ? 'update' : '') : ''; ?>
                                            <?php echo isset($result) ? 'data_submit' : 'new_apraisal'; ?>"
                           value="<?php echo isset($result) ? (($result->status == 'Pending') ? 'Update Appraisal' : 'Submit Appraisal') : 'Submit Appraisal'; ?>"/>
                </div>
            </div>
        </form>
        <?php if ( isset($result) && ($result->status == 'Pending') ) { ?>
            <div class="row">
                <div class="col" style="text-align: center; margin-top:30px;">
                    <a href="<?php echo home_url('/my-account/'); ?>" class="btn-info" style="color: #1b5a8d;">Return to Dashboard</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>