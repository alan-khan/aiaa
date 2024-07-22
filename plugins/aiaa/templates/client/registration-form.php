<?php
// Set the form variables
$firstname = '';
$lastname = '';
$username = '';
$email = '';
$phone = '';
$company = '';
$address = '';
$city = '';
$zip = '';

// Error message collector
$error_message = [];

// PHP code to parse the form above
if (isset($_POST['submit']) && $_POST['action'] == 'register') {
    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);
    $username = sanitize_text_field($_POST['username']);
    $email = sanitize_text_field($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $phone = sanitize_text_field($_POST['phone']);
    $company = sanitize_text_field($_POST['company']);
    $address = sanitize_text_field($_POST['address']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);

    global $wpdb;

    // Check if company and location already exists in the database
    $company_exists = $wpdb->get_results("
                SELECT *
                FROM company as c
                INNER JOIN locations as l ON c.companyID = l.companyID
                WHERE c.name = '$company' AND l.city = '$city'
        ");

    if (count($company_exists) > 0) {
        $error_message = ['Company and location already exists in the database.'];
    } else {

        $user_id = wp_create_user($username, $password, $email);

        if (!is_wp_error($user_id)) {

            // Update user meta data
            wp_update_user(
                array(
                    'ID' => $user_id,
                    'first_name' => $firstname,
                    'last_name' => $lastname
                )
            );

            // Insert Company
            $company_data = array(
                'name' => $company,
                'phone' => $phone,
                'email' => $email,
            );

            $result_company = $wpdb->insert('company', $company_data);

            // Check if company was created successfully
            if ($result_company === false) {
                $error_message = ['Error creating company.'];
            } else {
                // Get the company ID
                $companyID = $wpdb->insert_id;

                // Insert Location
                $location_data = array(
                    'companyID' => $companyID,
                    'name' => $company,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                );

                $result_location = $wpdb->insert('locations', $location_data);

                // Insert user_company
                $user_company_data = array(
                    'userID' => $user_id,
                    'companyID' => $companyID,
                );

                $result_user_company = $wpdb->insert('user_company', $user_company_data);

            }

            // Send welcome email notification to the user
            $message = file_get_contents(AIAA_PLUGIN_DIR . 'templates/client/welcome-email.php');
            $headers = 'Content-Type: text/html; From: alxautoappraisers@gmail.com;';
            wp_mail($email, 'Welcome to AIAA', $message, $headers);

        } else {
            $error_message = [$user_id->get_error_message()];
        }
    }
}
?>

<!-- Registration form -->
<div class="box">

    <h2>Create a new account</h2>
    <hr />
    <!-- add aerror message dic with style -->
    <?php if (isset($_POST['submit']) && sizeof($error_message) == 0) {
        wp_redirect(home_url('/my-account'));
    } else if (!isset($_POST['submit']) || (isset($_POST['submit']) && sizeof($error_message) > 0)) { ?>

        <?php if (sizeof($error_message) > 0) { ?>
                <div class="error">
                    <p style="text-align: center;">Please correct the following error(s):</p>

                    <?php

                    echo '<ul>';

                    foreach ($error_message as $message) {
                        echo '<li>' . $message . '</li>';
                    }

                    echo '</ul>';
                    ?>
                </div>
        <?php } ?>


            <div class="alert alert-danger" hidden>
                <strong>Whoops!</strong> Please fill in the following fields:
                
            </div>
            <form method="post" id="registerValidation" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                <input name="action" type="hidden" value="register" />
                <p>
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" value="<?php echo $firstname; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" value="<?php echo $lastname; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo $username; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $email; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="aiaa-validate">
                </p>
                <p>
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="company">Company Name</label>
                    <input type="text" name="company" id="company" value="<?php echo $company; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?php echo $address; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" value="<?php echo $city; ?>" class="aiaa-validate">
                </p>
                <p>
                    <label for="state">State</label>
                    <select name="state" id="state" class="aiaa-validate" required>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA" selected>Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                    </select>
                </p>
                <p>
                    <label for="zip">Zip Code</label>
                    <input type="text" name="zip" id="zip" value="<?php echo $zip; ?>" class="aiaa-validate">
                </p>
                <p>
                    <input id="submit" type="submit" name="submit" value="Create Account">
                </p>
            </form>
    <?php } ?>

    <p style="text-align: center">
        or <br />
        <a href="<?php echo wp_login_url(); ?>" class="blue">Login</a>
    </p>
</div>

<style>
    h1 {
        display: none;
    }
</style>