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

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    wp_redirect(admin_url('admin.php?page=aiaa_companies&selectId'));
}

if (current_user_can('administrator') || current_user_can('manager')) {

global $wpdb;

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
                WHERE c.companyID = '" . sanitize_text_field($_POST['id']) . "'
        ");

    // Update Company
    $company_data = array(
        'name' => $company,
        'phone' => $phone,
        'email' => $email,
    );

    $result_company = $wpdb->update('company', $company_data, array('companyID' => sanitize_text_field($_POST['id'])));

    // Update Location
    $location_data = array(
        'name' => $company,
        'address' => $address,
        'city' => $city,
        'state' => $state,
        'zip' => $zip,
    );

    $result_location = $wpdb->update('locations', $location_data, array('locationID' => sanitize_text_field($_POST['locationID'])));


}
$companyID = sanitize_text_field($_GET['id']);
$company_result = $wpdb->get_results("
                SELECT *
                FROM company as c
                INNER JOIN locations as l ON c.companyID = l.companyID
                WHERE c.companyID = '$companyID'
        ");

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php if (isset($_POST['submit']) && sizeof($error_message) == 0) { ?>
        <div class="success">The Company has been updated successfully.</div>
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
        <input name="id" type="hidden" value="<?php echo $companyID; ?>"/>
        <input name="locationID" type="hidden" value="<?php echo $company_result[0]->locationID; ?>"/>
        <p>
            <label for="company">Company Name</label>
            <input type="text" name="company" id="company" value="<?php echo $company_result[0]->name; ?>" required>
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $company_result[0]->email; ?>" required>
        </p>
        <p>
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" value="<?php echo $company_result[0]->phone; ?>" required>
        </p>
        <p>
            <label for="address">Address</label>
            <input type="text" name="address" id="address" value="<?php echo $company_result[0]->address; ?>" required>
        </p>
        <p>
            <label for="city">City</label>
            <input type="text" name="city" id="city" value="<?php echo $company_result[0]->city; ?>" required>
        </p>
        <p>
            <label for="state">State</label>
            <select name="state" id="state" required>
                <option value="AL" <?php echo $company_result[0]->state === 'AL' ? 'selected' : ''; ?>>Alabama</option>
                <option value="AK" <?php echo $company_result[0]->state === 'AK' ? 'selected' : ''; ?>>Alaska</option>
                <option value="AZ" <?php echo $company_result[0]->state === 'AZ' ? 'selected' : ''; ?>>Arizona</option>
                <option value="AR" <?php echo $company_result[0]->state === 'AR' ? 'selected' : ''; ?>>Arkansas</option>
                <option value="CA" <?php echo $company_result[0]->state === 'CA' ? 'selected' : ''; ?>>California
                </option>
                <option value="CO" <?php echo $company_result[0]->state === 'CO' ? 'selected' : ''; ?>>Colorado</option>
                <option value="CT" <?php echo $company_result[0]->state === 'CT' ? 'selected' : ''; ?>>Connecticut
                </option>
                <option value="DE" <?php echo $company_result[0]->state === 'DE' ? 'selected' : ''; ?>>Delaware</option>
                <option value="FL" <?php echo $company_result[0]->state === 'FL' ? 'selected' : ''; ?>>Florida</option>
                <option value="GA" <?php echo $company_result[0]->state === 'GA' ? 'selected' : ''; ?>>Georgia</option>
                <option value="HI" <?php echo $company_result[0]->state === 'HI' ? 'selected' : ''; ?>>Hawaii</option>
                <option value="ID" <?php echo $company_result[0]->state === 'ID' ? 'selected' : ''; ?>>Idaho</option>
                <option value="IL" <?php echo $company_result[0]->state === 'IL' ? 'selected' : ''; ?>>Illinois</option>
                <option value="IN" <?php echo $company_result[0]->state === 'IN' ? 'selected' : ''; ?>>Indiana</option>
                <option value="IA" <?php echo $company_result[0]->state === 'IA' ? 'selected' : ''; ?>>Iowa</option>
                <option value="KS" <?php echo $company_result[0]->state === 'KS' ? 'selected' : ''; ?>>Kansas</option>
                <option value="KY" <?php echo $company_result[0]->state === 'KY' ? 'selected' : ''; ?>>Kentucky</option>
                <option value="LA" <?php echo $company_result[0]->state === 'LA' ? 'selected' : ''; ?>>Louisiana
                </option>
                <option value="ME" <?php echo $company_result[0]->state === 'ME' ? 'selected' : ''; ?>>Maine</option>
                <option value="MD" <?php echo $company_result[0]->state === 'MD' ? 'selected' : ''; ?>>Maryland</option>
                <option value="MA" <?php echo $company_result[0]->state === 'MA' ? 'selected' : ''; ?>>Massachusetts
                </option>
                <option value="MI" <?php echo $company_result[0]->state === 'MI' ? 'selected' : ''; ?>>Michigan</option>
                <option value="MN" <?php echo $company_result[0]->state === 'MN' ? 'selected' : ''; ?>>Minnesota
                </option>
                <option value="MS" <?php echo $company_result[0]->state === 'MS' ? 'selected' : ''; ?>>Mississippi
                </option>
                <option value="MO" <?php echo $company_result[0]->state === 'MO' ? 'selected' : ''; ?>>Missouri</option>
                <option value="MT" <?php echo $company_result[0]->state === 'MT' ? 'selected' : ''; ?>>Montana</option>
                <option value="NE" <?php echo $company_result[0]->state === 'NE' ? 'selected' : ''; ?>>Nebraska</option>
                <option value="NV" <?php echo $company_result[0]->state === 'NV' ? 'selected' : ''; ?>>Nevada</option>
                <option value="NH" <?php echo $company_result[0]->state === 'NH' ? 'selected' : ''; ?>>New Hampshire
                </option>
                <option value="NJ" <?php echo $company_result[0]->state === 'NJ' ? 'selected' : ''; ?>>New Jersey
                </option>
                <option value="NM" <?php echo $company_result[0]->state === 'NM' ? 'selected' : ''; ?>>New Mexico
                </option>
                <option value="NY" <?php echo $company_result[0]->state === 'NY' ? 'selected' : ''; ?>>New York</option>
                <option value="NC" <?php echo $company_result[0]->state === 'NC' ? 'selected' : ''; ?>>North Carolina
                </option>
                <option value="ND" <?php echo $company_result[0]->state === 'ND' ? 'selected' : ''; ?>>North Dakota
                </option>
                <option value="OH" <?php echo $company_result[0]->state === 'OH' ? 'selected' : ''; ?>>Ohio</option>
                <option value="OK" <?php echo $company_result[0]->state === 'OK' ? 'selected' : ''; ?>>Oklahoma</option>
                <option value="OR" <?php echo $company_result[0]->state === 'OR' ? 'selected' : ''; ?>>Oregon</option>
                <option value="PA" <?php echo $company_result[0]->state === 'PA' ? 'selected' : ''; ?>>Pennsylvania
                </option>
                <option value="RI" <?php echo $company_result[0]->state === 'RI' ? 'selected' : ''; ?>>Rhode Island
                </option>
                <option value="SC" <?php echo $company_result[0]->state === 'SC' ? 'selected' : ''; ?>>South Carolina
                </option>
                <option value="SD" <?php echo $company_result[0]->state === 'SD' ? 'selected' : ''; ?>>South Dakota
                </option>
                <option value="TN" <?php echo $company_result[0]->state === 'TN' ? 'selected' : ''; ?>>Tennessee
                </option>
                <option value="TX" <?php echo $company_result[0]->state === 'TX' ? 'selected' : ''; ?>>Texas</option>
                <option value="UT" <?php echo $company_result[0]->state === 'UT' ? 'selected' : ''; ?>>Utah</option>
                <option value="VT" <?php echo $company_result[0]->state === 'VT' ? 'selected' : ''; ?>>Vermont</option>
                <option value="VA" <?php echo $company_result[0]->state === 'VA' ? 'selected' : ''; ?>>Virginia</option>
                <option value="WA" <?php echo $company_result[0]->state === 'WA' ? 'selected' : ''; ?>>Washington
                </option>
                <option value="WV" <?php echo $company_result[0]->state === 'WV' ? 'selected' : ''; ?>>West Virginia
                </option>
                <option value="WI" <?php echo $company_result[0]->state === 'WI' ? 'selected' : ''; ?>>Wisconsin
                </option>
                <option value="WY" <?php echo $company_result[0]->state === 'WY' ? 'selected' : ''; ?>>Wyoming</option>
            </select>
        </p>
        <p>
            <label for="zip">Zip Code</label>
            <input type="text" name="zip" id="zip" value="<?php echo $company_result[0]->zip; ?>" required>
        </p>

        <p>
            <?php submit_button('Update Company'); ?>
        </p>
    </form>


    </form>
</div>
<?php
} else {
    echo 'You do not have permission to view this page.';
}