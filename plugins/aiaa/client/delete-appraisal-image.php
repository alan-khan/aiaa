<?php
// Check if the user is logged in and has the required capabilities
if (is_user_logged_in() && (current_user_can('administrator') || current_user_can('manager') || current_user_can('customer'))) {
    global $wpdb;
    // Check if the 'id' parameter is set in the GET request
    if (isset ($_GET['deleteImage']) && isset($_GET['id']) && isset($_GET['filekey'])) {
        // Sanitize the 'id' parameter
        $id = sanitize_text_field($_GET['id']);
        $file_key = sanitize_text_field($_GET['filekey']);
        $appr = sanitize_text_field($_GET['appr']);

        delete_post_meta($appr, $file_key . '_attachment_id');

        $result = wp_delete_attachment($id, true);
        if ($result !== false) {
            echo 1;
        } else {
            echo 0;
        }

    }
}