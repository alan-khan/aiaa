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
</style>
<?php
if (current_user_can('administrator') || current_user_can('manager') || current_user_can('customer')) {
    global $wpdb;
    if (!isset($_GET['id']) && !isset($_POST['id'])) {
        wp_redirect(admin_url('admin.php?page=aiaa_messages'));
    }
    $id = @$_GET['id'];
    $company = $wpdb->get_row("
        SELECT ms.content, c.*, app.*, u.*
        FROM messages AS ms
        INNER JOIN company AS c ON c.companyID = ms.companyID
        INNER JOIN appraisals AS app ON c.companyID = app.companyID
        INNER JOIN user_company AS uc ON c.companyID = uc.companyID
        INNER JOIN {$wpdb->prefix}users AS u ON uc.userID = u.ID
        WHERE ms.messageID = '$id'
    ");
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="" class="edit_message_form">
        <input name="action" type="hidden" value="update-message"/>
        <input name="messageID" type="hidden" value="<?php echo $_GET['id']?>"/>
        <p>
            <label for="To">To: <?php echo !empty($company->user_email) ? $company->user_email : ''; ?></label>
        </p>
        <p>
            <label for="company">Company Name: <?php echo !empty($company->name) ? $company->name : ''; ?></label>
        </p>
        <p>
            <label for="VIN">VIN: <?php echo !empty($company->vin) ? $company->vin : ''; ?></label>
        </p>
        <?php if(@$_GET['reply']){?>
            <p>
                <label>Message: <?php echo !empty($company->content) ? $company->content : ''; ?></label>
                <input type="hidden" name="reply" value="reply"/>
            </p>
            <p>
                <label>Reply: </label>
                <textarea name="content" required placeholder="Enter Your Reply Here....."></textarea>
            </p>
            <p>
                <?php submit_button('Reply'); ?>
            </p>
        <?php }else{ ?>
            <p>
                <label>Message:</label>
                <textarea name="content" required placeholder="Enter Your Message Here....."><?php echo !empty($company->content) ? $company->content : ''; ?></textarea>
            </p>
            <p>
                <?php submit_button('Update Message'); ?>
            </p>
        <?php } ?>
    </form>
</div>
<?php
} else {
echo '<h1>Sorry, you do not have permission to view this page.</h1>';
}