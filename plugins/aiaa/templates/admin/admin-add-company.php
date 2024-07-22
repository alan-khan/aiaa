<style>
    input[type="email"],
    input[type="password"],
    input[type="text"] {
        width: 100%; /* Ensure inputs take up full box width */
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
</style>
<?php
global $wpdb;
// Set the form variables
$phone = '';
$company = '';
$email = '';
$address = '';
$city = '';
$zip = '';
$error_message = [];


if (isset($_POST['submit']) && $_POST['action'] == 'register') {

    $phone = sanitize_text_field($_POST['phone']);
    $company = sanitize_text_field($_POST['company']);
    $email = sanitize_text_field($_POST['email']);
    $address = sanitize_text_field($_POST['address']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);

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

        }
        $phone = '';
        $company = '';
        $email = '';
        $address = '';
        $city = '';
        $zip = '';
        $error_message = [];
    }
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php if (isset($_POST['submit']) && sizeof($error_message) == 0) { ?>
        <div class="success">Congratulations, the new company has been successfully created.</div>
    <?php } else if (!isset($_POST['submit']) || (isset($_POST['submit']) && sizeof($error_message) > 0)) {

        if (sizeof($error_message) > 0) { ?>
            <div class="error">
                <p>Please correct the following error(s):</p>

                <?php

                echo '<ul>';

                foreach ($error_message as $message) {
                    echo '<li>' . $message . '</li>';
                }

                echo '</ul>';
                ?>
            </div>
        <?php }
    }
    ?>
    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <input name="action" type="hidden" value="register"/>
        <p>
            <label for="company">Company Name</label>
            <input type="text" name="company" id="company" value="<?php echo $company; ?>" required>
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
        </p>
        <p>
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" required>
        </p>
        <p>
            <label for="address">Address</label>
            <input type="text" name="address" id="address" value="<?php echo $address; ?>" required>
        </p>
        <p>
            <label for="city">City</label>
            <input type="text" name="city" id="city" value="<?php echo $city; ?>" required>
        </p>
        <p>
            <label for="state">State</label>
            <select name="state" id="state" required>
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
            <input type="text" name="zip" id="zip" value="<?php echo $zip; ?>" required>
        </p>

        <p>
            <?php submit_button('Add Company'); ?>
        </p>
    </form>


    </form>
</div>