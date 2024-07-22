<?php

if (is_user_logged_in() && (current_user_can('administrator') || current_user_can('manager') || current_user_can('customer'))) {
    global $wpdb;
    if (isset($_GET['id'])) {

        $id = sanitize_text_field($_GET['id']);

        // Check if user has permission to delete the appraisal
        $appraisal = $wpdb->get_row("SELECT * FROM appraisals WHERE appraisalID = $id");

        if (!$appraisal || !isset($appraisal->appraisalID)) {
            setcookie('message', 'Sorry, but it appears that the Appraisal you are trying to delete doesn\'t exist.', time() + 5, '/');
            wp_safe_redirect(home_url('/my-account?failed'));
        }

        $companyID = $appraisal->companyID;

        $company = $wpdb->get_row("SELECT * FROM user_company WHERE companyID = $companyID");
        $userID = get_current_user_id();

        if ($company->userID != $userID) {
            setcookie('message', 'You don\'t have permission to delete this appraisal.', time() + 5, '/');
            wp_safe_redirect(home_url('/my-account?failed'));
        }

        $wpdb->delete('appraisals', array('appraisalID' => $id));
        setcookie('message', 'You have successfully deleted the appraisal.', time() + 5, '/');
        wp_safe_redirect(home_url('/my-account?success'));

    } else {
        wp_safe_redirect(home_url('/my-account?failed'));
    }


}