<style>
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
if (isset($_GET['deleteMessage'])) {
    if (isset($_GET['id'])) {
        global $wpdb;
        $messageID = sanitize_text_field($_GET['id']);
        $result = $wpdb->delete('messages', array('messageID' => $messageID));

        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=aiaa_messages&error=delete'));
        } else {
            wp_redirect(admin_url('admin.php?page=aiaa_messages&success=delete'));
        }
    }
}

if (current_user_can('administrator') || current_user_can('manager')) {
    global $wpdb;
    $where = '';
    if( isset($_GET['search']) ) {
        $search = sanitize_text_field($_GET['search']);
        $where  .= "WHERE appraisals.vin LIKE '%$search%'";
    }
    $messages = $wpdb->get_results("
        SELECT messages.*, appraisals.vin
        FROM messages
        LEFT JOIN appraisals ON messages.appraisalID = appraisals.appraisalID
        $where
        ORDER BY messages.updatedAt DESC
    ");
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <div class="wrap">
        <h1>Messages
            <a href="<?php echo admin_url('admin.php?page=aiaa_add_message'); ?>" class="page-title-action">Compose
                New</a>
        </h1>
        <?php if (isset($_COOKIE['update_message'])): ?>
            <div class="success"><?php echo esc_html($_COOKIE['update_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_COOKIE['message'])): ?>
            <div class="success"><?php echo esc_html($_COOKIE['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?php echo "Record Deleted Successfully!"; ?></div>
        <?php endif; ?>
        <p>This page lists all Messages saved in the database.</p>

        <form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <input type="hidden" name="page" value="aiaa_messages">
            <input type="text" name="search" placeholder="Search by VIN"
                   value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <input type="submit" value="Search" class="button">
            <a href="<?php echo admin_url('admin.php?page=aiaa_messages'); ?>" class="button">Reset</a>
        </form>
        <br/>

        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th>Status</th>
                <th>From</th>
                <th>VIN</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($messages) {
                foreach ($messages as $msg) {
                    $user = get_userdata($msg->senderID);
                    $timestamp = strtotime($msg->createdAt);
                    $formattedDate = date('F d, Y', $timestamp);
                    ?>
                    <tr>
                        <td <?php if (($msg->status === 'Sent' || $msg->status === 'Replied' || $msg->status == 'New Message')  && ($msg->status !== 'Replied' || $msg->parentID == 0)) { echo 'style="font-weight: bold;"';} ?>><?php echo $msg->status; ?></td>
                        <td><?php echo $user->user_email; ?></td>
                        <td><?php echo $msg->vin; ?></td>

                        <td><?php echo $formattedDate; ?></td>
                        <td>
                        <?php  if ($msg->status === 'New Message'){ ?>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_edit_message&id=' . $msg->messageID . '&reply=reply'); ?>"
                               data-id="<?php echo $msg->messageID; ?>" class="button button-secondary reply">
                                  Reply
                            </a>
                        <?php }else{ ?>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_edit_message&id=' . $msg->messageID); ?>"
                               data-id="<?php echo $msg->messageID; ?>" class="button button-secondary open">
                                Open
                            </a>
                        <?php }?>
                            <a href="<?php echo admin_url('admin.php?page=aiaa_messages&deleteMessage&id=' . $msg->messageID) ?>"
                               class="button button-danger"
                               onclick="return window.confirm('Are you sure you want to delete the message <?php echo $msg->content; ?>?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    echo '<h1>Sorry, you do not have permission to view this page.</h1>';
}