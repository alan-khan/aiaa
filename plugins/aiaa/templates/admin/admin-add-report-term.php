<?php
if (current_user_can('administrator') || current_user_can('manager')) {
?>
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
</style>
<?php
global $wpdb;
// Set the form variables
$term = '';
$error_message = [];


if (isset($_POST['submit']) && $_POST['action'] == 'term') {

    $term = sanitize_text_field(trim($_POST['name']));

// Check if company and location already exists in the database
    $term_exists = $wpdb->get_results("
                SELECT * FROM catalog WHERE name = '$term'
        ");

    if (count($term_exists) > 0) {
        $error_message = ['Term already exists in the database.'];
    } else {
        // Insert Company
        $term_data = array(
            'name' => $term,
            'value' => $term,
        );

        $result_term = $wpdb->insert('catalog', $term_data);

        $term = '';

        // Check if company was created successfully
        if ($result_term === false) {
            $error_message = ['Error creating term.'];
        }
    }
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php if (isset($_POST['submit']) && sizeof($error_message) == 0) { ?>
        <div class="success">Congratulations, the new term has been successfully created.</div>
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
        <input name="action" type="hidden" value="term"/>
        <p>
            <label for="name">New Term</label>
            <textarea name="name" id="name" required><?php echo $term; ?></textarea>
        </p>

        <p>
            <?php submit_button('Add Term'); ?>
        </p>
    </form>


    </form>
</div>
<?php
} else {
    echo 'You do not have permission to view this page.';
}