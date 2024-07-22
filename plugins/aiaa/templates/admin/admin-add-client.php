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
if (current_user_can('administrator') || current_user_can('manager')) {

global $wpdb;
// Set the form variables
$firstname = '';
$lastname = '';
$username = '';
$email = '';
$company = '';
$error_message = [];


if (isset($_POST['submit']) && $_POST['action'] == 'register') {

    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);
    $username = sanitize_text_field($_POST['username']);
    $email = sanitize_text_field($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $companyID = sanitize_text_field($_POST['company']);

    $user_id = wp_create_user($username, $password, $email);

    if (!is_wp_error($user_id)) {

        // Insert user_company
        $user_company_data = array(
            'userID' => $user_id,
            'companyID' => $companyID,
        );

        $result_user_company = $wpdb->insert('user_company', $user_company_data);

        // Send welcome email notification to the user
        $message = file_get_contents(AIAA_PLUGIN_DIR . 'templates/client/welcome-email.php');
        wp_mail($email, 'Welcome to AIAA', $message, 'Content-Type: text/html');

        $firstname = '';
        $lastname = '';
        $username = '';
        $email = '';
        $company = '';
        $error_message = [];

    } else {
        $error_message = [$user_id->get_error_message()];
    }

}

$companies = $wpdb->get_results("
        SELECT c.*, l.*
        FROM company as c
        INNER JOIN locations as l ON c.companyID = l.companyID
        ORDER BY c.name ASC
        ");

$selectFields = '';
foreach ($companies as $company) {
    $selectFields .= '<option value="' . $company->companyID . '">' . $company->name . ' - ' . $company->city . '</option>';
}

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php if (isset($_POST['submit']) && sizeof($error_message) == 0) { ?>
        <div class="success">Congratulations, the new client account has been successfully created.</div>
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
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname" value="<?php echo $firstname; ?>" required>
        </p>
        <p>
            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname" value="<?php echo $lastname; ?>" required>
        </p>
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $username; ?>" required>
        </p>
        <p>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </p>
        <p>
            <label for="company">Company</label>
            <select name="company" id="company" required>
                <?php echo $selectFields; ?>
            </select>
        </p>

        <p>
            <?php submit_button('Add Client'); ?>
        </p>
    </form>


    </form>
</div>
<?php
} else {
    echo 'You do not have permission to view this page.';
}