<style>
    .wrap{
        width:50%;
    }
    .wrap input, .wrap select, .wrap textarea{
        width:100%;
        max-width: 100%;
        padding: 5px;
    }
    .wrap textarea{
        height: 120px;
    }
    .wrap label {
        display: block;
        margin-top: 1em;
        font-weight: bold;
    }
    .wrap .error {
        border: 1px solid red;
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        margin-bottom: 1rem;
        border-radius: 4px;
    }
    .wrap .success {
        border: 1px solid green;
        background-color: #d4edda;
        color: #155724;
        padding: 10px;
        margin-bottom: 1rem;
        border-radius: 4px;
    }
    .wrap input[type=checkbox]{
        width:auto;
    }
    .wrap input[type=checkbox]:checked{
        width:auto;
    }
</style>
<?php
global $wpdb;
if (current_user_can('administrator') || current_user_can('manager') || current_user_can('customer')) {
    $args = array(
        'role'    => '',
        'orderby' => 'user_nicename',
        'order'   => 'ASC'
    );
    $users = get_users($args);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" class="add_message_form" action="">
            <p>
                <label>To:</label>
                <select name="receiverID" class="user-id" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $user) : ?>
                        <option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_html($user->user_email); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label>VIN:</label>
                <select name="appraisalID" id="appraisal_id" required>
                    <option value="">Select Vin</option>
                </select>
            </p>
            <p>
                <label>Message:</label>
                <textarea name="content" placeholder="Enter your message here....." required></textarea>
            </p>
            <p>
                <label><input type="checkbox" class="user_email" name="emailTo" value="1"/>  Email copy to client</label>
            </p>
            <p>
                <?php submit_button('Add Message'); ?>
            </p>
        </form>
    </div>
    <?php
} else {
    echo '<h1>Sorry, you do not have permission to view this page.</h1>';
}
?>
